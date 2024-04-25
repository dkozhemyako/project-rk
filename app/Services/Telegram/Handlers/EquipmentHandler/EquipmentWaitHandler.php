<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;

class EquipmentWaitHandler implements CommandsInterface
{
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '™ Frosty 78L',
                        ],
                        [ //кнопка
                            'text' => '™ Frosty RT98L',
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '⬜ Очікується',
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

        $message = 'Будь ласка оберіть доступне обладнання 👇';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);


        return $dto;
    }
}
