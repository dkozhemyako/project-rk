<?php

namespace App\Services\Telegram;

use App\Enums\TelegramCommandEnum;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementHandler;
use App\Services\Telegram\Handlers\AdminSignedAgreementHandler\AdminSignedAgreementHandler;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\ClientAgreementHandler;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\ClientCheckAgreementFalseHandler;
use App\Services\Telegram\Handlers\ClientCheckAgreementTrue\ClientCheckAgreementTrueHandler;
use App\Services\Telegram\Handlers\CoffeeGrinderHandler\CoffeeGrinderFiorenzatoF64Handler;
use App\Services\Telegram\Handlers\CoffeeGrinderHandler\CoffeeGrinderHandler;
use App\Services\Telegram\Handlers\CoffeeGrinderHandler\EquipmentFiorenzatoF64CharacteristicsHandler;
use App\Services\Telegram\Handlers\CoffeeMachineHandler\CoffeeMachineCassadioDieciHandler;
use App\Services\Telegram\Handlers\CoffeeMachineHandler\CoffeeMachineHandler;
use App\Services\Telegram\Handlers\CoffeeMachineHandler\EquipmentCassadioDieciCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrosty78LVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrostyRT98LCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrostyRT98LHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentFrostyRT98LVideoHandler;
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
            TelegramCommandEnum::agreement, TelegramCommandEnum::agreementBack  => app(AgreementHandler::class),
            TelegramCommandEnum::adminAgreement, TelegramCommandEnum::agreementAdminBack =>app(AdminAgreementHandler::class),
            TelegramCommandEnum::clientAgreement =>app(ClientAgreementHandler::class),
            TelegramCommandEnum::equipment, TelegramCommandEnum::equipmentBack => app(EquipmentHandler::class),
            TelegramCommandEnum::equipmentCm, TelegramCommandEnum::coffeeMachineBack => app(CoffeeMachineHandler::class),
            TelegramCommandEnum::equipmentCg, TelegramCommandEnum::coffeeGrinderBack => app(CoffeeGrinderHandler::class),
            TelegramCommandEnum::equipmentWait => app(EquipmentWaitHandler::class),
            TelegramCommandEnum::equipmentFrosty75l, TelegramCommandEnum::equipmentFrosty75lPhoto => app(EquipmentFrosty78LHandler::class),
            TelegramCommandEnum::equipmentCassadioDieci => app(CoffeeMachineCassadioDieciHandler::class),
            TelegramCommandEnum::equipmentFiorenzatoF64 => app(CoffeeGrinderFiorenzatoF64Handler::class),
            TelegramCommandEnum::equipmentFrostyRT98l, TelegramCommandEnum::equipmentFrostyRT98lPhoto => app(EquipmentFrostyRT98LHandler::class),
            TelegramCommandEnum::equipmentFrosty75lVideo =>app(EquipmentFrosty78LVideoHandler::class),
            TelegramCommandEnum::equipmentFrostyRT98LVideo =>app(EquipmentFrostyRT98LVideoHandler::class),
            TelegramCommandEnum::equipmentFrosty75lCharacteristics => app(EquipmentFrosty78LCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentFrostyRT98lCharacteristics => app(EquipmentFrostyRT98LCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentCassadioDieciCharacteristics => app(EquipmentCassadioDieciCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentFiorenzatoF64Characteristics => app(EquipmentFiorenzatoF64CharacteristicsHandler::class),
            TelegramCommandEnum::equipmentRentalConditions => app(EquipmentRentalConditionsHandler::class),
            TelegramCommandEnum::clientCheckAgreementTrue => app(ClientCheckAgreementTrueHandler::class),
            TelegramCommandEnum::adminSignedAgreement => app(AdminSignedAgreementHandler::class),
            TelegramCommandEnum::clientCheckAgreementFalse => app(ClientCheckAgreementFalseHandler::class),
        };
    }
}
