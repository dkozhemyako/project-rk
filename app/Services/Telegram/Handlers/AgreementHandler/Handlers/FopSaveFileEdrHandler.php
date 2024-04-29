<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\FilesDownloadEnum;
use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class FopSaveFileEdrHandler implements AgreementInterface
{
    public const SAVE_FILE_FOP_EDR = '_FOP_EDR_FILE';
    public const MEDIA_FILE_FOP_EDR = '_MEDIA_FILE_FOP_EDR';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {

        if ($agreementDTO->getMediaGroupId() != 0){
            Redis::set($agreementDTO->getSenderId() . self::MEDIA_FILE_FOP_EDR, 'check' , 'EX', 260000);
        }

        $key = $agreementDTO->getSenderId().self::SAVE_FILE_FOP_EDR;

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            return $next($agreementDTO);
        }


        if (Redis::exists($key) == true){

            return $next($agreementDTO);
        }

        if ($agreementDTO->getFileName() === ''){
            $agreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу.'
            );

            return $agreementDTO;

        }

        Redis::set($key, json_encode(['0' => $agreementDTO->getFileName()]), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 3);

        $agreementDTO->setMessage(
            'Бажаєте завантажити додаткові фото витягу з ЄДР?. 📎'
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
