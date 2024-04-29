<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Redis;

class AgreementStartDateClient implements AgreementInterface
{
    public const AGR_START_DATE_CLIENT = '_CLIENT_START_DATE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_START_DATE_CLIENT;

        if (Redis::get($agreementDTO->getSenderId()) == 6
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value) {
            Redis::del(
                $agreementDTO->getSenderId() . ClientNameHandler::AGR_STAGE_CLIENT_NAME,
            );

            Redis::set($agreementDTO->getSenderId(), 5);

            $agreementDTO->setMessage(
                '💬 Вкажіть повністю ПІБ орендаря обладнання.'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setDateFromClient(Redis::get($key));

            return $next($agreementDTO);
        }

        $arrayDate = explode( '.', $agreementDTO->getMessage());

        if (!array_key_exists(2, $arrayDate) || array_key_exists(3, $arrayDate)){
            $agreementDTO->setMessage(
                '🤦 Формат дати вказано не вірно. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }

        if ((int)$arrayDate[0] == 0 || (int)$arrayDate[0] < 0 || (int)$arrayDate[0] > 31){
            $agreementDTO->setMessage(
                '🤦 Число в даті вказано не вірно, воно не може дорівнювати нулю, бути менше нуля або більше 31. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }
        if ((int)$arrayDate[1] == 0 || (int)$arrayDate[1] < 0 || (int)$arrayDate[1] > 12){
            $agreementDTO->setMessage(
                '🤦 Місяць в даті вказано не вірно, він не може дорівнювати нулю, бути менше нуля або більше 12. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }

        if (mb_strlen($arrayDate[0]) > 2 || mb_strlen($arrayDate[1]) > 2 || mb_strlen($arrayDate[2]) > 4){
            $agreementDTO->setMessage(
                '🤦 Дата вказана з помилками, перевірте чи нема зайвих символів або їх недостатньо. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }

        $today = date ('d.m.Y', time());
        $todayYear = date('Y', time());
        if ((int)$arrayDate[2] < (int)$todayYear) {
            $agreementDTO->setMessage(
                '🤦 Рік в даті вказано не вірно, він не може бути меншим за поточний. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }

        if (Carbon::createFromFormat('d.m.Y', $agreementDTO->getMessage()) < Carbon::createFromFormat('d.m.Y', $today)) {
            $agreementDTO->setMessage(
                '🤦 Дата вказана не вірно, вона не може бути меншою за поточний день. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2024)'
            );

            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 5);

        $agreementDTO->setMessage(
            '💬 Вкажіть повністю ПІБ орендаря обладнання.'
        );
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
