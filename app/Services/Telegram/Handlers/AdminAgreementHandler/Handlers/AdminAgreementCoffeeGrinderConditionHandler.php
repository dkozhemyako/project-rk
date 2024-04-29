<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\EquipmentConditionEnum;
use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementCoffeeGrinderConditionHandler implements AdminAgreementInterface
{
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => 'було у використанні',
                        ],
                        [ //кнопка
                            'text' => 'не було у використанні',
                        ],

                    ],
                    [ //строка
                        [ //кнопка
                            'text' => TelegramCommandEnum::agreementAdminBack->value,
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    public const AGR_CG_CONDITION_ADMIN = '_ADMIN_CG_CONDITION';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_CG_CONDITION_ADMIN;

        if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
            return $next($adminAgreementDTO);
        }

        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 10)
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . AdminAgreementCoffeeGrinderCostHandler::AGR_CG_COST_ADMIN,
            );

            Redis::set($adminAgreementDTO->getSenderId() . '_admin', 9);

            $adminAgreementDTO->setMessage(
                'Вкажіть вартість кавомолки (тільки цифри, наприклад 5000)'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup());

            return $adminAgreementDTO;
        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentConditionCoffeeGrinder(EquipmentConditionEnum::from(Redis::get($key)));

            return $next($adminAgreementDTO);
        }

        if(EquipmentConditionEnum::tryFrom($adminAgreementDTO->getMessage()) === null){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка вводу. Ви не обрали жодного значення з меню. Оберіть значення з меню 👇'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup);
            return $adminAgreementDTO;
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);
        Redis::set($adminAgreementDTO->getSenderId() . '_admin', 9);

        $adminAgreementDTO->setMessage(
            'Вкажіть вартість кавомолки (тільки цифри, наприклад 5000)'
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
