<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\EqTypeClientEnum;
use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AgreementTypeHandler implements AgreementInterface
{
    public const AGR_STAGE_AGR_TYPE = '_AGR_TYPE';
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '👨‍💻 Фізична особа-підприємець',
                        ],
                        [ //кнопка
                            'text' => '👨‍💼 Фізична особa',
                        ],

                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_AGR_TYPE;

        if (Redis::exists($key) == true){

            return $next($agreementDTO);
        }

        $checkValue = EqTypeClientEnum::tryFrom($agreementDTO->getMessage());

        if (is_null($checkValue) == true) {

            $agreementDTO->setMessage('🤦 Помилка вводу. Оберіть значення з меню 👇');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }
        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);

        $message = 'Для формування договору, нам необхідно отримати інформацію про орендаря.' . PHP_EOL;
        $message .= 'Оберіть організаційно-правову форму 👇';

        $agreementDTO->setMessage($message);
        $agreementDTO->setReplyMarkup($this->replyMarkup);
        return $agreementDTO;
    }

    private function replyMarkup(): array
    {
        return
            [
                'keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => '👨‍💻 Фізична особа-підприємець',
                            ],
                            [ //кнопка
                                'text' => '👨‍💼 Фізична особа',
                            ],

                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }


}
