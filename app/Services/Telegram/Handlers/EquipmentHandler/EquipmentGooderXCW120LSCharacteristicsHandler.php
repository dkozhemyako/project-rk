<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXCW120LSCharacteristicsHandler implements CommandsInterface
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
                            'text' => '📸 Фото обладнання XCW-120LS',
                        ],
                        [ //кнопка
                            'text' => '🎬 Відео інструкція XCW-120LS',
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

        $message = 'Характеристики Gooder XCW-120LS.'.PHP_EOL.PHP_EOL;
        $message .= 'Обсяг,л: 120'.PHP_EOL;

        $message .= 'Потужність, кВт — 0,21'.PHP_EOL;
        $message .= 'Напруга, В — 220'.PHP_EOL;
        $message .= 'Розмір, мм — 702х568х686'.PHP_EOL;
        $message .= 'Вага, кг — 57'.PHP_EOL;
        $message .= 'Колір - чорний'.PHP_EOL;
        $message .= 'Кількість рівнів - 3'.PHP_EOL;
        $message .= 'Матеріал корпусу - нержавіюча сталь, пластик'.PHP_EOL;
        $message .= 'Матеріал поличок - хромована сталь'.PHP_EOL;
        $message .= 'Полиці регулюються по висоті'.PHP_EOL;
        $message .= 'Переднє скло - гнуте'.PHP_EOL;
        $message .= 'Температурний режим 0...+12 °С'.PHP_EOL;
        $message .= 'Цифрова панель управління'.PHP_EOL;
        $message .= 'Подвійні склопакети'.PHP_EOL;
        $message .= 'Динамічне охолодження'.PHP_EOL;
        $message .= 'Автоматичне розморожування'.PHP_EOL;
        $message .= 'Верхня LED підсвітка'.PHP_EOL;
        $message .= 'Вартість оренди 2000грн/міс';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
