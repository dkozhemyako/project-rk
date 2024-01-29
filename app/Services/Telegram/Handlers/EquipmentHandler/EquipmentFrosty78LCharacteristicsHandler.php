<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentFrosty78LCharacteristicsHandler implements CommandsInterface
{

    public function __construct(
        protected Client $client,
    ){}
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '📸 Фото обладнання',
                        ],
                        [ //кнопка
                            'text' => '🎬 Відео інструкція',
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '⬅ Повернутись назад',
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

        $message = 'Характеристики Frosty 78L.'.PHP_EOL.PHP_EOL;
        $message .= 'Вага: 30 кг.'.PHP_EOL;
        $message .= 'Матеріал корпусу: пластик чорний/білий'.PHP_EOL;
        $message .= 'Напруга живлення: 220В'.PHP_EOL;
        $message .= 'Обсяг,л: 78'.PHP_EOL;
        $message .= 'Охолоджуюча можливість: динамічне'.PHP_EOL;
        $message .= 'Параметри: 428х386х960'.PHP_EOL;
        $message .= 'Температурний режим,°C: 0...+12'.PHP_EOL;
        $message .= 'Тип товару: вітрина холодильна'.PHP_EOL;
        $message .= 'Тип установки: настільна';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
