<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class FoSaveFilePas3thHandler implements AgreementInterface
{
    public const SAVE_FILE_FO_PAS_3TH = '_FO_PAS_3TH_FILE';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::SAVE_FILE_FO_PAS_3TH;

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            return $next($agreementDTO);
        }

        if (Redis::exists($key) == true){

            return $next($agreementDTO);
        }

        if ($agreementDTO->getFileName() === ''){
            $agreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу'
            );

            return $agreementDTO;

        }

        Redis::set($key, $agreementDTO->getFileName(), 'EX', 260000);

        $agreementDTO->setMessage(
            'Завантажте фото договору оренди або права власності або талон на МАФ. 📎'
        );

        return $agreementDTO;



    }
}
