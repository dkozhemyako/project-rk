<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementTrue;

use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\CheckAdminCreateAgreement\CheckAdminCreateAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\GoogleDrive\GDriveCreateFolderService;
use App\Services\GoogleDrive\GDriveSendDocumentService;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;



class ClientCheckAgreementTrueHandler implements CommandsInterface
{


    public function __construct(
        protected CheckAdminCreateAgreementRepository $repository,
        protected AdminAgreementRepository $adminAgreementRepository,
        protected TelegramMessengerService $messengerService,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected GDriveCreateFolderService $driveCreateFolderService,
        protected GDriveSendDocumentService $driveSendDocumentService,
    ) {
    }

    public const GD_PARENT_FILES = '_GD_PARENT_FILES';

    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $key = $senderId . self::GD_PARENT_FILES;
        if ($fileName != ''){
            $msg = '🤦 Сталася помилка. Ми очікуємо підтвердження перевірки вами договору '.PHP_EOL;
            $msg .= 'Кнопка підтвердження була відправлена вам разом з договором для ознайомлення. 👆';

            Storage::disk('public')->delete($fileName);
            return new MessageDTO(
                $msg,
                $senderId
            );
        }

        if (Redis::exists($key) == true){
            $msg = '🤦Ви повторно натиснули кнопку підтвердження, договір підтверджено ще при першій спробі і інформація відправлена орендодавцю, очікуйте підписаний орендодавцем договір і подальші інструкції.'.PHP_EOL;
            return new MessageDTO(
                $msg,
                $senderId
            );
        }

        $dto = new AdminAgreementDTO(
            $callback,
            $message,
            $senderId,
            $fileName
        );

        $adminId = $this->repository->checkAdmin($dto->getCallback());
        $clientInfo = $this->clientAgreementRepository->getClientFilesById($dto->getCallback());

        if ($adminId->getTelegramId() != null){
            $message = '💬 По завданню № '. $callback .'('.$clientInfo->getName().')'. ' клієнт ознайомився з договром, перевірив дані та підтвердив що, все вказано вірно'.PHP_EOL.PHP_EOL;
            $message .= 'Підпишіть договір та натисніть кнопку продовжити під цим повідомленням, необхідно буде відправити файл з підписом .P7S';
            $messageForAdmin = new MessageDTO(
                $message,
                $adminId->getTelegramId(),
            );
            $messageForAdmin->setReplyMarkup($this->getAdminReplyMarkup($dto->getCallback()));
            $this->messengerService->send($messageForAdmin);

            $adminInfo = $this->adminAgreementRepository->getClientInfoForFilesById($dto->getCallback());

            $arrayQuery = array(
                'chat_id' => $adminId->getTelegramId(),
                'caption' => 'Договір для підпису',
                'document' => curl_file_create(storage_path('app/public/'.$adminInfo->getFileDraftAgreement()))
            );
            $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            curl_close($ch);

        }

        $messageForClient = new MessageDTO(
            '💬 Дякую, інформація відправлена орендодавцю, очікуйте підписаний орендодавцем договір і подальші інструкції.',
            $dto->getSenderId(),

        );

        $messageForClient->setReplyMarkup($this->getClientReplyMarkup());

        $files1stPartData = $this->clientAgreementRepository->getClientFilesById($dto->getCallback());
        $files2ndPartData = $this->adminAgreementRepository->getClientInfoForFilesById($dto->getCallback());

        $path = $files1stPartData->getName(). ' ' . $files1stPartData->getPhone() . '/';
        $path .= $files1stPartData->getEquipTown(). ' ' . $files1stPartData->getEquipStreet() . ' ' . $files1stPartData->getEquipHouse(). '/';
        $path .= $files2ndPartData->getEquipmentModel();

        $pathArray = [
            $files1stPartData->getName(). ' ' . $files1stPartData->getPhone(),
            $files1stPartData->getEquipTown(). ' ' . $files1stPartData->getEquipStreet() . ' ' . $files1stPartData->getEquipHouse(),
            $files2ndPartData->getEquipmentModel(),
        ];

        $parent = $this->driveCreateFolderService->createFolder($pathArray);
        Redis::set($key, $parent, 'EX', 86400);

        $files = [
            json_decode($files1stPartData->getFileFopEdr()),
            json_decode($files1stPartData->getFileFopAgrRent()),
            json_decode($files1stPartData->getFileFoAgrRent()),

            $files1stPartData->getFileFoPas1st(),
            $files1stPartData->getFileFoPas2nd(),
            $files1stPartData->getFileFoPasReg(),
            $files1stPartData->getFileDraftAgreement(),
            $files2ndPartData->getFileAgreement(),
            $files2ndPartData->getFileDraftAgreement(),
        ];

        foreach ($files as $value){
            if (is_array($value) === true && $value != null){
                foreach ($value as $newKey => $newValue){
                    $check = $this->driveSendDocumentService->handle($newValue, $parent);
                    if ($check === 'error'){
                        $this->driveSendDocumentService->handle($newValue, $parent, $path);
                    }
                }
            }
            if (is_array($value) === false && $value != null){
                $check = $this->driveSendDocumentService->handle($value, $parent);
                if ($check === 'error'){
                    $this->driveSendDocumentService->handle($value, $parent, $path);
                }
            }
        }

        foreach ($files as $value){
            if (is_array($value) === true && $value != null){
                foreach ($value as $newKey => $newValue){
                    Storage::disk('public')->move($newValue, $path.'/'.$newValue);
                }
            }
            if (is_array($value) === false && $value != null){
                Storage::disk('public')->move($value, $path.'/'.$value);
            }
        }

        return $messageForClient;

    }

    private function getAdminReplyMarkup(int $agreementId): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::adminSignedAgreement->value,
                                'callback_data' => $agreementId,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }

    private function getClientReplyMarkup(): array
    {
        return
            [
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
