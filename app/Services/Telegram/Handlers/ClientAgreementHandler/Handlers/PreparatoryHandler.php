<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentConditionHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentModelHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementEquipmentRentCostHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\AdminAgreementStartDateHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\CreateAdminAgreementHandler;
use App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers\StoreAdminAgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\FinalAgreementInterface;
use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class PreparatoryHandler implements FinalAgreementInterface
{

    public function __construct(
        protected TelegramMessengerService $messengerService,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected AdminAgreementRepository $adminAgreementRepository,
    ){}
    public const KEY_FINAL_CALLBACK = '_FINAL_CALLBACK';

    public function handle(FinalAgreementDTO $finalAgreementDTO, Closure $next): FinalAgreementDTO
    {
        $key = $finalAgreementDTO->getSenderId() . self::KEY_FINAL_CALLBACK;
        if($finalAgreementDTO->getMessage() === TelegramCommandEnum::clientCheckAgreementTrue->value){

            $senderId = $finalAgreementDTO->getSenderId();

                Redis::del(
                    $senderId . $key,
            );

            Redis::set($key, $finalAgreementDTO->getCallback(), 'EX', 260000);

            $message = '💬 Завантажте підписаний договір наступним повідомленням.' . PHP_EOL;
            $finalAgreementDTO->setMessage($message);
            //
            $arrayQuery1 = array(
                'chat_id' => $finalAgreementDTO->getSenderId(),
                'text' => "💬 Чудово, наступний крок. На договір, який ви перевірили, потрібно накласти підпис за допомогою Дія. Відеоінструкція нижче.",
                'parse_mode' => 'HTML',
            );

            $ch1 = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendMessage');
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $arrayQuery1);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch1);
            curl_close($ch1);
            //

            $clientInstructionMsg = new MessageDTO(
                "<a href='https://youtu.be/oebQEaliqjY'>Посилання на відео інструкцію підпису</a>",
                $finalAgreementDTO->getSenderId(),
            );
            $clientInstructionMsg->setParseMode('HTML');
            $clientInstructionMsg->setReplyMarkup($this->replyMarkupMain());
            $this->messengerService->send($clientInstructionMsg);
            //

            $files1stPartData = $this->clientAgreementRepository->getClientFilesById($finalAgreementDTO->getCallback());
            $files2ndPartData = $this->adminAgreementRepository->getClientInfoForFilesById($finalAgreementDTO->getCallback());

            $path = $files1stPartData->getName(). ' ' . $files1stPartData->getPhone() . '/';
            $path .= $files1stPartData->getEquipTown(). ' ' . $files1stPartData->getEquipStreet() . ' ' . $files1stPartData->getEquipHouse(). '/';
            $path .= $files2ndPartData->getEquipmentModel();



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
                        Storage::disk('public')->move($newValue, $path.'/'.$newValue);
                    }
                }
                if (is_array($value) === false && $value != null){
                    Storage::disk('public')->move($value, $path.'/'.$value);
                }
            }

            return $finalAgreementDTO;
        }

        $finalAgreementDTO->setCallback(Redis::get($key));

        return $next($finalAgreementDTO);
    }

    private function replyMarkupMain(): array
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
