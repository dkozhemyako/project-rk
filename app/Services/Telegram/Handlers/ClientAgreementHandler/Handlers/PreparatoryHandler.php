<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentConditionHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentModelHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentRentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementStartDateHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\CreateAdminAgreementHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\StoreAdminAgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\FinalAgreementInterface;
use Closure;
use Illuminate\Support\Facades\Redis;

class PreparatoryHandler implements FinalAgreementInterface
{
    public const KEY_FINAL_CALLBACK = '_FINAL_CALLBACK';

    public function handle(FinalAgreementDTO $finalAgreementDTO, Closure $next): FinalAgreementDTO
    {
        $key = $finalAgreementDTO->getSenderId() . self::KEY_FINAL_CALLBACK;
        if($finalAgreementDTO->getMessage() === TelegramCommandEnum::clientAgreement->value){

            $senderId = $finalAgreementDTO->getSenderId();

                Redis::del(
                    $senderId . $key,
            );

            Redis::set($key, $finalAgreementDTO->getCallback(), 'EX', 260000);

            $message = '💬 Завантажте будь ласка документ з підписом.' . PHP_EOL;
            $message .= '(Файл з підписом .p7s "Дія підпис", його можно завантажити одразу після накладення підпису.)';


            $finalAgreementDTO->setMessage($message);
            return $finalAgreementDTO;
        }

        $finalAgreementDTO->setCallback(Redis::get($key));

        return $next($finalAgreementDTO);
    }
}
