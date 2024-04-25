<?php

namespace App\Services\Telegram\Handlers\CoffeeGrinderHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use GuzzleHttp\Client;

class EquipmentFiorenzatoF64CharacteristicsHandler implements CommandsInterface
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
                            'text' => '📸 Фото зразків обладнання',
                        ],
                        [ //кнопка
                            'text' => '📝 Умови оренди'
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => '↗ Повернутись назад',
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

        $message = 'Технічні характеристики кавомолки Fiorenzato F64'.PHP_EOL.PHP_EOL;
        $message .= '- Діаметр жорен, мм: 64'.PHP_EOL;
        $message .= '- Частота обертання, об / хв 1350'.PHP_EOL;
        $message .= '- Місткість контейнера для зерен, Кг: 1,5'.PHP_EOL;
        $message .= '- Матеріал корпусу: алюмінієвий сплав'.PHP_EOL.PHP_EOL;
        $message .= '- Споживання енергії, Вт: 350'.PHP_EOL;
        $message .= '- Колір: сірий металік / чорний / білий / перловий / червоний'.PHP_EOL;
        $message .= '- Маса, Кг: 14'.PHP_EOL;
        $message .= '- Габарити (ШхВхГ), мм: 230x615x270'.PHP_EOL;

        $dto = new MessageDTO($message, $senderId);
        $dto->setReplyMarkup($this->replyMarkup);

        return $dto;
    }
}
