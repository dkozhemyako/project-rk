<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementFalse\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\ClientCheckAgreementFalseInterface;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\DTO\ClientCheckAgreementFalseDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class PreparatoryHandler implements ClientCheckAgreementFalseInterface
{
    public const KEY_CLI_AGR_FALSE = '_CLI_AGR_FALSE';

    public function handle(ClientCheckAgreementFalseDTO $agreementFalseDTO, Closure $next): ClientCheckAgreementFalseDTO
    {

        $key = $agreementFalseDTO->getSenderId() . self::KEY_CLI_AGR_FALSE;

            if (Redis::exists($key) == true){
                $agreementFalseDTO->setCallback(Redis::get($key));
                return $next($agreementFalseDTO);
            }

            Redis::del($key);

            $agreementFalseDTO->setMessage(
                    '💬 Опишіть свої пропозиції, питання або зауваження до договору.'
                );

            Redis::set($key, $agreementFalseDTO->getCallback(), 'EX', 260000);
            return $agreementFalseDTO;




    }
}
