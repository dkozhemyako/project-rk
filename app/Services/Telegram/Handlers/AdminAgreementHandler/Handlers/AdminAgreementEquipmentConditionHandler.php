<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EquipmentConditionEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementEquipmentConditionHandler implements AdminAgreementInterface
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
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    public const AGR_EQUIP_CONDITION_ADMIN = '_ADMIN_EQUIP_CONDITION';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_EQUIP_CONDITION_ADMIN;

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentCondition(EquipmentConditionEnum::from(Redis::get($key)));

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

        $adminAgreementDTO->setMessage(
            'Вкажіть вартість обладнання (тільки цифри, наприклад 5000)'
        );

        return $adminAgreementDTO;
    }
}
