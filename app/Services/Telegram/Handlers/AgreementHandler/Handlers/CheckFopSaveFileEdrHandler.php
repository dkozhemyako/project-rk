<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\FilesDownloadEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CheckFopSaveFileEdrHandler implements AgreementInterface
{
    public const CHECK_SAVE_FILE_FOP_EDR = '_CHECK_FOP_EDR_FILE';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            return $next($agreementDTO);
        }

        $key = $agreementDTO->getSenderId() . self::CHECK_SAVE_FILE_FOP_EDR;

        if (Redis::exists($key) == true){
            return $next($agreementDTO);
        }


        if ($agreementDTO->getMessage() === FilesDownloadEnum::NO->value){
            Redis::set($key, 'check', 'EX', 260000);
            $agreementDTO->setMessage(
                'Завантажте фото договору оренди або права власності або талон на МАФ. 📎'
            );
            Redis::del($agreementDTO->getSenderId() . FopSaveFileEdrHandler::MEDIA_FILE_FOP_EDR);
            return $agreementDTO;
        }

        if ($agreementDTO->getMessage() === FilesDownloadEnum::YES->value) {
            $agreementDTO->setMessage(
                'Завантажте додаткові файли витягу з ЄДР. 📎 '
            );
            Redis::del($agreementDTO->getSenderId() . FopSaveFileEdrHandler::MEDIA_FILE_FOP_EDR);
            return $agreementDTO;
        }

        if ($agreementDTO->getFileName() === ''){
            $agreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу.'
            );

            return $agreementDTO;
        }

        $redisKey = $agreementDTO->getSenderId().FopSaveFileEdrHandler::SAVE_FILE_FOP_EDR;

        $data = json_decode(Redis::get($redisKey), true);
        $data[] = $agreementDTO->getFileName();
        Redis::set($redisKey, json_encode($data), 'EX', 260000);

        if (Redis::exists($agreementDTO->getSenderId() . FopSaveFileEdrHandler::MEDIA_FILE_FOP_EDR) == false){
            $agreementDTO->setMessage(
                'Бажаєте завантажити додаткові фото витягу з ЄДР?. 📎'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());

            return $agreementDTO;
        }

        $agreementDTO->setMessage(
            '👇',
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
