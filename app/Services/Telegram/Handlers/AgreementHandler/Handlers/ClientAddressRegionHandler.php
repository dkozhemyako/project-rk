<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientAddressRegionHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_REGION = '_CLIENT_REGION';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_REGION;

        if (Redis::get($agreementDTO->getSenderId()) == 12
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . ClientAddressTownHandler::AGR_STAGE_CLIENT_TOWN,
            );
            Redis::set($agreementDTO->getSenderId(), 11);

            $agreementDTO->setMessage(
                '💬 Вкажіть адресу по якій планується встановлення обладнання. Наприклад: Київська обл., м.Харків, вул.Донця, б.25, кв. 1'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){
            $agreementDTO->getClientAgreementDTO()->setClientRegion(Redis::get($agreementDTO->getSenderId().ClientAddressRegionHandler::AGR_STAGE_CLIENT_REGION));
            return $next($agreementDTO);
        }


        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 11);

        $agreementDTO->setMessage(
            '💬 Вкажіть адресу по якій планується встановлення обладнання. Наприклад: Київська обл., м.Харків, вул.Донця, б.25, кв. 1'
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
