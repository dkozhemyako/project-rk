<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressRegionHandler implements AgreementInterface
{
    public const AGR_STAGE_EQUIP_REGION = '_EQUIP_REGION';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_REGION;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        $regions = [
            'Автономна Республіка Крим',
            'Вінницька',
            'Волинська',
            'Дніпропетровська',
            'Донецька',
            'Житомирська',
            'Закарпатська',
            'Запорізька',
            'Івано-Франківська',
            'Київська',
            'Кіровоградська',
            'Луганська',
            'Львівська',
            'Миколаївська',
            'Одеська',
            'Полтавська',
            'Рівненська',
            'Сумська',
            'Тернопільська',
            'Харківська',
            'Херсонська',
            'Хмельницька',
            'Черкаська',
            'Чернівецька',
            'Чернігівська',
        ];

        if(in_array($agreementDTO->getMessage(), $regions, true) === false){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу назви області або такої області не існує. Будь ласка вкажіть назву області розміщення обладнання українською мовою і з великої літери, наприклад Івано-Франківська'
            );
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage(
            '💬 Вкажіть назву населеного пункту в якому планується встановлення обладнання, наприклад м.Київ'
        );
        return $agreementDTO;
    }
}
