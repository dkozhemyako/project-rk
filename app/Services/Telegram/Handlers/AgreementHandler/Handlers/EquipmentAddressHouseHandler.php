<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressHouseHandler implements AgreementInterface
{
    public const AGR_STAGE_EQUIP_HOUSE = '_EQUIP_HOUSE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_HOUSE;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        if (is_numeric(str_split($agreementDTO->getMessage())[0]) === false || str_split($agreementDTO->getMessage())[0] == 0){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Номер будинку повинен починатись з числа але не з нуля, наприклад 14в. Будь ласка введіть номер будинку повторно.'
            );
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 19);
        $agreementDTO->setMessage(
            '💬 Вкажіть додаткові примітки розташування обладнання, поверх, площу, тип приміщення.'
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
