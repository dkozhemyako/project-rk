<?php

namespace App\Services\Telegram\Handlers\CoffeeGrinderHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class CoffeeGrinderFiorenzatoF64Handler implements CommandsInterface
{

    public function __construct(
        protected Client $client,
    ){}

    /**
     * @var array
     */
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '📌 Характеристики кавомолок',
                        ],
                        [ //кнопка
                            'text' => '📝 Умови оренди',
                        ],
                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '↗ Повернутись назад',
                        ],
                        [ //кнопка
                            'text' => '⬆ На головну',
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];

    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $this->client->post(
            config('messenger.telegram.url_media_group'),
            [
                'json' => [
                    'chat_id' => $senderId,
                    'media' => [
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FiorenzatoF64/FiorenzatoF64-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FiorenzatoF64/FiorenzatoF64-2.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FiorenzatoF64/FiorenzatoF64-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FiorenzatoF64/FiorenzatoF64-4.jpg'],
                    ],
                ],
            ]
        );


        $message = 'Кавомолка професійна прямого помолу.'.PHP_EOL;
        $message .= 'Відправляємо Вaм фото обладнання для ознайомлення.';
        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
