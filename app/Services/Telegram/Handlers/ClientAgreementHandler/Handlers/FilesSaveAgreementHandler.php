<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers;



use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\GoogleDrive\GDriveCreateFolderService;
use App\Services\GoogleDrive\GDriveSendDocumentService;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\FinalAgreementInterface;
use App\Services\Telegram\Handlers\ClientCheckAgreementTrue\ClientCheckAgreementTrueHandler;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class FilesSaveAgreementHandler implements FinalAgreementInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
        protected GDriveSendDocumentService $driveSendDocumentService,
    ){}


    public function handle(FinalAgreementDTO $finalAgreementDTO , Closure $next): FinalAgreementDTO
    {

        $files1stPartData = $this->clientAgreementRepository->getClientFilesById($finalAgreementDTO->getCallback());
        $files2ndPartData = $this->adminAgreementRepository->getClientInfoForFilesById($finalAgreementDTO->getCallback());

        $path = $files1stPartData->getName(). ' ' . $files1stPartData->getPhone() . '/';
        $path .= $files1stPartData->getEquipTown(). ' ' . $files1stPartData->getEquipStreet() . ' ' . $files1stPartData->getEquipHouse(). '/';
        $path .= $files2ndPartData->getEquipmentModel();

        $pathArray = [
            $files1stPartData->getName(). ' ' . $files1stPartData->getPhone(),
            $files1stPartData->getEquipTown(). ' ' . $files1stPartData->getEquipStreet(),
            $files2ndPartData->getEquipmentModel(),
        ];

        $parent = Redis::get($finalAgreementDTO->getSenderId() . ClientCheckAgreementTrueHandler::GD_PARENT_FILES);

        $files = [
            $files1stPartData->getFileSignedAgreement(),
            $files2ndPartData->getFileSignedAgreement(),
        ];

        foreach ($files as $value){
            if ($value != null){
                $this->driveSendDocumentService->handle($value, $parent);

            }
        }

        foreach ($files as $value){
            if ($value != null){

                Storage::disk('public')->move($value, $path.'/'.$value);
            }
        }

        return $finalAgreementDTO;

    }


}
