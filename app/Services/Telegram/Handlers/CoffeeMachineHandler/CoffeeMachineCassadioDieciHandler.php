<?php

namespace App\Services\Telegram\Handlers\CoffeeMachineHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class CoffeeMachineCassadioDieciHandler implements CommandsInterface
{

    public function __construct(
        protected Client $client,
    ){}

    /**
     * @var array
     */
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '📝 Умови оренди'
                        ],
                        [ //кнопка
                            'text' => '📌 Характеристики кавоварок'
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
        $this->client->post(
            config('messenger.telegram.url_media_group'),
            [
                'json' => [
                    'chat_id' => $senderId,
                    'media' => [
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-1.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-2.jpg' ],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-3.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-4.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-5.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-6.jpg'],
                        ['type' => 'photo', 'media' => config('messenger.telegram.ngrok').'/'. 'CassadioDieci/CassadioDieci-7.jpg'],
                    ],
                ],
            ]
        );

        $message = 'Кавоварка професійна, двопостова.'.PHP_EOL;
        $message = 'Відправляємо Вaм фото обладнання для ознайомлення.';
        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
