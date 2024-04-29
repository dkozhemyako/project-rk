<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\EquipmentConditionEnum;
use App\Enums\TelegramCommandEnum;
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

        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 11)
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . AdminAgreementEquipmentRentCostHandler::AGR_EQUIP_RENT_COST_ADMIN,
                $adminAgreementDTO->getSenderId() . CreateAdminAgreementHandler::AGR_CREATE_ADMIN,
                $adminAgreementDTO->getSenderId() . StoreAdminAgreementHandler::AGR_STORE_ADMIN,
                $adminAgreementDTO->getSenderId() . GetAdminDraftAgreementHandler::AGR_DRAFT_ADMIN,
            );

            Redis::set($adminAgreementDTO->getSenderId() . '_admin', 10);

            $adminAgreementDTO->setMessage(
                'Вкажіть вартість оренди комплекту обладнання (тільки цифри, наприклад 1000)'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup());

            return $adminAgreementDTO;

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
        Redis::set($adminAgreementDTO->getSenderId() . '_admin', 10);


        $adminAgreementDTO->setMessage(
            'Вкажіть вартість оренди комплекту обладнання (тільки цифри, наприклад 1000)'
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
