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

class CheckSaveFileAgrHandler implements AgreementInterface
{
    public const CHECK_SAVE_FILE_FOP_AGR = '_CHECK_FOP_AGR_FILE';

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {

        $key = $agreementDTO->getSenderId() . self::CHECK_SAVE_FILE_FOP_AGR;

        if (Redis::get($agreementDTO->getSenderId()) == 5
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . AgreementStartDateClient::AGR_START_DATE_CLIENT,
            );
            Redis::set($agreementDTO->getSenderId(), 4);

            $agreementDTO->setMessage(
                '💬 Вкажіть бажану дату встановлення обладнання в форматі 30.12.2025'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup(true));
            return $agreementDTO;
        }

        if (Redis::exists($key) == true){
            if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){

                $agreementDTO->getClientAgreementDTO()->setFileFopEdr(Redis::get($agreementDTO->getSenderId() . FopSaveFileEdrHandler::SAVE_FILE_FOP_EDR));
                $agreementDTO->getClientAgreementDTO()->setFileFopAgrRent(Redis::get($agreementDTO->getSenderId() . FopSaveFileAgrHandler::SAVE_FILE_FOP_AGR));
            }
            if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO) {

                $agreementDTO->getClientAgreementDTO()->setFileFoPass1st(Redis::get($agreementDTO->getSenderId() . FoSaveFilePas1stHandler::SAVE_FILE_FO_PAS_1ST));
                $agreementDTO->getClientAgreementDTO()->setFileFoPass2nd(Redis::get($agreementDTO->getSenderId() . FoSaveFilePas2ndHandler::SAVE__NEW_FILE_FO_PAS_2ND));
                $agreementDTO->getClientAgreementDTO()->setFileFoPassReg(Redis::get($agreementDTO->getSenderId() . FoSaveFilePas3thHandler::SAVE_FILE_FO_PAS_3TH));
                $agreementDTO->getClientAgreementDTO()->setFileFoAgrRent(Redis::get($agreementDTO->getSenderId() . FoSaveFilePasAgrHandler::SAVE_FILE_FO_AGR));
            }

            return $next($agreementDTO);

        }
        /*
        if (is_null(FilesDownloadEnum::tryFrom($agreementDTO->getMessage()))){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Оберіть значення з меню 👇'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
            return $agreementDTO;
        }
          */
        if ($agreementDTO->getMessage() === FilesDownloadEnum::NO->value){
            Redis::set($key, 'check', 'EX', 260000);
            $agreementDTO->setMessage(
                '💬 Вкажіть бажану дату встановлення обладнання в форматі 30.12.2025'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup(true));
            Redis::del($agreementDTO->getSenderId() . FopSaveFileAgrHandler::MEDIA_FILE_FOP_AGR);
            Redis::set($agreementDTO->getSenderId(), 4);
            return $agreementDTO;
        }

        if ($agreementDTO->getMessage() === FilesDownloadEnum::YES->value) {
            $agreementDTO->setMessage(
                'Завантажте додаткові файли договору оренди або права власності приміщення або талон на МАФ. 📎 '
            );
            Redis::del($agreementDTO->getSenderId() . FopSaveFileAgrHandler::MEDIA_FILE_FOP_AGR);
            return $agreementDTO;
        }

        if ($agreementDTO->getFileName() === ''){
            $agreementDTO->setMessage(
                '🤦 Ви не завантажили жодного документу, повторіть спробу.'
            );

            return $agreementDTO;
        }

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            $redisKey = $agreementDTO->getSenderId().FopSaveFileAgrHandler::SAVE_FILE_FOP_AGR;
        }
        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO) {
            $redisKey = $agreementDTO->getSenderId().FoSaveFilePasAgrHandler::SAVE_FILE_FO_AGR;
        }

        $data = json_decode(Redis::get($redisKey), true);
        $data[] = $agreementDTO->getFileName();
        Redis::set($redisKey, json_encode($data), 'EX', 260000);

        if (Redis::exists($agreementDTO->getSenderId() . FopSaveFileAgrHandler::MEDIA_FILE_FOP_AGR) == false){
            $agreementDTO->setMessage(
                'Бажаєте завантажити додаткові фото договору оренди або права власності або талон на МАФ? 📎'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());

            return $agreementDTO;
        }
        $agreementDTO->setMessage(
            '👇'
        );
        $agreementDTO->setReplyMarkup($this->replyMarkup());

        return $agreementDTO;
    }

    private function replyMarkup(bool $value = false): array
    {
        if ($value === true){
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
