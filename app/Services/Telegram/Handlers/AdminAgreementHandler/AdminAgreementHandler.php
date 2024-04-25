<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeGrinderConditionHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeGrinderCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeGrinderModelHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeMachineConditionHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeMachineCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementCoffeeMachineModelHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentConditionHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentModelHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentRentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementStartDateHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\CreateAdminAgreementHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\GetAdminDraftAgreementHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\PreparatoryHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\StoreAdminAgreementHandler;
use Illuminate\Pipeline\Pipeline;

class AdminAgreementHandler implements CommandsInterface
{
    public const HANDLERS =
        [
            PreparatoryHandler::class,
            AdminAgreementStartDateHandler::class,
            //
            AdminAgreementEquipmentModelHandler::class,
            AdminAgreementEquipmentConditionHandler::class,
            AdminAgreementEquipmentCostHandler::class,
            //
            AdminAgreementCoffeeMachineModelHandler::class,
            AdminAgreementCoffeeMachineConditionHandler::class,
            AdminAgreementCoffeeMachineCostHandler::class,
            //
            AdminAgreementCoffeeGrinderModelHandler::class,
            AdminAgreementCoffeeGrinderConditionHandler::class,
            AdminAgreementCoffeeGrinderCostHandler::class,
            //
            AdminAgreementEquipmentRentCostHandler::class,
            CreateAdminAgreementHandler::class,
            StoreAdminAgreementHandler::class,
            GetAdminDraftAgreementHandler::class,
        ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $dto = new AdminAgreementDTO(
            $callback,
            $message,
            $senderId,
            $fileName
        );

        $result = $this->pipeline
            ->send($dto)
            ->through(self::HANDLERS)
            ->thenReturn();

        $messageDTO = new MessageDTO(
            $result->getMessage(),
            $senderId,
        );

        $messageDTO->setReplyMarkup($result->getReplyMarkup());

        return $messageDTO;
    }
}
