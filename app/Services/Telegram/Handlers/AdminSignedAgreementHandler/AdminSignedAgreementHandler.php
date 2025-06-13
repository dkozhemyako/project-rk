<?php

namespace App\Services\Telegram\Handlers\AdminSignedAgreementHandler;

use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AdminSignedAgreementHandler implements CommandsInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
    ){}

    public const ADM_SIGNED_STAGE = '_ADM_SIGNED_STAGE';

    /**
     * @throws GuzzleException
     */
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $key = $senderId . self::ADM_SIGNED_STAGE;

        $dto = new AdminAgreementDTO(
            $callback,
            $message,
            $senderId,
            $fileName
        );

        if ($dto->getFileName() === '') {
            Redis::set($key, $callback, 'EX', 260000);
            return new MessageDTO(
                '🤦 Ви не завантажили жодного документу, повторіть спробу. Необхідно завантажити підписаний файл.',
                $dto->getSenderId()
            );
        }

        $fileName = $dto->getFileName(); // виправлено подвійний $$
        $parts = explode('.', $fileName);

        if (count($parts) > 1) {
            array_shift($parts); // видаляємо все до першої крапки
            $extension = strtolower('.' . implode('.', $parts));
        } else {
            $extension = '';
        }

        // 🔒 Перевірка розширення
        $allowedExtensions = ['.p7s', '.asics'];
        if (!in_array($extension, $allowedExtensions)) {
            return new MessageDTO(
                '❗️Невірне розширення файлу. Дозволено лише файли з розширенням .p7s або .asics. Ви завантажили: ' . $extension,
                $dto->getSenderId()
            );
        }

        if (Redis::exists($key)) {
            $callback = Redis::get($key);
            $clientInfo = $this->clientAgreementRepository->getClientFilesById($callback);
            $clientName = $clientInfo->getName();
            $newFileName = 'adm_signed_' . $clientName . $extension;

            Storage::disk('public')->move($fileName, $newFileName);

            $this->adminAgreementRepository->updateSignedAgreement($callback, $newFileName);

            $result = $this->clientAgreementRepository->getClientTelegramIdById($callback);
            $clientId = $result[0]->telegram_id;

            $message = '💬 Вітаю, надсилаємо вам підписаний договір орендодавцем.' . PHP_EOL;
            $message .= 'Для того щоб перевірити підписаний договір перейдіть за посиланням - https://ca.diia.gov.ua/verify' . PHP_EOL;

            $ClientDto = new MessageDTO(
                $message,
                $clientId,
            );
            $ClientDto->setReplyMarkup($this->replyMarkupMain());
            $this->messengerService->send($ClientDto);

            $arrayQuery = array(
                'chat_id' => $clientId,
                'caption' => 'Підписаний договір',
                'document' => curl_file_create(storage_path('app/public/' . $newFileName))
            );
            $ch = curl_init('https://api.telegram.org/bot' . config('messenger.telegram.token') . '/sendDocument');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            curl_close($ch);

            Redis::del($key);
            return new MessageDTO(
                '💬 Договір відправлено клієнту.',
                $dto->getSenderId()
            );
        }

        return new MessageDTO(
            '💬 Сталася помилка, зверніться до адміністратора.',
            $dto->getSenderId()
        );
    }

    private function replyMarkup(int $agreementId): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => TelegramCommandEnum::clientAgreement->value,
                        'callback_data' => $agreementId,
                    ],
                ],
            ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }

    private function replyMarkupMain(): array
    {
        return [
            'keyboard' => [
                [
                    [
                        'text' => TelegramCommandEnum::returnMain->value,
                    ],
                ],
            ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
