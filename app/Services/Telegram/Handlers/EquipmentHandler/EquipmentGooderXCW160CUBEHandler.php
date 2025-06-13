<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXCW160CUBEHandler implements CommandsInterface
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
                            'text' => '📌 Характеристики XCW-160 CUBE',
                        ],
                        */
                        [ //кнопка
                            'text' => '🎬 Відео інструкція XCW-160 CUBE',
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
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW160CUBE/GooderXCW160CUBE-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW160CUBE/GooderXCW160CUBE-2.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW160CUBE/GooderXCW160CUBE-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'GooderXCW160CUBE/GooderXCW160CUBE-4.jpg'],
                    ],
                ],
            ]
        );


        $message = 'Відправляємо Вaм також характеристики для ознайомлення.'.PHP_EOL;
        $message .= 'Детальніше 👇'.PHP_EOL.PHP_EOL;

        $message .= 'Характеристики Gooder XCW-160 CUBE.'.PHP_EOL.PHP_EOL;
        $message .= 'Обсяг,л: 172'.PHP_EOL;

        $message .= 'Вага 66 кг'.PHP_EOL;
        $message .= 'Вага в упаковці 70 кг'.PHP_EOL;
        $message .= 'Додатково: Склопакет, нержавіюча сталь, регульовані полички'.PHP_EOL;
        $message .= 'Кількість полок 2'.PHP_EOL;
        $message .= 'Климатичний клас 4'.PHP_EOL;
        $message .= 'Матеріал полиць нержавійка'.PHP_EOL;
        $message .= 'Освітлення	LED 2шт'.PHP_EOL;
        $message .= 'Розмір верхньої полиці	810х335 мм'.PHP_EOL;
        $message .= 'Розмір нижньої полиці	810х365 мм'.PHP_EOL;
        $message .= 'Розміри (ДхШхВ) 888х568х686 мм'.PHP_EOL;
        $message .= 'Розміри в упаковці (ДхШхВ)	951х627х735 мм'.PHP_EOL;
        $message .= 'Розміри внутрішні	500х840х400 мм'.PHP_EOL;
        $message .= 'Система охолодження Динамічна'.PHP_EOL;
        $message .= 'Споживання електроенергії 4,25 кВт/д ( 0,17 кВт/г)'.PHP_EOL;
        $message .= 'Темп.режим	0...+12°C'.PHP_EOL;
        $message .= 'Температура навколишнього середовища max.32 °C'.PHP_EOL;
        $message .= 'Тип дверей	Розсувні'.PHP_EOL;
        $message .= 'Хлодоген R600a'.PHP_EOL;
        $message .= 'Вартість оренди 2400грн/міс';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
