<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PassportNumberHandler implements AgreementInterface
{
    public const AGR_PASSPORT_NUMBER = '_PASSPORT_NUMBER';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            return $next($agreementDTO);
        }

        $keyRedis = $agreementDTO->getSenderId() . self::AGR_PASSPORT_NUMBER;

        if (Redis::get($agreementDTO->getSenderId()) == 200
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . PassportIssuedHandler::AGR_PASSPORT_ISSUED,
            );
            Redis::set($agreementDTO->getSenderId(), 8);

            $agreementDTO->setMessage('💬 Вкажіть ким виданий докумет - номер або назву органу');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($keyRedis) == true){
            return $next($agreementDTO);
        }

        $arrayPassport = [];
        for ($i = 0; $i < mb_strlen($agreementDTO->getMessage()); $i++){
            $arrayPassport[$i] = mb_substr($agreementDTO->getMessage(), $i, 1);
        }

        if(is_numeric($arrayPassport[0]) === false && is_numeric($arrayPassport[1]) === false){
            if (mb_strlen($agreementDTO->getMessage()) != 8){
                $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас не ID паспорт введіть серію та номер паспорту без пробілу, 2 літери та 6 цифр, загалом 8 знаків, наприклад СН112233');
                return $agreementDTO;
            }
            foreach ($arrayPassport as $key => $value){
                if ($key > 1) {
                    if (is_numeric($value) === false){
                        $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас не ID паспорт введіть серію та номер паспорту без пробілу, 2 літери та 6 цифр, загалом 8 знаків, наприклад СН112233');
                        return $agreementDTO;
                    }
                }
            }
            foreach ($arrayPassport as $key => $value){
                if ($key < 2) {
                    if ($value === mb_strtolower($value)){
                        $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас не ID паспорт введіть серію та номер паспорту без пробілу, 2 великі літери та 6 цифр, загалом 8 знаків, наприклад СН112233');
                        return $agreementDTO;
                    }
                }
            }
            foreach ($arrayPassport as $key => $value){
                if ($key > 1) {
                    if (is_numeric($value) < 0){
                        $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас не ID паспорт введіть серію та номер паспорту без пробілу, 2 літери та 6 цифр (не можуть бути відємними значеннями), загалом 8 знаків, наприклад СН112233');
                        return $agreementDTO;
                    }
                }
            }
        }
        if(is_numeric($arrayPassport[0]) === true){
            if (count($arrayPassport) != 9){
                $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас ID паспорт введіть номер паспорту 9 цифр, наприклад 000111222');
                return $agreementDTO;
            }
            foreach ($arrayPassport as $value){
                if (is_numeric($value) === false){
                    $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас ID паспорт введіть номер паспорту 9 цифр, наприклад 000111222');
                    return $agreementDTO;
                }
            }
            foreach ($arrayPassport as $value){
                if ($value < 0){
                    $agreementDTO->setMessage('🤦 Номер паспорту вказано не вірно. Якщо у вас ID паспорт введіть номер паспорту 9 цифр, не може бути відємним, наприклад 000111222');
                    return $agreementDTO;
                }
            }
        }

        Redis::set($keyRedis, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 8);
        $agreementDTO->setMessage('💬 Вкажіть ким виданий докумет - номер або назву органу');
        $agreementDTO->setReplyMarkup($this->replyMarkup());
        return $agreementDTO;
    }

    private function replyMarkup(): array
    {
        return [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => TelegramCommandEnum::returnMain->value,
                        ],
                        [ //кнопка
                            'text' => TelegramCommandEnum::agreementBack->value,
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
