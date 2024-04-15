<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientAddressRegionHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_REGION = '_CLIENT_REGION';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_REGION;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        $regions = [
            'АР Крим',
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
                '🤦 Помилка вводу назви області або такої області не існує. Будь ласка вкажіть назву області вашої прописки українською мовою та з великої літери, наприклад Івано-Франківська'
            );
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage(
            '💬 Вкажіть тип та назву населеного пункту вашої прописки, наприклад смт.Мирне або м.Київ'
        );
        return $agreementDTO;
    }
}
