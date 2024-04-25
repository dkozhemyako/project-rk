<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentFrostyRT98LCharacteristicsHandler implements CommandsInterface
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
                            'text' => '📸 Фото обладнання RT98L',
                        ],
                        [ //кнопка
                            'text' => '🎬 Відео інструкція RT98L',
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

        $message = 'Характеристики Frosty RT98L.'.PHP_EOL.PHP_EOL;
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
        $message .= 'Розміри: 428х386х1105 мм';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
