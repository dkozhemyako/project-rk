<?php

namespace App\Services\Telegram\Handlers\CoffeeMachineHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class CoffeeMachineCassadioDieciHandler implements CommandsInterface
{
    public function __construct(
        protected Client $client,
    ) {}

    /**
     * @var array Структура клавіатури
     */
    private array $replyMarkup = [
        'keyboard' => [
            [ ['text' => '📝 Умови оренди'] ],
            [ ['text' => '⬆ На головну'], ['text' => '↖ Повернутись назад'] ],
        ],
        'one_time_keyboard' => true,
        'resize_keyboard'   => true,
    ];

    /**
     * Обробка команди: надсилає групу фото та характеристики
     *
     * @param string $message
     * @param int    $senderId
     * @param string $fileName
     * @param int    $callback
     * @param int    $mediaGroupId
     * @return MessageDTO
     */
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        // Формуємо масив media із посиланнями attach://
        $media = [];
        for ($i = 1; $i <= 7; $i++) {
            $media[] = [
                'type'  => 'photo',
                'media' => "attach://cd-{$i}",
            ];
        }

        // Готуємо multipart-форму для завантаження файлів
        $multipart = [
            [ 'name' => 'chat_id', 'contents' => (string) $senderId ],
            [ 'name' => 'media',   'contents' => json_encode($media) ],
        ];
        for ($i = 1; $i <= 7; $i++) {
            $multipart[] = [
                'name'     => "cd-{$i}",
                'contents' => fopen(public_path("CassadioDieci/CassadioDieci-{$i}.jpg"), 'r'),
                'filename' => "CassadioDieci-{$i}.jpg",
            ];
        }

        // Надсилаємо групу фото без зовнішніх GET
        $this->client->post(
            config('messenger.telegram.url_media_group'),
            ['multipart' => $multipart]
        );

        // Формуємо текстове повідомлення
        $text  = 'Кавоварка професійна, двопостова.' . PHP_EOL;
        $text .= 'Відправляємо Вам фото обладнання для ознайомлення.' . PHP_EOL . PHP_EOL;
        $text .= 'Характеристики обладнання:' . PHP_EOL . PHP_EOL;
        $text .= 'Кількість груп: 2' . PHP_EOL;
        $text .= 'Управління: автомат/напів-автомат' . PHP_EOL;
        $text .= 'Об’єм бойлера (л): 10.5' . PHP_EOL;
        $text .= 'Маса (кг): 76' . PHP_EOL . PHP_EOL;
        $text .= 'Потужність (Вт): 4000' . PHP_EOL;
        $text .= 'Напруга (V): 230' . PHP_EOL;
        $text .= 'Габарити (ШхВхГ) мм : 755x540x575' . PHP_EOL;

        $dto = new MessageDTO($text, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
