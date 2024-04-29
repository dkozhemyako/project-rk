<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class PassportIssuedHandler implements AgreementInterface
{
    public const AGR_PASSPORT_ISSUED = '_PASSPORT_ISSUED';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            return $next($agreementDTO);
        }

        $key = $agreementDTO->getSenderId() . self::AGR_PASSPORT_ISSUED;

        if (Redis::get($agreementDTO->getSenderId()) == 201
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . PassportDateHandler::AGR_PASSPORT_DATE,
            );
            Redis::set($agreementDTO->getSenderId(), 200);

            $agreementDTO->setMessage('💬 Вкажіть дату видачі паспорту у форматі 01.01.2020');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;

        }

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }

        if(is_numeric($agreementDTO->getMessage()) === true) {
            if(mb_strlen($agreementDTO->getMessage()) != 4){
                $agreementDTO->setMessage('🤦 Дані вказано не вірно. Якщо у вас ID паспорт, орган що видав його вказаний в форматі 4 чисел, вкажіть їх будь ласка.');
                return $agreementDTO;
            }
        }

        if(is_numeric($agreementDTO->getMessage()) === false) {
            if($agreementDTO->getMessage() === ''){
                $agreementDTO->setMessage('🤦 Дані вказано не вірно, поле не може бути пустим. Введіть орган, що видав паспорт повторно.');
                return $agreementDTO;
            }
            if(mb_strlen($agreementDTO->getMessage()) < 20){
                $agreementDTO->setMessage('🤦 Дані вказано не вірно, замало літер. Вкажіть повністю запис з паспорту про орган видачі паспорту.');
                return $agreementDTO;
            }
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 200);
        $agreementDTO->setMessage('💬 Вкажіть дату видачі паспорту у форматі 01.01.2020');
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
