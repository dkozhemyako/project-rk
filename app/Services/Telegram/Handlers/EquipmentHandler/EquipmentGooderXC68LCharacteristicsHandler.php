<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXC68LCharacteristicsHandler implements CommandsInterface
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
                            'text' => '📸 Фото обладнання XC68L',
                        ],
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

        $message = 'Характеристики Gooder XC68L.'.PHP_EOL.PHP_EOL;
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
