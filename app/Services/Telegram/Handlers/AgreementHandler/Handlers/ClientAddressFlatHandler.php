<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\ClientAddressDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class ClientAddressFlatHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_FLAT = '_CLIENT_FLAT';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_FLAT;

        if (Redis::get($agreementDTO->getSenderId()) == 16
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . EquipmentAddressRegionHandler::AGR_STAGE_EQUIP_REGION,
            );
            Redis::set($agreementDTO->getSenderId(), 15);

            $agreementDTO->setMessage
            (
                '💬 Вкажіть назву області в якій планується встановлення обладнання.'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setClientRegion(Redis::get($agreementDTO->getSenderId().ClientAddressRegionHandler::AGR_STAGE_CLIENT_REGION));
            $agreementDTO->getClientAgreementDTO()->setClientTown(Redis::get($agreementDTO->getSenderId(). ClientAddressTownHandler::AGR_STAGE_CLIENT_TOWN));
            $agreementDTO->getClientAgreementDTO()->setClientStreet(Redis::get($agreementDTO->getSenderId(). ClientAddressStreetHandler::AGR_STAGE_CLIENT_STREET));
            $agreementDTO->getClientAgreementDTO()->setClientHouse(Redis::get($agreementDTO->getSenderId(). ClientAddressHouseHandler::AGR_STAGE_CLIENT_HOUSE));
            $agreementDTO->getClientAgreementDTO()->setClientFlat(Redis::get($agreementDTO->getSenderId(). ClientAddressFlatHandler::AGR_STAGE_CLIENT_FLAT));

            return $next($agreementDTO);
        }

        if (is_numeric($agreementDTO->getMessage()) === false) {
            $agreementDTO->setMessage
            (
                '🤦 Помилка вводу. Номер квартири повинен бути числом. Якщо номер квартири відсутній (приватний будинок) - введіть 0'
            );
            return $agreementDTO;
        }

        if ((int)$agreementDTO->getMessage() < 0) {
            $agreementDTO->setMessage
            (
                '🤦 Помилка вводу. Номер квартири не може бути відємним числом. Якщо номер квартири відсутній (приватний будинок) - введіть 0'
            );
            return $agreementDTO;
        }


        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 15);
        $agreementDTO->setMessage
        (
            '💬 Вкажіть назву області в якій планується встановлення обладнання.'
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
