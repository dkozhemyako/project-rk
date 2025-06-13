<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\EqTypeClientEnum;
use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Repositories\ClientAgreement\DTO\ClientAgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientTypeHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_TYPE = '_CLIENT_TYPE';


    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => TypeClientEnum::FOP->value,
                        ],
                        [ //кнопка
                            'text' => TypeClientEnum::FO->value,
                        ],

                    ],
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

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_TYPE;

        if (Redis::get($agreementDTO->getSenderId()) == 3
            && $agreementDTO->getMessage() == TelegramCommandEnum::agreementBack->value)
        {
            Redis::del(
                $agreementDTO->getSenderId() . FopSaveFileEdrHandler::SAVE_FILE_FOP_EDR,
                $agreementDTO->getSenderId() . FopSaveFileEdrHandler::MEDIA_FILE_FOP_EDR,
                $agreementDTO->getSenderId() . CheckFopSaveFileEdrHandler::CHECK_SAVE_FILE_FOP_EDR,
                $agreementDTO->getSenderId() . FoSaveFilePas1stHandler::SAVE_FILE_FO_PAS_1ST,


            );
            Redis::set($agreementDTO->getSenderId(), 2);

            $typeClient = TypeClientEnum::tryFrom(Redis::get($key));

            if ($typeClient === TypeClientEnum::FOP){
                $agreementDTO->setMessage(
                    'Завантажте витяг з ЄДР 📎'
                );
                $agreementDTO->setReplyMarkup($this->replyMarkup());
            }

            if ($typeClient === TypeClientEnum::FO){
                $agreementDTO->setMessage(
                    'Завантажте фото першої сторінки паспорту 📎'
                );
                $agreementDTO->setReplyMarkup($this->replyMarkup());
            }

            return $agreementDTO;
        }

        if (Redis::exists($key) == true){

            $agreementDTO->setClientAgreementDTO(new ClientAgreementDTO());
            $agreementDTO->getClientAgreementDTO()->setEqType(EqTypeClientEnum::from(Redis::get($agreementDTO->getSenderId() . AgreementTypeHandler::AGR_STAGE_AGR_TYPE)));
            $agreementDTO->getClientAgreementDTO()->setType(TypeClientEnum::from(Redis::get($key)));
            $agreementDTO->getClientAgreementDTO()->setTelegramId($agreementDTO->getSenderId());
            return $next($agreementDTO);
        }

        $checkValue = TypeClientEnum::tryFrom($agreementDTO->getMessage());

        if (is_null($checkValue) == true) {

            $agreementDTO->setMessage('🤦 Помилка вводу. Оберіть значення з меню 👇');
            $agreementDTO->setReplyMarkup($this->replyMarkup);
            return $agreementDTO;
        }
        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        Redis::set($agreementDTO->getSenderId(), 2);

        if ($agreementDTO->getMessage() === TypeClientEnum::FOP->value){
            $agreementDTO->setMessage(
                'Завантажте витяг з ЄДР 📎'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
        }

        if ($agreementDTO->getMessage() === TypeClientEnum::FO->value){
            $agreementDTO->setMessage(
                'Завантажте фото першої сторінки паспорту 📎'
            );
            $agreementDTO->setReplyMarkup($this->replyMarkup());
        }

        return $agreementDTO;
    }
    private function replyMarkup(): array
    {
        return
            [
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
