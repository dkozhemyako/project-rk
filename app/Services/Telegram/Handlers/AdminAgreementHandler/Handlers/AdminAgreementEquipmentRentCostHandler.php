<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EquipmentConditionEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementEquipmentRentCostHandler implements AdminAgreementInterface
{
    public const AGR_EQUIP_RENT_COST_ADMIN = '_ADMIN_EQUIP_RENT_COST';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_EQUIP_RENT_COST_ADMIN;
        if (Redis::exists($key)){
            $adminAgreementDTO->setEquipmentRentalCost(Redis::get($key));
            return $next($adminAgreementDTO);
        }

        if(is_numeric($adminAgreementDTO->getMessage()) === false){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка вводу. Вартість оренди обладнання необхідно вказати числом, наприклад 2000. Будьласка введіть значення.'
            );

            return $adminAgreementDTO;
        }

        foreach (str_split($adminAgreementDTO->getMessage()) as $value){
            if ($value < 0){
                $adminAgreementDTO->setMessage('🤦 Помилка вводу. Вартість оренди обладнання необхідно вказати числом, число не може бути відємним, наприклад 2000. Будьласка введіть значення.');
                return $adminAgreementDTO;
            }
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);
        $adminAgreementDTO->setEquipmentRentalCost(Redis::get($key));

        return $next($adminAgreementDTO);
    }
}
