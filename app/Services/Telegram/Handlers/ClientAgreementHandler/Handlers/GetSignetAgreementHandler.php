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
use Throwable;

class GetSignetAgreementHandler implements FinalAgreementInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
    ) {
    }

    public function handle(
        FinalAgreementDTO $finalAgreementDTO,
        Closure $next
    ): FinalAgreementDTO {
        $agreementId = (int) $finalAgreementDTO->getCallback();
        $fileName = trim((string) $finalAgreementDTO->getFileName());

        /*
         * 1. Перевіряємо наявність завантаженого файлу.
         */
        if ($fileName === '') {
            $finalAgreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документа, повторіть спробу.'
            );

            return $finalAgreementDTO;
        }

        Log::info('Початок обробки підписаного договору клієнта.', [
            'agreementId' => $agreementId,
            'fileName' => $fileName,
        ]);

        /*
         * 2. Отримуємо інформацію про клієнта.
         */
        try {
            $clientInfo = $this->clientAgreementRepository
                ->getClientFilesById($agreementId);
        } catch (Throwable $exception) {
            Log::error('Не вдалося отримати інформацію про клієнта.', [
                'agreementId' => $agreementId,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Виникла помилка під час отримання інформації про договір. Повторіть спробу пізніше.'
            );

            return $finalAgreementDTO;
        }

        if (!$clientInfo) {
            Log::error('Інформацію про клієнта не знайдено.', [
                'agreementId' => $agreementId,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Не вдалося знайти інформацію про договір.'
            );

            return $finalAgreementDTO;
        }

        $clientName = trim((string) $clientInfo->getName());

        Log::info('Інформацію про клієнта отримано.', [
            'agreementId' => $agreementId,
            'clientName' => $clientName,
        ]);

        /*
         * 3. Визначаємо розширення файлу.
         *
         * pathinfo() повертає останнє розширення.
         * Наприклад:
         * document.docx.p7s => p7s
         * document.xml      => xml
         */
        $extension = strtolower(
            (string) pathinfo($fileName, PATHINFO_EXTENSION)
        );

        Log::info('Визначено розширення завантаженого файлу.', [
            'agreementId' => $agreementId,
            'fileName' => $fileName,
            'extension' => $extension,
        ]);

        /*
         * 4. Перевіряємо розширення.
         */
        $allowedExtensions = [
            'p7s',
            'asics',
            'asice',
        ];

        if (!in_array($extension, $allowedExtensions, true)) {
            $displayExtension = $extension !== ''
                ? '.' . $extension
                : 'відсутнє';

            Log::warning('Завантажено файл із недозволеним розширенням.', [
                'agreementId' => $agreementId,
                'fileName' => $fileName,
                'extension' => $displayExtension,
                'allowedExtensions' => $allowedExtensions,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Невірне розширення файлу. Дозволено лише файли з розширенням .p7s, .asics або .asice. Ви завантажили: '
                . $displayExtension
            );

            return $finalAgreementDTO;
        }

        /*
         * 5. Формуємо безпечне ім’я нового файлу.
         */
        $safeClientName = preg_replace(
            '/[^\p{L}\p{N}_-]+/u',
            '_',
            $clientName
        );

        $safeClientName = trim((string) $safeClientName, '_-');

        if ($safeClientName === '') {
            $safeClientName = 'client_' . $agreementId;
        }

        $newFileName = sprintf(
            'cli_signed_%s_%d.%s',
            $safeClientName,
            $agreementId,
            $extension
        );

        Log::info('Сформовано нове ім’я підписаного договору.', [
            'agreementId' => $agreementId,
            'oldFileName' => $fileName,
            'newFileName' => $newFileName,
        ]);

        /*
         * 6. Перевіряємо початковий файл і перейменовуємо його.
         */
        try {
            if (!Storage::disk('public')->exists($fileName)) {
                Log::error('Завантажений файл не знайдено на диску public.', [
                    'agreementId' => $agreementId,
                    'fileName' => $fileName,
                    'fullPath' => Storage::disk('public')->path($fileName),
                ]);

                $finalAgreementDTO->setMessage(
                    '❗️Не вдалося знайти завантажений документ. Завантажте його повторно.'
                );

                return $finalAgreementDTO;
            }

            /*
             * Видаляємо старий файл із таким самим іменем, якщо він існує.
             */
            if (
                $fileName !== $newFileName
                && Storage::disk('public')->exists($newFileName)
            ) {
                Storage::disk('public')->delete($newFileName);

                Log::warning('Попередній файл із новим іменем було видалено.', [
                    'agreementId' => $agreementId,
                    'newFileName' => $newFileName,
                ]);
            }

            if ($fileName !== $newFileName) {
                $moved = Storage::disk('public')->move(
                    $fileName,
                    $newFileName
                );

                if (!$moved) {
                    Log::error('Storage::move повернув false.', [
                        'agreementId' => $agreementId,
                        'oldFileName' => $fileName,
                        'newFileName' => $newFileName,
                    ]);

                    $finalAgreementDTO->setMessage(
                        '❗️Не вдалося обробити завантажений документ. Повторіть спробу.'
                    );

                    return $finalAgreementDTO;
                }
            }

            if (!Storage::disk('public')->exists($newFileName)) {
                Log::error('Файл відсутній після перейменування.', [
                    'agreementId' => $agreementId,
                    'oldFileName' => $fileName,
                    'newFileName' => $newFileName,
                ]);

                $finalAgreementDTO->setMessage(
                    '❗️Не вдалося зберегти завантажений документ. Повторіть спробу.'
                );

                return $finalAgreementDTO;
            }

            Log::info('Файл підписаного договору успішно збережено.', [
                'agreementId' => $agreementId,
                'oldFileName' => $fileName,
                'newFileName' => $newFileName,
            ]);
        } catch (Throwable $exception) {
            Log::error('Помилка під час роботи з файлом договору.', [
                'agreementId' => $agreementId,
                'oldFileName' => $fileName,
                'newFileName' => $newFileName,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Виникла помилка під час збереження документа. Повторіть спробу.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 7. Перевіряємо фізичний файл перед відправленням.
         */
        $filePath = Storage::disk('public')->path($newFileName);

        if (!is_file($filePath) || !is_readable($filePath)) {
            Log::error('Файл не існує або недоступний для читання.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'filePath' => $filePath,
                'exists' => file_exists($filePath),
                'isFile' => is_file($filePath),
                'isReadable' => is_readable($filePath),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ збережено, але не вдалося підготувати його до відправлення.'
            );

            return $finalAgreementDTO;
        }

        $fileSize = filesize($filePath);

        if ($fileSize === false || $fileSize === 0) {
            Log::error('Файл договору порожній або не вдалося визначити його розмір.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'filePath' => $filePath,
                'fileSize' => $fileSize,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Завантажений документ порожній. Завантажте коректний файл повторно.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 8. Оновлюємо ім’я підписаного договору в базі.
         */
        try {
            $this->clientAgreementRepository->updateSignedAgreement(
                $agreementId,
                $newFileName
            );

            Log::info('Ім’я підписаного договору оновлено в базі.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
            ]);
        } catch (Throwable $exception) {
            Log::error('Не вдалося оновити підписаний договір у базі.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ збережено, але виникла помилка під час оновлення даних договору.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 9. Отримуємо налаштування Telegram.
         */
        $adminId = config('messenger.telegram.admin_id');
        $telegramToken = config('messenger.telegram.token');

        if (empty($adminId) || empty($telegramToken)) {
            Log::error('Не задані налаштування Telegram.', [
                'agreementId' => $agreementId,
                'adminIdExists' => !empty($adminId),
                'telegramTokenExists' => !empty($telegramToken),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але сервіс відправлення тимчасово недоступний.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 10. Надсилаємо адміністратору текстове повідомлення.
         */
        $message = '💬 Вітаю, надсилаємо вам підписаний орендарем договір.'
            . PHP_EOL
            . PHP_EOL;

        $message .= 'Завдання №'
            . $agreementId
            . ' ('
            . $clientName
            . ')'
            . PHP_EOL;

        $message .= 'Перевірити підпис на отриманому файлі: '
            . 'https://ca.diia.gov.ua/verify'
            . PHP_EOL;

        $message .= 'Підпишіть та відправте договір клієнту.'
            . PHP_EOL;

        try {
            $dto = new MessageDTO(
                $message,
                $adminId
            );

            $dto->setReplyMarkup(
                $this->getAdminReplyMarkup($agreementId)
            );

            $this->messengerService->send($dto);

            Log::info('Текстове повідомлення адміністратору відправлено.', [
                'agreementId' => $agreementId,
                'adminId' => $adminId,
            ]);
        } catch (Throwable $exception) {
            Log::error('Не вдалося відправити текстове повідомлення адміністратору.', [
                'agreementId' => $agreementId,
                'adminId' => $adminId,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але виникла помилка під час повідомлення орендодавця.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 11. Надсилаємо адміністратору файл через Telegram Bot API.
         */
        $mimeType = mime_content_type($filePath);

        if ($mimeType === false || $mimeType === '') {
            $mimeType = 'application/octet-stream';
        }

        $arrayQuery = [
            'chat_id' => $adminId,
            'caption' => 'Підписаний договір клієнтом',
            'document' => new \CURLFile(
                $filePath,
                $mimeType,
                basename($filePath)
            ),
        ];

        $telegramUrl = sprintf(
            'https://api.telegram.org/bot%s/sendDocument',
            $telegramToken
        );

        Log::info('Початок відправлення файлу через Telegram Bot API.', [
            'agreementId' => $agreementId,
            'adminId' => $adminId,
            'newFileName' => $newFileName,
            'filePath' => $filePath,
            'fileSize' => $fileSize,
            'mimeType' => $mimeType,
        ]);

        $ch = curl_init();

        if ($ch === false) {
            Log::error('Не вдалося ініціалізувати cURL.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але виникла технічна помилка під час його відправлення.'
            );

            return $finalAgreementDTO;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $telegramUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $arrayQuery,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 120,
        ]);

        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        curl_close($ch);

        Log::info('Отримано відповідь Telegram Bot API.', [
            'agreementId' => $agreementId,
            'newFileName' => $newFileName,
            'httpCode' => $httpCode,
            'curlErrno' => $curlErrno,
            'curlError' => $curlError,
            'contentType' => $contentType,
            'totalTime' => $totalTime,
            'response' => $response,
        ]);

        /*
         * 12. Перевіряємо транспортну помилку cURL.
         */
        if ($response === false || $curlErrno !== 0) {
            Log::error('cURL не зміг відправити документ у Telegram.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'httpCode' => $httpCode,
                'curlErrno' => $curlErrno,
                'curlError' => $curlError,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але виникла технічна помилка під час його відправлення орендодавцю.'
            );

            return $finalAgreementDTO;
        }

        /*
         * 13. Перевіряємо JSON-відповідь Telegram.
         */
        $responseData = json_decode($response, true);

        if (!is_array($responseData)) {
            Log::error('Telegram повернув некоректну JSON-відповідь.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'httpCode' => $httpCode,
                'jsonError' => json_last_error_msg(),
                'response' => $response,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але Telegram повернув некоректну відповідь.'
            );

            return $finalAgreementDTO;
        }

        if (
            $httpCode !== 200
            || ($responseData['ok'] ?? false) !== true
        ) {
            Log::error('Telegram Bot API не прийняв документ.', [
                'agreementId' => $agreementId,
                'newFileName' => $newFileName,
                'httpCode' => $httpCode,
                'telegramErrorCode' => $responseData['error_code'] ?? null,
                'telegramDescription' => $responseData['description'] ?? null,
                'response' => $responseData,
            ]);

            $finalAgreementDTO->setMessage(
                '❗️Документ отримано, але не вдалося відправити його орендодавцю. Помилка Telegram: '
                . ($responseData['description'] ?? 'невідома помилка')
            );

            return $finalAgreementDTO;
        }

        Log::info('Документ успішно відправлено адміністратору через Telegram.', [
            'agreementId' => $agreementId,
            'newFileName' => $newFileName,
            'telegramMessageId' => $responseData['result']['message_id'] ?? null,
            'telegramChatId' => $responseData['result']['chat']['id'] ?? null,
        ]);

        /*
         * 14. Повідомляємо клієнту про успішне відправлення лише після
         * успішної відповіді Telegram API.
         */
        $finalAgreementDTO->setMessage(
            '💬 Дякуємо, договір відправлено орендодавцю. Чекайте на дзвінок за вказаним контактним номером, а також на підписаний договір.'
        );

        $finalAgreementDTO->setReplyMarkup(
            $this->replyMarkup()
        );

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
                        'callback_data' => (string) $agreementId,
                    ],
                ],
            ],
        ];
    }
}
