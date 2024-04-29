<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressStreetHandler implements AgreementInterface
{
    public const AGR_STAGE_EQUIP_STREET = '_EQUIP_STREET';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_STREET;

        if (Redis::get($agreementDTO->getSenderId()) == 19
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . EquipmentAddressHouseHandler::AGR_STAGE_EQUIP_HOUSE,
            );
            Redis::set($agreementDTO->getSenderId(), 18);

            $agreementDTO->setMessage(
                '💬 Вкажіть номер приміщення в якому буде розташоване обладнання.'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        $arrayStreet = explode('.', $agreementDTO->getMessage());

        if (count($arrayStreet) < 2){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити тип та назву вулиці у форматі (тип.Назва вулиці), наприклад вул.Нова або просп.Райдужний (тип, крапка, назва вулиці). Будь ласка введіть дані повторно.'
            );
            return $agreementDTO;
        }
        $first = mb_substr(trim($arrayStreet[1]), 0, 1);
        if ($first === mb_strtolower($first)){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити назву вулиці з великої букви. Будь ласка введіть дані повторно.'
            );
            return $agreementDTO;
        }

        $first = mb_substr($arrayStreet[0], 0, 1);
        if ($first != mb_strtolower($first)){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити тип вулиці з маленької букви. Будь ласка введіть дані повторно.'

            );
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 18);
        $agreementDTO->setMessage(
            '💬 Вкажіть номер приміщення в якому буде розташоване обладнання.'
        );
        $agreementDTO->setReplyMarkup($this->replyMarkup());
        return $agreementDTO;
    }

    private function replyMarkup(): array
    {
        return [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => TelegramCommandEnum::returnMain->value,
                        ],
                        [ //кнопка
                            'text' => TelegramCommandEnum::agreementBack->value,
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
