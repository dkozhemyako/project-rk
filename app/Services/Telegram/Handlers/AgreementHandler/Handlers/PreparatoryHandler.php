<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class PreparatoryHandler implements AgreementInterface
{
    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '👨‍💻 Фізична особа-підприємець',
                        ],
                        [ //кнопка
                            'text' => '👨‍💼 Фізична особa',
                        ],

                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if($agreementDTO->getMessage() === TelegramCommandEnum::agreement->value){

            $senderId = $agreementDTO->getSenderId();

                Redis::del(
                    $senderId . ClientTypeHandler::AGR_STAGE_CLIENT_TYPE,
                    $senderId . FopSaveFileEdrHandler::SAVE_FILE_FOP_EDR,
                    $senderId . FopSaveFileAgrHandler::SAVE_FILE_FOP_AGR,
                    $senderId . FoSaveFilePas1stHandler::SAVE_FILE_FO_PAS_1ST,
                    $senderId . FoSaveFilePas2ndHandler::SAVE__NEW_FILE_FO_PAS_2ND,
                    $senderId . FoSaveFilePas3thHandler::SAVE_FILE_FO_PAS_3TH,
                    $senderId . FoSaveFilePasAgrHandler::SAVE_FILE_FO_AGR,
                    $senderId . AgreementStartDateClient::AGR_START_DATE_CLIENT,
                    $senderId . ClientNameHandler::AGR_STAGE_CLIENT_NAME,
                    $senderId . ClientPhoneHandler::AGR_STAGE_CLIENT_PHONE,
                    $senderId . FopRegisterNumberHandler::AGR_STAGE_FOP_REGISTER_NUMBER,
                    $senderId . FopRegisterDateHandler::AGR_STAGE_FOP_REGISTER_DATE,
                    $senderId . PassportNumberHandler::AGR_PASSPORT_NUMBER,
                    $senderId . PassportIssuedHandler::AGR_PASSPORT_ISSUED,
                    $senderId . PassportDateHandler::AGR_PASSPORT_DATE,
                    $senderId . ClientRegisterNumberHandler::AGR_STAGE_CLIENT_REG_NUMBER,
                    $senderId . ClientAddressRegionHandler::AGR_STAGE_CLIENT_REGION,
                    $senderId . ClientAddressTownHandler::AGR_STAGE_CLIENT_TOWN,
                    $senderId . ClientAddressStreetHandler::AGR_STAGE_CLIENT_STREET,
                    $senderId . ClientAddressHouseHandler::AGR_STAGE_CLIENT_HOUSE,
                    $senderId . ClientAddressFlatHandler::AGR_STAGE_CLIENT_FLAT,
                    $senderId . EquipmentAddressRegionHandler::AGR_STAGE_EQUIP_REGION,
                    $senderId . EquipmentAddressTownHandler::AGR_STAGE_EQUIP_TOWN,
                    $senderId . EquipmentAddressStreetHandler::AGR_STAGE_EQUIP_STREET,
                    $senderId . EquipmentAddressHouseHandler::AGR_STAGE_EQUIP_HOUSE,
                    $senderId . EquipmentAddressAdditionalHandler::AGR_STAGE_EQUIP_ADD,
                    $senderId . CheckFopSaveFileEdrHandler::CHECK_SAVE_FILE_FOP_EDR,
                    $senderId . CheckSaveFileAgrHandler::CHECK_SAVE_FILE_FOP_AGR,
            );

            $message = 'Для формування договору, нам необхідно отримати інформацію про орендаря.' . PHP_EOL;
            $message .= 'Оберіть організаційно-правову форму 👇';

            $agreementDTO->setMessage($message);
            $agreementDTO->setReplyMarkup($this->replyMarkup);
            return $agreementDTO;
        }

        return $next($agreementDTO);
    }


}
