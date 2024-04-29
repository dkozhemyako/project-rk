<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class FoSaveFilePas1stHandler implements AgreementInterface
{
    public const SAVE_FILE_FO_PAS_1ST = '_FO_PAS_1ST_FILE';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::SAVE_FILE_FO_PAS_1ST;

        if (Redis::get($agreementDTO->getSenderId()) == 100
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value) {
            Redis::del(
                $agreementDTO->getSenderId() . FoSaveFilePas2ndHandler::SAVE__NEW_FILE_FO_PAS_2ND,
            );
            Redis::set($agreementDTO->getSenderId(), 3);

            $agreementDTO->setMessage(
                'Завантажте фото другої сторінки паспорту 📎'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());

            return $agreementDTO;
        }

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
        Redis::set($agreementDTO->getSenderId(), 3);

        $agreementDTO->setMessage(
            'Завантажте фото другої сторінки паспорту 📎'
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
