<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Repositories\CheckAdminCreateAgreement\CheckAdminCreateAgreementRepository;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PreparatoryHandler implements AdminAgreementInterface
{
    public function __construct(
        protected CheckAdminCreateAgreementRepository $repository,
    ){}
    public const KEY_ADMIN_CALLBACK = '_ADMIN_CALLBACK';

    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {


        $key = $adminAgreementDTO->getSenderId() . self::KEY_ADMIN_CALLBACK;
        if ($adminAgreementDTO->getMessage() === TelegramCommandEnum::agreementAdminBack->value
            && Redis::get($adminAgreementDTO->getSenderId() . '_admin') == 1)
        {
            Redis::del(
                $adminAgreementDTO->getSenderId() . '_admin',
                $adminAgreementDTO->getSenderId() . AdminAgreementStartDateHandler::AGR_START_DATE_ADMIN,
                $adminAgreementDTO->getSenderId() . AdminAgreementStartDateHandler::AGR_EQ_TYPE_ADMIN,
            );

            $message = 'Вкажіть дату встановлення обладнання в форматі 01.01.2023';
            $adminAgreementDTO->setMessage($message);
            return $adminAgreementDTO;
        }

        if($adminAgreementDTO->getMessage() === TelegramCommandEnum::adminAgreement->value
        ){

            $checkId = $this->repository->checkId($adminAgreementDTO->getCallback());
            if ($checkId->getId() === null){
                $this->repository->store($adminAgreementDTO->getCallback(), $adminAgreementDTO->getSenderId());
            }

            $checkAdmin = $this->repository->checkAdmin($adminAgreementDTO->getCallback(), $adminAgreementDTO->getSenderId());

            if ($checkAdmin->getTelegramId() != $adminAgreementDTO->getSenderId()){
                $adminAgreementDTO->setMessage(
                    '❗ Формуванням завдання номер ' . $adminAgreementDTO->getCallback() .' вже займаються.'
                );

                return $adminAgreementDTO;
            }

            $senderId = $adminAgreementDTO->getSenderId();

                Redis::del(
                    $senderId . '_admin',
                    $senderId . $key,
                    $senderId . AdminAgreementStartDateHandler::AGR_START_DATE_ADMIN,
                    $senderId . AdminAgreementEquipmentModelHandler::AGR_EQUIP_MODEL_ADMIN,
                    $senderId . AdminAgreementEquipmentConditionHandler::AGR_EQUIP_CONDITION_ADMIN,
                    $senderId . AdminAgreementEquipmentCostHandler::AGR_EQUIP_COST_ADMIN,
                    $senderId . AdminAgreementEquipmentRentCostHandler::AGR_EQUIP_RENT_COST_ADMIN,
                    $senderId . CreateAdminAgreementHandler::AGR_CREATE_ADMIN,
                    $senderId . StoreAdminAgreementHandler::AGR_STORE_ADMIN,
                    $senderId . GetAdminDraftAgreementHandler::AGR_DRAFT_ADMIN,
                    $senderId . AdminAgreementStartDateHandler::AGR_EQ_TYPE_ADMIN,
                    $senderId . AdminAgreementCoffeeMachineModelHandler::AGR_CM_MODEL_ADMIN,
                    $senderId . AdminAgreementCoffeeMachineConditionHandler::AGR_CM_CONDITION_ADMIN,
                    $senderId . AdminAgreementCoffeeMachineCostHandler::AGR_CM_COST_ADMIN,
                    $senderId . AdminAgreementCoffeeGrinderModelHandler::AGR_CG_MODEL_ADMIN,
                    $senderId . AdminAgreementCoffeeGrinderConditionHandler::AGR_CG_CONDITION_ADMIN,
                    $senderId . AdminAgreementCoffeeGrinderCostHandler::AGR_CG_COST_ADMIN,
            );



            Redis::set($key, $adminAgreementDTO->getCallback(), 'EX', 260000);

            $message = 'Для продовження формування договору необхідна буде наступна інформація по обладнанню:' . PHP_EOL;
            $message .= '- дата встановлення'. PHP_EOL;
            $message .= '- модель і стан'. PHP_EOL;
            $message .= '- вартість обладнання'. PHP_EOL;
            $message .= '- вартість оренди'. PHP_EOL . PHP_EOL;

            $message .= 'Вкажіть дату встановлення обладнання в форматі 01.01.2023';

            $adminAgreementDTO->setMessage($message);
            return $adminAgreementDTO;
        }

        $adminAgreementDTO->setCallback(Redis::get($key));

        return $next($adminAgreementDTO);
    }
}
