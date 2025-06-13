<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\ClientInfoDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientRegisterNumberHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_REG_NUMBER = '_CLIENT_REG_NUMBER';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_REG_NUMBER;

        if (Redis::get($agreementDTO->getSenderId()) == 11
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . ClientAddressRegionHandler::AGR_STAGE_CLIENT_REGION,
            );
            Redis::set($agreementDTO->getSenderId(), 10);

            $agreementDTO->setMessage(
                '💬 Вкажіть адресу вашої прописки. Наприклад: Київська обл., м.Київ, вул.Перемоги, б.32, кв. 12'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setName(Redis::get($agreementDTO->getSenderId() . ClientNameHandler::AGR_STAGE_CLIENT_NAME));
            $agreementDTO->getClientAgreementDTO()->setPhone(Redis::get($agreementDTO->getSenderId() . ClientPhoneHandler::AGR_STAGE_CLIENT_PHONE));
            $agreementDTO->getClientAgreementDTO()->setClientInn(Redis::get($agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_REG_NUMBER));

            return $next($agreementDTO);
        }

        if(mb_strlen($agreementDTO->getMessage()) != 10){
            $agreementDTO->setMessage('🤦 Номер ІПН вказано не вірно, номер повинен містити 10 чисел. Будьласка вкажіть номер ІНН повторно.');
            return $agreementDTO;
        }

        if (is_numeric($agreementDTO->getMessage()) === false){
            $agreementDTO->setMessage('🤦 Номер ІПН вказано не вірно, номер повинен складатись виключно з 10 чисел. Будьласка вкажіть номер ІПН повторно.');
            return $agreementDTO;
        }

        foreach (str_split($agreementDTO->getMessage()) as $value){
            if ($value < 0){
                $agreementDTO->setMessage('🤦 Номер ІПН вказано не вірно, номер не може мати відємних чисел. Будьласка вкажіть номер ІНН повторно. Повинно бути 10 чисел, жодне з них не повинно бути відємним.');
                return $agreementDTO;
            }
        }

        if ($agreementDTO->getMessage() * 1 === 0){
            $agreementDTO->setMessage('🤦 Номер ІПН вказано не вірно, номер не може складатись з одних нулів. Будьласка вкажіть номер ІПН повторно. Повинно бути 10 чисел, кожне з яких повинно дорівнювати або бути більше 0');
            return $agreementDTO;
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 10);
        $agreementDTO->setMessage(
            '💬 Вкажіть адресу вашої прописки. Наприклад: Київська обл., м.Київ, вул.Перемоги, б.32, кв. 12'
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
