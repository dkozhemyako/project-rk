<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentFrostyRT98LHandler implements CommandsInterface
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
                            'text' => '📌 Характеристики RT98L',
                        ],
                        */
                        [ //кнопка
                            'text' => '🎬 Відео інструкція RT98L',
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
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FrostyRT98L/FrostyRT98L-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FrostyRT98L/FrostyRT98L-2.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'FrostyRT98L/FrostyRT98L-3.jpg'],
                    ],
                ],
            ]
        );


        $message = 'Відправляємо Вaм також характеристики для ознайомлення.'.PHP_EOL;
        $message .= 'Детальніше 👇'.PHP_EOL.PHP_EOL;

        $message .= 'Характеристики Frosty RT98L.'.PHP_EOL.PHP_EOL;
        $message .= 'Робочі температури: +2 ... + 6 C'.PHP_EOL;
        $message .= 'Корисний обєм: 98 л.'.PHP_EOL;
        $message .= 'Виконання дверей: прозора'.PHP_EOL;
        $message .= 'Тип охолодження: динамічний.'.PHP_EOL.PHP_EOL;
        $message .= 'Тип оттайкі: автоматична.'.PHP_EOL;
        $message .= 'Кількість полиць: 4 шт. 5 рівнів викладки.'.PHP_EOL;
        $message .= 'Цифровий термостат.'.PHP_EOL;
        $message .= 'Електронний контролер.'.PHP_EOL;
        $message .= 'Наявність замку: є'.PHP_EOL;
        $message .= 'Підсвічування: світлодіодна'.PHP_EOL.PHP_EOL;
        $message .= 'Корпус: білий пластик'.PHP_EOL;
        $message .= 'Потужність: 0,18 кВт.'.PHP_EOL;
        $message .= 'Вага: 38 кг.'.PHP_EOL;
        $message .= 'Розміри: 428х386х1105 мм'.PHP_EOL;
        $message .= 'Вартість оренди 1500грн/міс';
        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
