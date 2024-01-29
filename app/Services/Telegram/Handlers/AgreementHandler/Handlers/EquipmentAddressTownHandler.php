<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressTownHandler implements AgreementInterface
{
    public const AGR_STAGE_EQUIP_TOWN = '_EQUIP_TOWN';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_TOWN;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        $arrayTown = explode('.', $agreementDTO->getMessage());

        if (count($arrayTown) < 2){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити тип та назву населеного пункту у форматі (тип.Місто), наприклад м.Київ або смт.Нове (тип, крапка, назва міста). Будь ласка введіть дані повторно.'
            );
            return $agreementDTO;
        }

        $first = mb_substr($arrayTown[1], 0, 1);
        if ($first === mb_strtolower($first)){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити назву населеного пункту з великої букви. Будь ласка введіть дані повторно.'
            );
            return $agreementDTO;
        }

        $first = mb_substr($arrayTown[0], 0, 1);
        if ($first !== mb_strtolower($first)){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Необхідно обовязково вводити тип населеного пункту з маленької букви. Будь ласка введіть дані повторно.'
            );
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage(
            '💬 Вкажіть будь ласка назву вулиці/бульвару/проспекту/провулку де буде встановлене обладнання,'.PHP_EOL.
            'наприклад: просп.Олени Пчілки'

        );
        return $agreementDTO;
    }
}
