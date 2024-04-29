<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class EquipmentAddressAdditionalHandler implements AgreementInterface
{

    public const AGR_STAGE_EQUIP_ADD = '_EQUIP_ADD';


    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_EQUIP_ADD;

        if (Redis::exists($key) == true){

            $agreementDTO->setMessage(
                '❗ Договір вже сформовано і передано орендодавцю для перевірки та заповнення даних про обладнання.'.PHP_EOL.
                'Невдовзі ми відправимо або вже відправили (дивіться вище) договір оренди для ознайомлення. Підтвердиити його необхідно за допомогою кнопки "Приймаю умови договору".'
            );

            $agreementDTO->setReplyMarkup($this->replyMarkup());

            return $agreementDTO;

        }

        if ($agreementDTO->getMessage() === ' '){
            $agreementDTO->setMessage(
                '🤦 Помилка вводу. Додаткова інформація повинна містити як мінімум інформацію про площу приміщення. Будь ласка введіть дані повторно.'
            );
        }

        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);
        $agreementDTO->setMessage(
            '💬 Дякуємо за надану інформацію , договір передано орендодавцю для перевірки та заповнення даних про обладнання.'.PHP_EOL.
            'Невдовзі ми відправимо Вам заповнений та підписаний договір оренди для ознайомлення та підписання за допомогою "Дія підпису".'
        );
        $agreementDTO->setReplyMarkup($this->replyMarkup());

        if (Redis::exists($key) == true){

            $agreementDTO->getClientAgreementDTO()->setEquipRegion(Redis::get($agreementDTO->getSenderId(). EquipmentAddressRegionHandler::AGR_STAGE_EQUIP_REGION));
            $agreementDTO->getClientAgreementDTO()->setEquipTown(Redis::get($agreementDTO->getSenderId(). EquipmentAddressTownHandler::AGR_STAGE_EQUIP_TOWN));
            $agreementDTO->getClientAgreementDTO()->setEquipStreet(Redis::get($agreementDTO->getSenderId(). EquipmentAddressStreetHandler::AGR_STAGE_EQUIP_STREET));
            $agreementDTO->getClientAgreementDTO()->setEquipHouse(Redis::get($agreementDTO->getSenderId(). EquipmentAddressHouseHandler::AGR_STAGE_EQUIP_HOUSE));
            $agreementDTO->getClientAgreementDTO()->setEquipAddressAdd(Redis::get($agreementDTO->getSenderId(). EquipmentAddressAdditionalHandler::AGR_STAGE_EQUIP_ADD));

        }

        return $next($agreementDTO);
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
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
