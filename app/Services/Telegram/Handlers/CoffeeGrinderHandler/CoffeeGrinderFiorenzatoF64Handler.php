<?php

namespace App\Services\Telegram\Handlers\CoffeeGrinderHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class CoffeeGrinderFiorenzatoF64Handler implements CommandsInterface
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
            [ ['text' => '↗ Повернутись назад'], ['text' => '⬆ На головну'] ],
        ],
        'one_time_keyboard' => true,
        'resize_keyboard'   => true,
    ];

    /**
     * Обробка команди: надсилає групу фото та характеристик
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
        for ($i = 1; $i <= 4; $i++) {
            $media[] = [
                'type'  => 'photo',
                'media' => "attach://f64-{$i}",
            ];
        }

        // Готуємо multipart-форму для завантаження файлів
        $multipart = [
            [ 'name' => 'chat_id', 'contents' => (string) $senderId ],
            [ 'name' => 'media',   'contents' => json_encode($media) ],
        ];
        for ($i = 1; $i <= 4; $i++) {
            $multipart[] = [
                'name'     => "f64-{$i}",
                'contents' => fopen(public_path("FiorenzatoF64/FiorenzatoF64-{$i}.jpg"), 'r'),
                'filename' => "FiorenzatoF64-{$i}.jpg",
            ];
        }

        // Надсилаємо групу фото без зовнішніх GET
        $this->client->post(
            config('messenger.telegram.url_media_group'),
            ['multipart' => $multipart]
        );

        // Формуємо текстове повідомлення
        $text  = 'Кавомолка професійна прямого помолу.' . PHP_EOL;
        $text .= 'Відправляємо Вам також характеристики для ознайомлення.' . PHP_EOL . PHP_EOL;
        $text .= 'Технічні характеристики кавомолки Fiorenzato F64' . PHP_EOL . PHP_EOL;
        $text .= '- Діаметр жорен, мм: 64' . PHP_EOL;
        $text .= '- Частота обертання, об/хв: 1350' . PHP_EOL;
        $text .= '- Місткість контейнера для зерен, кг: 1,5' . PHP_EOL;
        $text .= '- Матеріал корпусу: алюмінієвий сплав' . PHP_EOL . PHP_EOL;
        $text .= '- Споживання енергії, Вт: 350' . PHP_EOL;
        $text .= '- Колір: сірий металік / чорний / білий / перловий / червоний' . PHP_EOL;
        $text .= '- Маса, кг: 14' . PHP_EOL;
        $text .= '- Габарити (ШхВхГ), мм: 230x615x270' . PHP_EOL;

        $dto = new MessageDTO($text, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
