<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;

class EquipmentHandler implements CommandsInterface
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
                            'text' => '⬜ Очікується',
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '📝 Умови оренди',
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
        $message = 'Тут ви зможете дізнатись:'.PHP_EOL;
        $message .= '- характеристики обладннання'.PHP_EOL;
        $message .= '- отримати фото '.PHP_EOL;
        $message .= '- переглянути відео інструкцію по користуванню '.PHP_EOL;
        $message .= '- ознайомитись з основними умовами оренди '.PHP_EOL.PHP_EOL;

        $message .= 'Оберіть обладнання 👇';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);


        return $dto;
    }
}
