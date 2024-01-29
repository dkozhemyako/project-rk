<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\ClientPassportDTO;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Redis;

class PassportDateHandler implements AgreementInterface
{
    public const AGR_PASSPORT_DATE = '_PASSPORT_DATE';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            return $next($agreementDTO);
        }

        $key = $agreementDTO->getSenderId() . self::AGR_PASSPORT_DATE;

        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setPassportSeriesNumber(Redis::get($agreementDTO->getSenderId() . PassportNumberHandler::AGR_PASSPORT_NUMBER));
            $agreementDTO->getClientAgreementDTO()->setPassportIssue(Redis::get($agreementDTO->getSenderId() . PassportIssuedHandler::AGR_PASSPORT_ISSUED));
            $agreementDTO->getClientAgreementDTO()->setPassportDate(Redis::get($key));

            return $next($agreementDTO);
        }

        $arrayDate = explode( '.', $agreementDTO->getMessage());

        if (!array_key_exists(2, $arrayDate) || array_key_exists(3, $arrayDate)){
            $agreementDTO->setMessage(
                '🤦 Формат дати вказано не вірно. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2020)'
            );

            return $agreementDTO;
        }

        if ((int)$arrayDate[0] == 0 || (int)$arrayDate[0] < 0 || (int)$arrayDate[0] > 31){
            $agreementDTO->setMessage(
                '🤦 Число в даті вказано не вірно, воно не може дорівнювати нулю, бути менше нуля або більше 31. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2020)'
            );

            return $agreementDTO;
        }
        if ((int)$arrayDate[1] == 0 || (int)$arrayDate[1] < 0 || (int)$arrayDate[1] > 12){
            $agreementDTO->setMessage(
                '🤦 Місяць в даті вказано не вірно, він не може дорівнювати нулю, бути менше нуля або більше 12. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2020)'
            );

            return $agreementDTO;
        }
        $today = date ('d.m.Y', time());
        $todayYear = date('Y', time());
        if ((int)$arrayDate[2] > (int)$todayYear) {
            $agreementDTO->setMessage(
                '🤦 Рік в даті вказано не вірно, він не може бути більшим за поточний. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2020)'
            );

            return $agreementDTO;
        }

        if (Carbon::rawCreateFromFormat('d.m.Y', $agreementDTO->getMessage())  >= Carbon::rawCreateFromFormat('d.m.Y', $today)) {
            $agreementDTO->setMessage(
                '🤦 Дата вказана не вірно, вона не може бути більшою або дорівнювати поточному дню. Будь ласка вкажіть дату повторно у форматі ДД.ММ.РРРР (наприклад 31.12.2020)'
            );

            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage('💬 Вкажіть Ваш ІПН, повинно бути 9 цифр.');
        return $agreementDTO;
    }
}
