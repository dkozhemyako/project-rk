<?php

namespace App\Services\Telegram\Handlers\EquipmentHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentGooderXC68LVideoHandler implements CommandsInterface
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
                            'text' => '📌 Характеристики XC68L',
                        ],
                        [ //кнопка
                            'text' => '📸 Фото обладнання XC68L',
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


        $message = "<a href='https://youtu.be/qI3_oN3Hwp8'>Посилання на відео інструкцію</a>";
        $dto = new MessageDTO($message, $senderId);
        $dto->setParseMode('HTML');
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
