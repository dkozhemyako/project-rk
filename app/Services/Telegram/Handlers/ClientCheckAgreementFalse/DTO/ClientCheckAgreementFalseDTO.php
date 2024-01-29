<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementFalse\DTO;

use App\Enums\EquipmentConditionEnum;

class ClientCheckAgreementFalseDTO
{
    protected array $replyMarkup = [];

    public function __construct(
        protected ? int $callback,
        protected string $message,
        protected int $senderId,
        protected string $fileName,
    ){}

    /**
     * @return array
     */
    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
    }

    /**
     * @param int|null $callback
     */
    public function setCallback(?int $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @return int|null
     */
    public function getCallback(): ?int
    {
        return $this->callback;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param array $replyMarkup
     */
    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }



}
