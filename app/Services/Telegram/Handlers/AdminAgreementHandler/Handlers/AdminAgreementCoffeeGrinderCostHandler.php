<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\EquipmentConditionEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementCoffeeGrinderCostHandler implements AdminAgreementInterface
{
    public const AGR_CG_COST_ADMIN = '_ADMIN_CG_COST';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_CG_COST_ADMIN;

        if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
            return $next($adminAgreementDTO);
        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentCostCoffeeGrinder(Redis::get($key));

            return $next($adminAgreementDTO);
        }

        if(is_numeric($adminAgreementDTO->getMessage()) === false){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка вводу. Вартість обладнання необхідно вказати числом, наприклад 10000. Будьласка введіть значення.'
            );

            return $adminAgreementDTO;
        }

        foreach (str_split($adminAgreementDTO->getMessage()) as $value){
            if ($value < 0){
                $adminAgreementDTO->setMessage('🤦 Помилка вводу. Вартість обладнання необхідно вказати числом, число не може бути відємним, наприклад 10000. Будьласка введіть значення.');
                return $adminAgreementDTO;
            }
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);


        $adminAgreementDTO->setMessage(
            'Вкажіть вартість оренди комплекту обладнання (тільки цифри, наприклад 1000)'
        );

        return $adminAgreementDTO;


    }
}
