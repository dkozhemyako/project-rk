<?php

namespace App\Services\Telegram;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementHandler;
use App\Services\Telegram\Handlers\AdminSignedAgreementHandler\AdminSignedAgreementHandler;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\ClientAgreementHandler;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\ClientCheckAgreementFalseHandler;
use App\Services\Telegram\Handlers\ClientCheckAgreementTrue\ClientCheckAgreementTrueHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentRentalConditionsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentWaitHandler;
use App\Services\Telegram\Handlers\StartHandler;

class CommandsFactory
{
    public function handle(TelegramCommandEnum $commandEnum): CommandsInterface
    {
        return match ($commandEnum) {
            TelegramCommandEnum::start, TelegramCommandEnum::returnMain => app(StartHandler::class),
            TelegramCommandEnum::agreement => app(AgreementHandler::class),
            TelegramCommandEnum::adminAgreement =>app(AdminAgreementHandler::class),
            TelegramCommandEnum::clientAgreement =>app(ClientAgreementHandler::class),
            TelegramCommandEnum::equipment, TelegramCommandEnum::equipmentBack => app(EquipmentHandler::class),
            TelegramCommandEnum::equipmentWait => app(EquipmentWaitHandler::class),
            TelegramCommandEnum::equipmentFrosty75l, TelegramCommandEnum::equipmentFrosty75lPhoto => app(EquipmentFrosty78LHandler::class),
            TelegramCommandEnum::equipmentFrosty75lVideo =>app(EquipmentFrosty78LVideoHandler::class),
            TelegramCommandEnum::equipmentFrosty75lCharacteristics => app(EquipmentFrosty78LCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentRentalConditions => app(EquipmentRentalConditionsHandler::class),
            TelegramCommandEnum::clientCheckAgreementTrue => app(ClientCheckAgreementTrueHandler::class),
            TelegramCommandEnum::adminSignedAgreement => app(AdminSignedAgreementHandler::class),
            TelegramCommandEnum::clientCheckAgreementFalse => app(ClientCheckAgreementFalseHandler::class),
        };
    }
}
