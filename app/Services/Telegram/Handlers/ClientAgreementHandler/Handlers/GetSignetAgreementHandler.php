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
        if ($finalAgreementDTO->getFileName() === '') {
            $finalAgreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу'
            );
            return $finalAgreementDTO;
        }

        // 1. Отримуємо ім’я файлу
        $fileName = $finalAgreementDTO->getFileName();
        Log::info('Отримано ім’я файлу:', ['fileName' => $fileName]);

        // 2. Отримуємо інформацію про клієнта
        $clientInfo = $this->clientAgreementRepository->getClientFilesById($finalAgreementDTO->getCallback());
        $clientName = $clientInfo->getName();
        Log::info('Отримано ім’я клієнта:', ['clientName' => $clientName]);

        // 3. Розділяємо ім’я файлу по крапках
        $parts = explode('.', $fileName);
        Log::info('Частини імені файлу після explode:', ['parts' => $parts]);

        // 4. Визначаємо розширення
        if (count($parts) > 1) {
            array_shift($parts); // видаляємо першу частину
            $extension = strtolower('.' . implode('.', $parts)); // додаємо strtolower
            Log::info('Сформоване складене розширення:', ['extension' => $extension]);
        } else {
            $extension = '';
            Log::info('Файл не має розширення.');
        }

        // 🔒 Перевірка дозволених розширень
        $allowedExtensions = ['.p7s', '.asics', '.asice'];
        if (!in_array($extension, $allowedExtensions)) {
            $finalAgreementDTO->setMessage(
                '❗️Невірне розширення файлу. Дозволено лише файли з розширенням .p7s або .asics або .asice. Ви завантажили: ' . $extension
            );
            return $finalAgreementDTO;
        }

        // 5. Формуємо нове ім’я файлу
        $newFileName = 'cli_signed_' . $clientName . $extension;
        Log::info('Сформоване нове ім’я файлу:', ['newFileName' => $newFileName]);

        // 6. Перевіряємо, чи існує файл
        if (Storage::disk('public')->exists($fileName)) {
            Log::info('Файл існує. Виконується перейменування...');
            Storage::disk('public')->move($fileName, $newFileName);
            Log::info('Файл успішно перейменовано.', [
                'old' => $fileName,
                'new' => $newFileName
            ]);
        } else {
            Log::error('Файл не знайдено для перейменування:', ['fileName' => $fileName]);
        }

        // 7. Оновлюємо файл у базі
        $this->clientAgreementRepository->updateSignedAgreement($finalAgreementDTO->getCallback(), $newFileName);

        // 8. Надсилаємо повідомлення адміну
        $message = '💬 Вітаю, надсилаємо вам підписаний договір орендарем..' . PHP_EOL . PHP_EOL;
        $message .= 'Завдання №' . $finalAgreementDTO->getCallback() . ' (' . $clientInfo->getName() . ')' . PHP_EOL;
        $message .= 'Перевірити підпис на отриманому файлі - https://ca.diia.gov.ua/verify' . PHP_EOL;
        $message .= 'Підпишіть та відправте договір клієнту.' . PHP_EOL;

        $dto = new MessageDTO(
            $message,
            config('messenger.telegram.admin_id'),
        );
        $dto->setReplyMarkup($this->getAdminReplyMarkup($finalAgreementDTO->getCallback()));
        $this->messengerService->send($dto);

        // 9. Надсилаємо сам файл через curl
        $arrayQuery = array(
            'chat_id' => config('messenger.telegram.admin_id'),
            'caption' => 'Підписаний договір клієнтом',
            'document' => curl_file_create(storage_path('app/public/' . $newFileName))
        );
        $ch = curl_init('https://api.telegram.org/bot' . config('messenger.telegram.token') . '/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);

        // 10. Завершення обробки
        $finalAgreementDTO->setMessage('💬 Дякуємо, договір відправлено орендодавцю. Чекайте на дзвінок по вказаному контактному номеру, а також підписаний договір.');
        $finalAgreementDTO->setReplyMarkup($this->replyMarkup());

        return $next($finalAgreementDTO);
    }

    private function replyMarkup(): array
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

    private function getAdminReplyMarkup(int $agreementId): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => TelegramCommandEnum::adminSignedAgreement->value,
                        'callback_data' => $agreementId,
                    ],
                ],
            ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
