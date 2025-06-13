<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressRegionHandler implements AgreementInterface
{
    public const AGR_STAGE_EQUIP_REGION = '_EQUIP_REGION';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_REGION;

        if (Redis::get($agreementDTO->getSenderId()) == 13
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . EquipmentAddressTownHandler::AGR_STAGE_EQUIP_TOWN,
            );
            Redis::set($agreementDTO->getSenderId(), 12);

            $agreementDTO->setMessage(
                '💬 Вкажіть додаткові примітки розташування обладнання, поверх, площу, тип приміщення.'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }


        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 12);
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
