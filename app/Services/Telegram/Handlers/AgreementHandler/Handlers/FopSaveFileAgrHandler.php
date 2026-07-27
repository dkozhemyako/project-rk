<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\FilesDownloadEnum;
use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class FopSaveFileAgrHandler implements AgreementInterface
{
    public const SAVE_FILE_FOP_AGR = '_FOP_AGR_FILE';
    public const MEDIA_FILE_FOP_AGR = '_MEDIA_FILE_FOP_AGR';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if ($agreementDTO->getMediaGroupId() != 0){
            Redis::set($agreementDTO->getSenderId() . self::MEDIA_FILE_FOP_AGR, 'check' , 'EX', 260000);
        }
        $key = $agreementDTO->getSenderId() . self::SAVE_FILE_FOP_AGR;

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            return $next($agreementDTO);
        }


        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setFileFopEdr(Redis::get($agreementDTO->getSenderId().FopSaveFileEdrHandler::SAVE_FILE_FOP_EDR));
            $agreementDTO->getClientAgreementDTO()->setFileFopAgrRent(Redis::get($key));

            return $next($agreementDTO);
        }

        if ($agreementDTO->getFileName() === ''){
            $agreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу'
            );

            return $agreementDTO;
        }

        Redis::set(
            $key,
            json_encode(
                [$agreementDTO->getFileName()],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            'EX',
            260000
        );

        $agreementDTO->setMessage(
            'Бажаєте завантажити додаткові фото договору оренди або права власності приміщення або талон на МАФ? 📎'
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
