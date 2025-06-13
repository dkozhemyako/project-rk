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
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXC68LCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXC68LHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXC68LVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW100LCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW100LHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW100LVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120CUBECharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120CUBEHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120CUBEVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120LSCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120LSHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW120LSVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160CUBECharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160CUBEHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160CUBEVideoHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160LSCharacteristicsHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160LSHandler;
use App\Services\Telegram\Handlers\EquipmentHandler\EquipmentGooderXCW160LSVideoHandler;
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
            TelegramCommandEnum::clientCheckAgreementTrue => app(ClientAgreementHandler::class),
            TelegramCommandEnum::adminSignedAgreement => app(AdminSignedAgreementHandler::class),
            TelegramCommandEnum::clientCheckAgreementFalse => app(ClientCheckAgreementFalseHandler::class),

            TelegramCommandEnum::equipmentGooderXC68L, TelegramCommandEnum::equipmentGooderXC68LPhoto => app(EquipmentGooderXC68LHandler::class),
            TelegramCommandEnum::equipmentGooderXC68LCharacteristics => app(EquipmentGooderXC68LCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXC68LVideo =>app(EquipmentGooderXC68LVideoHandler::class),
            TelegramCommandEnum::equipmentGooderXCW100L, TelegramCommandEnum::equipmentGooderXCW100LPhoto => app(EquipmentGooderXCW100LHandler::class),
            TelegramCommandEnum::equipmentGooderXCW100LCharacteristics => app(EquipmentGooderXCW100LCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXCW100LVideo =>app(EquipmentGooderXCW100LVideoHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120LS, TelegramCommandEnum::equipmentGooderXCW120LSPhoto => app(EquipmentGooderXCW120LSHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120LSCharacteristics => app(EquipmentGooderXCW120LSCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120LSVideo =>app(EquipmentGooderXCW120LSVideoHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160CUBE, TelegramCommandEnum::equipmentGooderXCW160CUBEPhoto => app(EquipmentGooderXCW160CUBEHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160CUBECharacteristics => app(EquipmentGooderXCW160CUBECharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160CUBEVideo =>app(EquipmentGooderXCW160CUBEVideoHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160LS, TelegramCommandEnum::equipmentGooderXCW160LSPhoto => app(EquipmentGooderXCW160LSHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160LSCharacteristics => app(EquipmentGooderXCW160LSCharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXCW160LSVideo =>app(EquipmentGooderXCW160LSVideoHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120CUBE, TelegramCommandEnum::equipmentGooderXCW120CUBEPhoto => app(EquipmentGooderXCW120CUBEHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120CUBECharacteristics => app(EquipmentGooderXCW120CUBECharacteristicsHandler::class),
            TelegramCommandEnum::equipmentGooderXCW120CUBEVideo =>app(EquipmentGooderXCW120CUBEVideoHandler::class),
        };
    }
}
