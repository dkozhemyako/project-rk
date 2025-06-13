<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramGetFileService
{
    public function __construct(
        protected Client $client,
    ) {}

    /**
     * Отримує файл з Telegram і зберігає його в storage/app/public
     *
     * @throws GuzzleException
     */
    public function getFile(array $data): string
    {
        if (array_key_exists('message', $data)) {
            if (array_key_exists('document', $data['message'])) {
                $fileId = $data['message']['document']['file_id'];
                $originalFileName = $data['message']['document']['file_name'] ?? null;

                $filePath = $this->getPath($fileId);
                return $this->copyFile($filePath, $originalFileName);
            }

            if (array_key_exists('photo', $data['message'])) {
                $lastKey = end($data['message']['photo']);
                $fileId = $lastKey['file_id'];
                $filePath = $this->getPath($fileId);

                return $this->copyFile($filePath, null);
            }
        }

        return '';
    }

    /**
     * Копіює файл за шляхом Telegram API і зберігає його з коректним ім’ям
     */
    private function copyFile(string $filePath, ?string $originalFileName = null): string
    {
        $fileFromTelegram = config('messenger.telegram.url_get_file') . '/' . $filePath;

        // Формуємо нове ім’я файлу
        if ($originalFileName) {
            $newFileName = time() . '_' . $originalFileName;
        } else {
            // fallback якщо нема імені — беремо з шляху
            $pathParts = explode('/', $filePath);
            $fileOnly = end($pathParts);
            $newFileName = time() . '_' . $fileOnly;
        }

        // Логування дій
        Log::info('Отримано шлях до файлу Telegram:', ['filePath' => $filePath]);
        Log::info('Формується нове ім’я файлу:', ['newFileName' => $newFileName]);

        // Завантажуємо і зберігаємо
        $fileContent = file_get_contents($fileFromTelegram);
        Storage::disk('public')->put($newFileName, $fileContent);

        Log::info('Файл успішно збережено в storage/app/public:', ['fileName' => $newFileName]);

        return $newFileName;
    }

    /**
     * Отримує шлях до файлу через Telegram API
     *
     * @throws GuzzleException
     */
    private function getPath(string $fileId): string
    {
        $response = $this->client->post(
            config('messenger.telegram.url_get_path'),
            [
                'json' => ['file_id' => $fileId],
            ]
        );

        $resultArray = json_decode($response->getBody()->getContents(), true);

        return $resultArray['result']['file_path'];
    }
}
