<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementFalse\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\CheckAdminCreateAgreement\CheckAdminCreateAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\ClientCheckAgreementFalseInterface;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\DTO\ClientCheckAgreementFalseDTO;
use Closure;
use Illuminate\Support\Facades\Redis;

class GetMessageWhyFalseHandler implements ClientCheckAgreementFalseInterface
{
    public function __construct(
        protected CheckAdminCreateAgreementRepository $repository,
        protected TelegramMessengerService $messengerService,
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
    ){}

    public function handle(ClientCheckAgreementFalseDTO $agreementFalseDTO, Closure $next): ClientCheckAgreementFalseDTO
    {
        $clientInfo = $this->clientAgreementRepository->getClientFilesById($agreementFalseDTO->getCallback());
        $adminInfo = $this->repository->checkAdmin($agreementFalseDTO->getCallback());
        $messageAdm  = '❗ По завданню № ' . $agreementFalseDTO->getCallback().'('.$clientInfo->getName().')'. ' клієнт залишив коментар до договору:' .PHP_EOL.PHP_EOL;
        $messageAdm .= $agreementFalseDTO->getMessage();

        $adminMsg = new MessageDTO(
            $messageAdm,
            $adminInfo->getTelegramId(),
        );

        $adminMsg->setReplyMarkup($this->getAdminReplyMarkup($agreementFalseDTO->getCallback()));

        $this->messengerService->send($adminMsg);

        $fileInfo = $this->adminAgreementRepository->getClientInfoForFilesById($agreementFalseDTO->getCallback());

        $arrayQuery = array(
            'chat_id' => $adminInfo->getTelegramId(),
            'caption' => 'Драфт договору',
            'document' => curl_file_create(storage_path('app/public/'.$fileInfo->getFileDraftAgreement()))
        );
        $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);

        $agreementFalseDTO->setMessage(
            '💬 Інформація прийнята та відправлена орендодавцю, очікуйте на відповідь або оновлений договір.',
        );

        $agreementFalseDTO->setReplyMarkup($this->getReplyMarkup());
        Redis::del($agreementFalseDTO->getSenderId() . PreparatoryHandler::KEY_CLI_AGR_FALSE);
        return $agreementFalseDTO;



    }
    private function getReplyMarkup(): array
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
    private function getAdminReplyMarkup(int $callback): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::adminSignedAgreement->value,
                                'callback_data' => $callback,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }
}
