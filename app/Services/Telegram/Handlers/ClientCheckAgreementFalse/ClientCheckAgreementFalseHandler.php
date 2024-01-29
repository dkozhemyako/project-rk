<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementFalse;

use App\Services\Messenger\MessageDTO;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\DTO\ClientCheckAgreementFalseDTO;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\Handlers\GetMessageWhyFalseHandler;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\Handlers\PreparatoryHandler;
use Illuminate\Pipeline\Pipeline;

class ClientCheckAgreementFalseHandler implements CommandsInterface
{
    public const HANDLERS =
        [
            PreparatoryHandler::class,
            GetMessageWhyFalseHandler::class,
        ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $dto = new ClientCheckAgreementFalseDTO(
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
