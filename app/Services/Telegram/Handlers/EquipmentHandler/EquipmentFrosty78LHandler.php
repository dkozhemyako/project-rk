<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentFrosty78LHandler implements CommandsInterface
{

    public function __construct(
        protected Client $client,
    ){}
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        /*
                        [ //кнопка
                            'text' => '📌 Характеристики 78L',
                        ],
                        */
                        [ //кнопка
                            'text' => '🎬 Відео інструкція 78L',
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '⬆ На головну',
                        ],
                        [ //кнопка
                            'text' => '⬅ Повернутись назад',
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
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-9.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-2.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-4.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-5.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-6.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-7.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'Frosty75L/Frosty78L-8.jpg' ],
                    ],
                ],
            ]
        );


        $message = 'Відправляємо Вaм також характеристики для ознайомлення.'.PHP_EOL;
        $message .= '👇'.PHP_EOL.PHP_EOL;

        $message .= 'Характеристики Frosty 78L.'.PHP_EOL.PHP_EOL;
        $message .= 'Вага: 30 кг.'.PHP_EOL;
        $message .= 'Матеріал корпусу: пластик чорний/білий'.PHP_EOL;
        $message .= 'Напруга живлення: 220В'.PHP_EOL;
        $message .= 'Обсяг,л: 78'.PHP_EOL;
        $message .= 'Охолоджуюча можливість: динамічне'.PHP_EOL;
        $message .= 'Параметри: 428х386х960'.PHP_EOL;
        $message .= 'Температурний режим,°C: 0...+12'.PHP_EOL;
        $message .= 'Тип товару: вітрина холодильна'.PHP_EOL;
        $message .= 'Тип установки: настільна'.PHP_EOL;
        $message .= 'Вартість оренди 1300грн/міс';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
