<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers;



use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\FinalAgreementInterface;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetSignetAgreementHandler implements FinalAgreementInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
    ){}


    public function handle(FinalAgreementDTO $finalAgreementDTO , Closure $next): FinalAgreementDTO
    {
        if ($finalAgreementDTO->getFileName() === ''){
            $finalAgreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу'
            );
            return $finalAgreementDTO;
        }

        $zip = explode('.',$finalAgreementDTO->getFileName());

        if (trim(end($zip)) != 'p7s'){
            $finalAgreementDTO->setMessage(
                '🤦 Помилка відправки файлу. Необхідно завантажити файл з підписом, розширення .p7s'
            );
            return $finalAgreementDTO;
        }

        $clientInfo = $this->clientAgreementRepository->getClientFilesById($finalAgreementDTO->getCallback());

        $newFileName = 'cli_signed_' . $clientInfo->getName() . '.docx.p7s';
        Storage::disk('public')->move($finalAgreementDTO->getFileName(), $newFileName);

        $this->clientAgreementRepository->updateSignedAgreement($finalAgreementDTO->getCallback(), $newFileName);

        $message = '💬 Вітаю, надсилаємо вам підписаний договір орендарем..'.PHP_EOL.PHP_EOL;
        $message .= 'Завдання №'.$finalAgreementDTO->getCallback().'('.$clientInfo->getName().')'.PHP_EOL;
        $message .= 'Перевірити підпис на отриманому файлі - https://ca.diia.gov.ua/verify'.PHP_EOL;
        $message .= 'Всі файли клієнта збережені у відповідну теку.'.PHP_EOL;

        $dto = new MessageDTO(
            $message,
            config('messenger.telegram.admin_id'),
        );
        $this->messengerService->send($dto);

        $arrayQuery = array(
            'chat_id' => config('messenger.telegram.admin_id'),
            'caption' => 'Підписаний договір клієнтом',
            'document' => curl_file_create(storage_path('app/public/'.$newFileName))
        );
        $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);

        $finalAgreementDTO->setMessage('💬 Дякуємо, договір відправлено орендодавцю. Чекайте на дзвінок по вказаному контактному номеру');
        $finalAgreementDTO->setReplyMarkup($this->replyMarkup());

        return $next($finalAgreementDTO);


    }

    private function replyMarkup(): array
    {
        return
            [
                'keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::returnMain->value,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }
}
