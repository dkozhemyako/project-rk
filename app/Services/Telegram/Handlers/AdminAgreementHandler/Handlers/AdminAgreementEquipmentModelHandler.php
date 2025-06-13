<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementEquipmentModelHandler implements AdminAgreementInterface
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
    public const AGR_EQUIP_MODEL_ADMIN = '_ADMIN_EQUIP_MODEL';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_EQUIP_MODEL_ADMIN;

        if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::KK->value){
            return $next($adminAgreementDTO);
        }

        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 3)
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . AdminAgreementEquipmentConditionHandler::AGR_EQUIP_CONDITION_ADMIN,
            );

            Redis::set($adminAgreementDTO->getSenderId() . '_admin', 2);

            $adminAgreementDTO->setMessage(
                'Оберіть стан холодильної вітрини 👇'
            );

            $adminAgreementDTO->setReplyMarkup($this->replyMarkup);

            return $adminAgreementDTO;
        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setEquipmentModel(Redis::get($key));

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

        if($adminAgreementDTO->getMessage() == "Gooder XC68L" || $adminAgreementDTO->getMessage() == "Reednee RT78L" || $adminAgreementDTO->getMessage() == "Frosty RT78L" || $adminAgreementDTO->getMessage() == "Frosty RT98L" || $adminAgreementDTO->getMessage() == "Reednee RT98L"){
            $adminAgreementDTO->setMessage(
                "Холодильна вітрина, настільна, вертикальна, " . $adminAgreementDTO->getMessage() . " чорного кольору, СН:44957885"
            );
        }

        if($adminAgreementDTO->getMessage() == "Gooder XCW100L" || $adminAgreementDTO->getMessage() == "Gooder XCW120LS" || $adminAgreementDTO->getMessage() == "Gooder XCW120 CUBE" || $adminAgreementDTO->getMessage() == "Gooder XCW160LS" || $adminAgreementDTO->getMessage() == "Gooder XCW160 CUBE"){
            $adminAgreementDTO->setMessage(
                "Холодильна вітрина, горизонтальна, настільна, " . $adminAgreementDTO->getMessage() . " чорного кольору,  СН:8884857"
            );
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);
        Redis::set($adminAgreementDTO->getSenderId() . '_admin', 2);

        $adminAgreementDTO->setMessage(
            'Оберіть стан холодильної вітрини 👇'
        );

        $adminAgreementDTO->setReplyMarkup($this->replyMarkup);

        return $adminAgreementDTO;
    }

}
