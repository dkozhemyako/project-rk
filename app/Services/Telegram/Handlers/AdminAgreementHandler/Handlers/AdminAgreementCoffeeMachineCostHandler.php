<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\EquipmentConditionEnum;
use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementCoffeeMachineCostHandler implements AdminAgreementInterface
{
    public const AGR_CM_COST_ADMIN = '_ADMIN_CM_COST';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_CM_COST_ADMIN;

        if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
            return $next($adminAgreementDTO);
        }

        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 8)
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . AdminAgreementCoffeeGrinderModelHandler::AGR_CG_MODEL_ADMIN,
            );

            Redis::set($adminAgreementDTO->getSenderId() . '_admin', 7);

            $adminAgreementDTO->setMessage(
                'Вкажіть модель кавомолки.'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup());

            return $adminAgreementDTO;
        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentCostCoffeeMachine(Redis::get($key));

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
        Redis::set($adminAgreementDTO->getSenderId() . '_admin', 7);

        $adminAgreementDTO->setMessage(
            'Вкажіть модель кавомолки.'
        );
        $adminAgreementDTO->setReplyMarkup($this->replyMarkup());

        return $adminAgreementDTO;

    }

    private function replyMarkup(): array
    {
        return [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => TelegramCommandEnum::agreementAdminBack->value,
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
