<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
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

        if (Redis::get($agreementDTO->getSenderId()) == 9
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . FopRegisterDateHandler::AGR_STAGE_FOP_REGISTER_DATE,
            );
            Redis::set($agreementDTO->getSenderId(), 8);

            $agreementDTO->setMessage('💬 Вкажіть дату запису в ЄДР в форматі 01.01.2023'.PHP_EOL.
                'Формат: ДД.ММ.РРРР');
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;

        }

        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_FOP_REGISTER_NUMBER;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }


        if (is_numeric($agreementDTO->getMessage()) === false){
            $agreementDTO->setMessage('🤦 Номер запису в ЄДР вказано не вірно, номер повинен складатись виключно з чисел. Будьласка вкажіть номер запису в ЄДР повторно.');
            return $agreementDTO;
        }

        foreach (str_split($agreementDTO->getMessage()) as $value){
            if ($value < 0){
                $agreementDTO->setMessage('🤦 Номер запису в ЄДР вказано не вірно, номер не може мати відємних чисел. Будьласка вкажіть номер запису в ЄДР повторно.');
                return $agreementDTO;
            }
        }


        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 8);

        $agreementDTO->setMessage('💬 Вкажіть дату запису в ЄДР в форматі 01.01.2023'.PHP_EOL.
        'Формат: ДД.ММ.РРРР');
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
