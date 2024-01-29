<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

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
        $first = mb_substr($arrayStreet[1], 0, 1);
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
        $agreementDTO->setMessage(
            '💬 Вкажіть номер приміщення в якому буде розташоване обладнання.'
        );
        return $agreementDTO;
    }
}
