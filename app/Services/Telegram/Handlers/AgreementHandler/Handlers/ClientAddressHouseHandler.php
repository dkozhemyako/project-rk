<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class ClientAddressHouseHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_HOUSE = '_CLIENT_HOUSE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_HOUSE;

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
        $agreementDTO->setMessage(
            '💬 Вкажіть номер квартири вашої прописки, якщо будинок приватний, вкажіть 0.'
        );
        return $agreementDTO;
    }
}
