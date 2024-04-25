<?php

namespace App\Services\Telegram\Handlers\CoffeeMachineHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentCassadioDieciCharacteristicsHandler implements CommandsInterface
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
                            'text' => '📷 Фото зразків обладнання',
                        ],
                        [ //кнопка
                            'text' => '📝 Умови оренди'
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '↖ Повернутись назад',
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

        $message = 'Характеристики обладнання:'.PHP_EOL.PHP_EOL;
        $message .= 'Кількість груп: 2'.PHP_EOL;
        $message .= 'Управління: автомат/напів-автомат'.PHP_EOL;
        $message .= 'Об’єм бойлера (л): 10.5'.PHP_EOL;
        $message .= 'Маса (кг): 76'.PHP_EOL.PHP_EOL;
        $message .= 'Потужність (Вт): 4000'.PHP_EOL;
        $message .= 'Напруга (V): 230'.PHP_EOL;
        $message .= 'Габарити (ШхВхГ) мм : 755x540x575'.PHP_EOL;

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
