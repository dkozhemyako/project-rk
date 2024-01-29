<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\FilesDownloadEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class FoSaveFilePasAgrHandler implements AgreementInterface
{
    public const SAVE_FILE_FO_AGR = '_FO_PAS_AGR_FILE';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::SAVE_FILE_FO_AGR;

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

        Redis::set($key, json_encode(['0' => $agreementDTO->getFileName()]), 'EX', 260000);

        $agreementDTO->setMessage(
            'Бажаєте завантажити додаткові фото договору оренди або права власності або талон на МАФ? 📎'
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
                            'text' => FilesDownloadEnum::YES->value,
                        ],
                        [ //кнопка
                            'text' => FilesDownloadEnum::NO->value,
                        ],

                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }

}
