<?php

namespace App\Services\Telegram\Handlers\AgreementHandler;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\AgreementStartDateClient;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\AgreementTypeHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\CheckSaveFileAgrHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\CheckFopSaveFileEdrHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientAddressFlatHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientAddressHouseHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientAddressRegionHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientAddressStreetHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientAddressTownHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientNameHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientPhoneHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientRegisterNumberHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\ClientTypeHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\CreateAgreementDraftHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\EquipmentAddressAdditionalHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\EquipmentAddressHouseHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\EquipmentAddressRegionHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\EquipmentAddressStreetHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\EquipmentAddressTownHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FopRegisterDateHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FopRegisterNumberHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FopSaveFileAgrHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FopSaveFileEdrHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FoSaveFilePas1stHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FoSaveFilePas2ndHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FoSaveFilePas3thHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\FoSaveFilePasAgrHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\PassportDateHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\PassportIssuedHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\PassportNumberHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\PreparatoryHandler;
use App\Services\Telegram\Handlers\AgreementHandler\Handlers\StoreClientAgreementHandler;
use Illuminate\Pipeline\Pipeline;


class AgreementHandler implements CommandsInterface
{
    public const HANDLERS = [
        PreparatoryHandler::class,
        //
        AgreementTypeHandler::class,
        ClientTypeHandler::class,
        //
        FopSaveFileEdrHandler::class,
        CheckFopSaveFileEdrHandler::class,
        FopSaveFileAgrHandler::class,
        //
        FoSaveFilePas1stHandler::class,
        FoSaveFilePas2ndHandler::class,
        FoSaveFilePas3thHandler::class,
        FoSaveFilePasAgrHandler::class,
        //
        CheckSaveFileAgrHandler::class,
        //
        AgreementStartDateClient::class,
        //
        ClientNameHandler::class,
        ClientPhoneHandler::class,
        //
        FopRegisterNumberHandler::class,
        FopRegisterDateHandler::class,
        //
        PassportNumberHandler::class,
        PassportIssuedHandler::class,
        PassportDateHandler::class,
        //
        ClientRegisterNumberHandler::class,
        //
        ClientAddressRegionHandler::class,
        ClientAddressTownHandler::class,
        ClientAddressStreetHandler::class,
        ClientAddressHouseHandler::class,
        ClientAddressFlatHandler::class,
        //
        EquipmentAddressRegionHandler::class,
        EquipmentAddressTownHandler::class,
        EquipmentAddressStreetHandler::class,
        EquipmentAddressHouseHandler::class,
        EquipmentAddressAdditionalHandler::class,
        //
        CreateAgreementDraftHandler::class,
        StoreClientAgreementHandler::class,

    ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $dto = new AgreementDTO(
            $message,
            $senderId,
            $fileName,
        );
        $dto->setMediaGroupId($mediaGroupId);


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
