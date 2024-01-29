<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;

class EquipmentRentalConditionsHandler implements CommandsInterface
{
    private array $replyMarkup =
        [
            'keyboard' =>
                [
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
        $message = 'Умови оренди обладнання:'.PHP_EOL.PHP_EOL;
        $message .= '- мінімальний термін оренди 2 місяці'.PHP_EOL;
        $message .= '- у вартість оренди входить доставка, встановлення та сервісне обслуговування обладнання'.PHP_EOL;
        $message .= '(тільки Київ, за межі міста - окремо прораховується доставка)'.PHP_EOL;
        $message .= '- при встановленні сплачується перший і останній місяць користування'.PHP_EOL;
        $message .= '- для припинення дії договору орендар повинен повідомити орендодавця за 30 днів'.PHP_EOL.PHP_EOL;

        $message .= 'Оберіть наступний крок 👇';

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);


        return $dto;
    }
}
