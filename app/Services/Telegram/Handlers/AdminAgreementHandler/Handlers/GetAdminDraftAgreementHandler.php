<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EquipmentConditionEnum;
use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class GetAdminDraftAgreementHandler implements AdminAgreementInterface
{
    public const AGR_DRAFT_ADMIN = '_ADMIN_DRAFT_AGREEMENT';
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
    ){}



    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_DRAFT_ADMIN;

        if (Redis::get($key) == 'checked'){
            Log::info(Redis::get($key));
            $adminAgreementDTO->setMessage(
                '🤦 Адмін частина по заповненню драфту договору цього завдання завершена. При необхідності зверніться до адміністратора.
                (AdminAgreementHandler)'
            );
            return $adminAgreementDTO;
        }


        if ($adminAgreementDTO->getFileName() === ''){
            $adminAgreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу'
            );
            return $adminAgreementDTO;
        }

        $zip = explode('.',$adminAgreementDTO->getFileName());
        if (trim($zip[1]) != 'docx'){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка відправки файлу. Необхідно завантажити файл з розширенням .docx'
            );
            return $adminAgreementDTO;
        }

        $clientInfo = $this->clientAgreementRepository->getClientFilesById($adminAgreementDTO->getCallback());

        $newFileName = 'adm_final_' . $clientInfo->getName() . '.docx';
        Storage::disk('public')->move($adminAgreementDTO->getFileName(), $newFileName);

        $this->adminAgreementRepository->updateDraftAgreement($adminAgreementDTO->getCallback(), $newFileName);

        $result = $this->clientAgreementRepository->getClientTelegramIdById($adminAgreementDTO->getCallback());
        $clientId = $result['0']->telegram_id; //не вариант, разобратся с коллекциями

        $message = '💬 Вітаю, надсилаємо вам екземпляр договору створений орендодавцем.'.PHP_EOL;
        $message .= 'Будьласка ознайомтесь з договором, перевірте дані. '.PHP_EOL.PHP_EOL;
        $message .= 'Після перевірки підтвердіть, що все добре 👇'.PHP_EOL;

        $dto = new MessageDTO(
            $message,
            $clientId,
        );
        $dto->setReplyMarkup($this->replyMarkupTrue($adminAgreementDTO->getCallback()));
        $this->messengerService->send($dto);

        $dtoFalse = new MessageDTO(
            'Або зробіть уточнення при необхідності внесення правок 👇',
            $clientId,
        );
        $dtoFalse->setReplyMarkup($this->replyMarkupFalse($adminAgreementDTO->getCallback()));
        $this->messengerService->send($dtoFalse);

        //

        $arrayQuery = array(
            'chat_id' => $clientId,
            'caption' => 'Договір для ознайомлення',
            'document' => curl_file_create(storage_path('app/public/'.$newFileName))
        );
        $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);

        $adminAgreementDTO->setMessage('💬 Договір відправлено клієнту для ознайомлення та перевірки, очікуйте на відповідь.');

        Redis::set($key, 'checked', 'EX', 260000);
        return $adminAgreementDTO;


    }

    private function replyMarkupTrue(int $agreementId): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::clientCheckAgreementTrue->value,
                                'callback_data' => $agreementId,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }
    private function replyMarkupFalse(int $agreementId): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::clientCheckAgreementFalse->value,
                                'callback_data' => $agreementId,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }
}
