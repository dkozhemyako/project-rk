<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXCW100LHandler implements CommandsInterface
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
                            'text' => '📌 Характеристики XCW-100L',
                        ],
                        */
                        [ //кнопка
                            'text' => '🎬 Відео інструкція XCW-100L',
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
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW100L/GooderXCW100L-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW100L/GooderXCW100L-2.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW100L/GooderXCW100L-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW100L/GooderXCW100L-4.jpg'],
                    ],
                ],
            ]
        );


        $message = 'Відправляємо Вaм також характеристики для ознайомлення.'.PHP_EOL;
        $message .= 'Детальніше 👇'.PHP_EOL.PHP_EOL;

        $message .= 'Характеристики Gooder XCW-100L.'.PHP_EOL.PHP_EOL;
        $message .= 'Обсяг,л: 100'.PHP_EOL;

        $message .= 'Колір - чорний'.PHP_EOL;
        $message .= 'Кількість рівнів - 3'.PHP_EOL;
        $message .= 'Матеріал корпусу - пластик'.PHP_EOL;
        $message .= 'Полиці регулюються по висоті'.PHP_EOL;
        $message .= 'Переднє скло - гнуте'.PHP_EOL;
        $message .= 'Температурний режим 0...+6 °С'.PHP_EOL;
        $message .= 'Цифрова панель управління'.PHP_EOL;
        $message .= 'Подвійні склопакети'.PHP_EOL;
        $message .= 'Динамічне охолодження'.PHP_EOL;
        $message .= 'Автоматичне розморожування'.PHP_EOL;
        $message .= 'Підсвічування LED з двох сторін'.PHP_EOL;
        $message .= 'Вартість оренди 1600грн/міс';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
