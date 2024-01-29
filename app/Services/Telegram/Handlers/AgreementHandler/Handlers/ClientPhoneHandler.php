<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class ClientPhoneHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_PHONE = '_CLIENT_PHONE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_PHONE;

        $availablePhoneCodes = [
            '39',
            '50',
            '63',
            '66',
            '67',
            '68',
            '73',
            '89',
            '91',
            '92',
            '93',
            '94',
            '95',
            '96',
            '97',
            '98',
            '99',
        ];

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        if (in_array(str_split($agreementDTO->getMessage())[1] . str_split($agreementDTO->getMessage())[2], $availablePhoneCodes, true) === false){
            $agreementDTO->setMessage('🤦 Такого коду0636964239 мобільної мережі не зареєстровано за жодним оператором. Повторіть спробу.');
            return $agreementDTO;
        }

        if(strlen($agreementDTO->getMessage()) != 10){
            $agreementDTO->setMessage('🤦 Номер телефону вказано не вірно, необхідно вказати 10 чисел починаючи з 0, наприклад 0631112233');
            return $agreementDTO;
        }

        if (is_numeric($agreementDTO->getMessage()) === false){
            $agreementDTO->setMessage('🤦 Номер телефону вказано не вірно, телефон повинен складатись виключно з чисел, необхідно вказати 10 чисел починаючи з 0, наприклад 0631112233');
            return $agreementDTO;
        }

        if (str_split($agreementDTO->getMessage())[0] != 0){
            $agreementDTO->setMessage('🤦 Номер телефону вказано не вірно, необхідно вказати 10 чисел починаючи з 0, наприклад 0631112233');
            return $agreementDTO;
        }

        if (str_split($agreementDTO->getMessage())[1] == 0 || str_split($agreementDTO->getMessage())[2] == 0){
            $agreementDTO->setMessage('🤦 Номер телефону вказано не вірно, необхідно вказати 10 чисел починаючи з 0, друге і третє число не може бути нулем, наприклад 0631112233');
            return $agreementDTO;
        }

        Redis::set($key, (int)$agreementDTO->getMessage(), 'EX', 260000);

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            $agreementDTO->setMessage('💬 Вкажіть номер запису в ЄДР , має бути 19 символів');
            return $agreementDTO;
        }

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            $agreementDTO->setMessage('💬 Вкажіть номер та серію паспорту однією стрічкою, наприклад НМ112233. Якщо у вас ID картка вкажіт її номер.');
            return $agreementDTO;
        }

    }
}
