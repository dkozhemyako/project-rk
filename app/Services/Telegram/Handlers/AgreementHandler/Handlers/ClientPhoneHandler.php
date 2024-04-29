<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientPhoneHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_PHONE = '_CLIENT_PHONE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if (Redis::get($agreementDTO->getSenderId()) == 8
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . FopRegisterNumberHandler::AGR_STAGE_FOP_REGISTER_NUMBER,
                $agreementDTO->getSenderId() . PassportNumberHandler::AGR_PASSPORT_NUMBER,
            );
            Redis::set($agreementDTO->getSenderId(), 7);

            if (TypeClientEnum::tryFrom(Redis::get($agreementDTO->getSenderId() . ClientTypeHandler::AGR_STAGE_CLIENT_TYPE)) === TypeClientEnum::FOP){
                $agreementDTO->setMessage('💬 Вкажіть номер запису в ЄДР , має бути 19 або 17 символів');
                $agreementDTO->setReplyMarkup($this->replyMarkup());
                return $agreementDTO;
            }

            if (TypeClientEnum::tryFrom(Redis::get($agreementDTO->getSenderId() . ClientTypeHandler::AGR_STAGE_CLIENT_TYPE)) === TypeClientEnum::FO){
                $agreementDTO->setMessage('💬 Вкажіть номер та серію паспорту однією стрічкою, наприклад НМ112233. Якщо у вас ID картка вкажіт її номер.');
                $agreementDTO->setReplyMarkup($this->replyMarkup());
                return $agreementDTO;
            }

        }

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
            $agreementDTO->setMessage('🤦 Такого коду мобільної мережі не зареєстровано за жодним оператором. Повторіть спробу.');
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

        if (str_split($agreementDTO->getMessage())[1] == 0 ){
            $agreementDTO->setMessage('🤦 Номер телефону вказано не вірно, необхідно вказати 10 чисел починаючи з 0, друге число не може бути нулем, наприклад 0631112233');
            return $agreementDTO;
        }

        Redis::set($key, (int)$agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 7);

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            $agreementDTO->setMessage('💬 Вкажіть номер запису в ЄДР , має бути 19 або 17 символів');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            $agreementDTO->setMessage('💬 Вкажіть номер та серію паспорту однією стрічкою, наприклад НМ112233. Якщо у вас ID картка вкажіт її номер.');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

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
