<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Redis;

class AdminAgreementStartDateHandler implements AdminAgreementInterface
{
    public const AGR_START_DATE_ADMIN = '_ADMIN_START_DATE';
    public const AGR_EQ_TYPE_ADMIN = '_ADMIN_EQ_TYPE';

    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
    ){}

    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_START_DATE_ADMIN;
        $keyEqType = $adminAgreementDTO->getSenderId() . self::AGR_EQ_TYPE_ADMIN;

        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 2
            ||
            $adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 5
        )
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . AdminAgreementEquipmentModelHandler::AGR_EQUIP_MODEL_ADMIN,
                $adminAgreementDTO->getSenderId() . AdminAgreementCoffeeMachineModelHandler::AGR_CM_MODEL_ADMIN,
            );

            Redis::set($adminAgreementDTO->getSenderId() . '_admin', 1);

            $clientAgreementData = $this->adminAgreementRepository->getClientAgreementData($adminAgreementDTO->getCallback());
            if($clientAgreementData->getEqType() == EqTypeClientEnum::HV->value || $clientAgreementData->getEqType() == EqTypeClientEnum::PACK->value){
                $adminAgreementDTO->setMessage(
                    'Вкажіть модель холодильної вітрини.'
                );
                $adminAgreementDTO->setReplyMarkup($this->replyMarkup());
                return $adminAgreementDTO;
            }

            if($clientAgreementData->getEqType() == EqTypeClientEnum::KK->value){
                $adminAgreementDTO->setMessage(
                    'Вкажіть модель кавоварки.'
                );
                $adminAgreementDTO->setReplyMarkup($this->replyMarkup());
                return $adminAgreementDTO;
            }

        }

        if (Redis::exists($key) == true){

            $adminAgreementDTO->setDateFromAdmin(Redis::get($key));
            $adminAgreementDTO->setEqType(Redis::get($keyEqType));

            return $next($adminAgreementDTO);
        }

        $arrayDate = explode( '.', $adminAgreementDTO->getMessage());

        if (!array_key_exists(2, $arrayDate) || array_key_exists(3, $arrayDate)){
            $adminAgreementDTO->setMessage(
                '🤦 Формат дати вказано не вірно. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }

        if ((int)$arrayDate[0] == 0 || (int)$arrayDate[0] < 0 || (int)$arrayDate[0] > 31){
            $adminAgreementDTO->setMessage(
                '🤦 Число в даті вказано не вірно, воно не може дорівнювати нулю, бути менше нуля або більше 31. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }
        if ((int)$arrayDate[1] == 0 || (int)$arrayDate[1] < 0 || (int)$arrayDate[1] > 12){
            $adminAgreementDTO->setMessage(
                '🤦 Місяць в даті вказано не вірно, він не може дорівнювати нулю, бути менше нуля або більше 12. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }

        if (mb_strlen($arrayDate[0]) > 2 || mb_strlen($arrayDate[1]) > 2 || mb_strlen($arrayDate[2]) > 4){
            $adminAgreementDTO->setMessage(
                '🤦 Дата вказана з помилками, перевірте чи нема зайвих символів або їх недостатньо. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }

        $today = date ('d.m.Y', time());
        $todayYear = date('Y', time());
        if ((int)$arrayDate[2] < (int)$todayYear || (int)$arrayDate[2] > (int)$todayYear+1) {
            $adminAgreementDTO->setMessage(
                '🤦 Рік в даті вказано не вірно, він не може бути меншим за поточний або більшим за наступний. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }

        if (Carbon::createFromFormat('d.m.Y', $adminAgreementDTO->getMessage()) < Carbon::createFromFormat('d.m.Y', $today)) {
            $adminAgreementDTO->setMessage(
                '🤦 Дата вказана не вірно, вона не може бути меншою за поточний день. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $adminAgreementDTO;
        }

        Redis::set($key, $adminAgreementDTO->getMessage(), 'EX', 260000);


        $clientAgreementData = $this->adminAgreementRepository->getClientAgreementData($adminAgreementDTO->getCallback());
        Redis::set($keyEqType, $clientAgreementData->getEqType(), 'EX', 260000);
        Redis::set($adminAgreementDTO->getSenderId() . '_admin', 1);

        if($clientAgreementData->getEqType() == EqTypeClientEnum::HV->value || $clientAgreementData->getEqType() == EqTypeClientEnum::PACK->value){
            $adminAgreementDTO->setMessage(
                'Вкажіть модель холодильної вітрини.'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup());
            return $adminAgreementDTO;
        }

        if($clientAgreementData->getEqType() == EqTypeClientEnum::KK->value){
            $adminAgreementDTO->setMessage(
                'Вкажіть модель кавоварки.'
            );
            $adminAgreementDTO->setReplyMarkup($this->replyMarkup());
            return $adminAgreementDTO;
        }

    }

    private function replyMarkup(): array
    {
        return [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => "Gooder XC68L",
                        ],
                        [ //кнопка
                            'text' => "Reednee RT78L",
                        ],
                        [ //кнопка
                            'text' => "Frosty RT78L",
                        ],
                    ],
                    [ //строка
                        [ //кнопка
                            'text' => "Frosty RT98L",
                        ],
                        [ //кнопка
                            'text' => "Reednee RT98L",
                        ],
                        [ //кнопка
                            'text' => "Gooder XCW100L",
                        ],
                    ],
                    [ //строка
                        [ //кнопка
                            'text' => "Gooder XCW120LS",
                        ],
                        [ //кнопка
                            'text' => "Gooder XCW120 CUBE",
                        ],
                        [ //кнопка
                            'text' => "Gooder XCW160LS",
                        ],
                    ],
                    [ //строка
                        [ //кнопка
                            'text' => "Gooder XCW160 CUBE",
                        ],
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
