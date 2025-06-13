<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXC68LHandler implements CommandsInterface
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
                            'text' => '📌 Характеристики XC68L',
                        ],
                        */
                        [ //кнопка
                            'text' => '🎬 Відео інструкція XC68L',
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
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXC68L/GooderXC68L-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXC68L/GooderXC68L-2.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXC68L/GooderXC68L-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXC68L/GooderXC68L-4.jpg'],
                    ],
                ],
            ]
        );


        $message = 'Відправляємо Вaм також характеристики для ознайомлення.'.PHP_EOL;
        $message .= 'Детальніше 👇'.PHP_EOL.PHP_EOL;

        $message .= 'Характеристики Gooder XC68L.'.PHP_EOL.PHP_EOL;
        $message .= 'Обсяг,л: 68'.PHP_EOL;
        $message .= 'Розмір, мм — 454х408х895'.PHP_EOL;
        $message .= 'Колір - чорний'.PHP_EOL;
        $message .= 'Кількість рівнів - 4'.PHP_EOL;
        $message .= 'Матеріал корпусу - пластик'.PHP_EOL;
        $message .= 'Розмір поличок 355х305 мм'.PHP_EOL;
        $message .= 'Полиці регулюються по висоті'.PHP_EOL;
        $message .= 'Температурний режим 0...+6 °С'.PHP_EOL;
        $message .= 'Цифрова панель управління'.PHP_EOL;
        $message .= 'Подвійні склопакети з 4-х сторін'.PHP_EOL;
        $message .= 'Динамічне охолодження'.PHP_EOL;
        $message .= 'Автоматичне розморожування'.PHP_EOL;
        $message .= 'Вартість оренди 1300грн/міс';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
