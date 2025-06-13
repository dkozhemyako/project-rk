<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler;

use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\ClientHandlerAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers\ClientCheckAgreementTrueHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers\FilesSaveAgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers\GetSignetAgreementHandler;
use App\Services\Telegram\Handlers\ClientAgreementHandler\Handlers\PreparatoryHandler;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Storage;


class ClientAgreementHandler implements CommandsInterface
{
    public const HANDLERS =
        [
            PreparatoryHandler::class,
            GetSignetAgreementHandler::class,
            FilesSaveAgreementHandler::class,
        ];
    public function __construct(
        protected Pipeline $pipeline,
        protected ClientAgreementRepository $clientAgreementRepository,
    ) {
    }
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {

        if ($callback == 0){
            $agreementId = $this->clientAgreementRepository
                ->getLatestUnsignedAgreementIdByTelegramId($senderId);

            if (!$agreementId) {
                Storage::disk('public')->delete($fileName);
                return new MessageDTO('⚠️ Нажаль система не знайшла договору який очікує підтвердження. Можливо Ви вже підтвердили коректність договору.', $senderId);
            }
            $callback = $agreementId;
        }

        $dto = new FinalAgreementDTO(
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
