<?php

namespace App\Services\Telegram\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;


class StartHandler implements CommandsInterface
{
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $result = 'Вітаю, я створений щоб допомагати.'. PHP_EOL;
        $result .= 'Зі мною Ви зможете:'. PHP_EOL;
        $result .= '- обрати необхідне Вам обладнання'. PHP_EOL;
        $result .= '- дізнатися про вартість його оренди'. PHP_EOL;
        $result .= '- ознайомитись із технічними параметрами'. PHP_EOL;
        $result .= '- сформувати договір оренди'. PHP_EOL;

        $dto = new MessageDTO(
            $result,
            $senderId,
        );

        $markup =
        [
          'keyboard' =>
              [
                  [ //строка
                      [ //кнопка
                          'text' => '❄ Холодильні вітрини',
                      ],
                      [ //кнопка
                          'text' => '📋 Сформувати договір',
                      ],

                  ],
                  [ //строка
                      [ //кнопка
                          'text' => '☕ Кавоварки',
                      ],
                      [ //кнопка
                          'text' => '☕ Кавомолки',
                      ],

                  ],
              ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];

        $dto->setReplyMarkup($markup);
        return $dto;
    }
}
