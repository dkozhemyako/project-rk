<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class StoreClientAgreementHandler implements AgreementInterface
{

    public function __construct(
        protected ClientAgreementRepository $repository,
        protected TelegramMessengerService $messengerService,
    ){

    }

    /**
     * @throws GuzzleException
     */
    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $agreementId = $this->repository->store($agreementDTO->getClientAgreementDTO());
        if ($agreementId > 0){

            $message =
                '🔥 Запит формування договору, завдання #'.$agreementId.PHP_EOL.
                $agreementDTO->getClientAgreementDTO()->getName().PHP_EOL.
                '+380'.$agreementDTO->getClientAgreementDTO()->getPhone().PHP_EOL.
                'Область розміщення: '.$agreementDTO->getClientAgreementDTO()->getEquipRegion().PHP_EOL.
                'Місто розміщення: '.$agreementDTO->getClientAgreementDTO()->getEquipTown().PHP_EOL.
                'Адреса розміщення: '.$agreementDTO->getClientAgreementDTO()->getEquipStreet().', '.$agreementDTO->getClientAgreementDTO()->getEquipHouse() .PHP_EOL.
                'Дата встановлення: '.$agreementDTO->getClientAgreementDTO()->getDateFromClient().PHP_EOL.
                'Комплект: ' . $agreementDTO->getClientAgreementDTO()->getEqType()->value;

            $senderId = config('messenger.telegram.admin_id');
            $dto = new MessageDTO(
                $message,
                $senderId,
            );
            $dto->setReplyMarkup($this->replyMarkup($agreementId));

            $this->messengerService->send($dto);


            $array = ['Витяг з ЄДР' => json_decode($agreementDTO->getClientAgreementDTO()->getFileFopEdr())];
            $array['ФОП Приміщення'] = json_decode($agreementDTO->getClientAgreementDTO()->getFileFopAgrRent());
            $array['ФО Приміщення'] = json_decode($agreementDTO->getClientAgreementDTO()->getFileFoAgrRent());

            $array['Паспорт 1-ша сторінка'] = $agreementDTO->getClientAgreementDTO()->getFileFoPass1st();
            $array['Паспорт 2-га сторінка'] = $agreementDTO->getClientAgreementDTO()->getFileFoPass2nd();
            $array['Паспорт/витяг прописка'] = $agreementDTO->getClientAgreementDTO()->getFileFoPassReg();


            foreach ($array as $key => $value){
                if(is_array($value) === true && $value != null){
                    foreach ($value as $newKey => $newValue){
                        $arrayQuery = array(
                            'chat_id' => config('messenger.telegram.admin_id'),
                            'caption' => $key.'-'.$newKey,
                            'document' => curl_file_create(storage_path('app/public/'. $newValue)),

                        );

                        $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                }
                if (is_array($value) === false && $value != null){
                    $arrayQuery = array(
                        'chat_id' => config('messenger.telegram.admin_id'),
                        'caption' => $key,
                        'document' => curl_file_create(storage_path('app/public/'. $value)),

                    );

                    $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $arrayQuery = array(
                'chat_id' => config('messenger.telegram.admin_id'),
                'caption' => 'Договір DOCX для перевірки від клієнта',
                'document' => curl_file_create(storage_path('app/public/'.$agreementDTO->getClientAgreementDTO()->getFileDraftAgreement())),

            );

            $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            curl_close($ch);

            return $agreementDTO;
        }

        $agreementDTO->setMessage('Помилка вводу данних, спробуйте знову'.PHP_EOL.'/start');
        return $agreementDTO;
    }

    private function replyMarkup(int $agreementId): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::adminAgreement->value,
                                'callback_data' => $agreementId,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }


}
