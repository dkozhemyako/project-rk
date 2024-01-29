<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientNameHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_NAME = '_CLIENT_NAME';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_NAME;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        $arrayName = explode( " ", $agreementDTO->getMessage());

        foreach ($arrayName as $value){
            $first = mb_substr($value, 0, 1);
            if ($first === mb_strtolower($first)){
                $agreementDTO->setMessage(
                    '🤦 Введіть корректне значення. Необхідно вказати прізвище, імя та по батькові через пробіл. Кожне значення з великої літери, наприклад Іванов Іван Іванович'
                );
                return $agreementDTO;
            }
        }

        if (mb_strlen($agreementDTO->getMessage()) < 10 || count($arrayName) < 3 ){
            $agreementDTO->setMessage(
                '🤦 Введіть корректне значення. Необхідно вказати прізвище, імя та по батькові через пробіл'
            );
            return $agreementDTO;
        }

        foreach ($arrayName as $value){
            if (mb_strlen($value) < 2) {
                $agreementDTO->setMessage(
                    '🤦 Введіть корректне значення. Необхідно вказати прізвище, імя та по батькові через пробіл. Жодне значення не може бути менше 2 символів.'
                );
                return $agreementDTO;
            }
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage('💬 Напишіть будь ласка контактний номер телефону в форматі 0ххххххххх');
        return $agreementDTO;
    }
}
