<?php

namespace App\Services\Telegram\Handlers\CoffeeMachineHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;

class CoffeeMachineHandler implements CommandsInterface
{
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
                        /*
                        [ //кнопка
                            'text' => '📌 Характеристики кавоварок',
                        ],
                        */
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
        $message .= '- загальні характеристики обладнання'.PHP_EOL;
        $message .= '- отримати фото зразків обладнання'.PHP_EOL;
        $message .= '- ознайомитися з основними умовами оренди'.PHP_EOL;

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);


        return $dto;
    }
}
