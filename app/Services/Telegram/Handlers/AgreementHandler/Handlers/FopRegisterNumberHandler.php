<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class FopRegisterNumberHandler implements AgreementInterface
{
    public const AGR_STAGE_FOP_REGISTER_NUMBER = '_FOP_NUMBER';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            return $next($agreementDTO);
        }

        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_FOP_REGISTER_NUMBER;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        if(mb_strlen($agreementDTO->getMessage()) != 19 || mb_strlen($agreementDTO->getMessage()) != 17){
            $agreementDTO->setMessage('🤦 Номер запису в ЄДР вказано не вірно, номер повинен містити 19 або 17 чисел. Будьласка вкажіть номер запису в ЄДР повторно.');
            return $agreementDTO;
        }

        if (is_numeric($agreementDTO->getMessage()) === false){
            $agreementDTO->setMessage('🤦 Номер запису в ЄДР вказано не вірно, номер повинен складатись виключно з 19 чисел. Будьласка вкажіть номер запису в ЄДР повторно.');
            return $agreementDTO;
        }

        foreach (str_split($agreementDTO->getMessage()) as $value){
            if ($value < 0){
                $agreementDTO->setMessage('🤦 Номер запису в ЄДР вказано не вірно, номер не може мати відємних чисел. Будьласка вкажіть номер запису в ЄДР повторно. Повинно бути 19 чисел, кожне з яких більше 0');
                return $agreementDTO;
            }
        }


        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage('💬 Вкажіть дату запису в ЄДР в форматі 01.01.2023'.PHP_EOL.
        'Формат: ДД.ММ.РРРР');
        return $agreementDTO;
    }
}
