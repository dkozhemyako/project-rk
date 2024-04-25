<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementCoffeeMachineModelHandler implements AdminAgreementInterface
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
    public const AGR_CM_MODEL_ADMIN = '_ADMIN_CM_MODEL';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_CM_MODEL_ADMIN;

        if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
            return $next($adminAgreementDTO);
        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentModelCoffeeMachine(Redis::get($key));

            return $next($adminAgreementDTO);
        }

        if (is_numeric($adminAgreementDTO->getMessage()) === true){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка вводу, модель не може містити тільки число. Введіть дані повторно.'
            );
            return $adminAgreementDTO;
        }

        if (mb_strlen($adminAgreementDTO->getMessage()) < 10){
            $adminAgreementDTO->setMessage(
                '🤦 Помилка вводу, назва моделі повинна складатись мінімум з 10 символів. Введіть дані повторно.'
            );
            return $adminAgreementDTO;
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);

        $adminAgreementDTO->setMessage(
            'Оберіть стан кавоварки 👇'
        );

        $adminAgreementDTO->setReplyMarkup($this->replyMarkup);

        return $adminAgreementDTO;
    }
}
