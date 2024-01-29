<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\DTO;

use App\Enums\TypeClientEnum;
use App\Repositories\ClientAgreement\DTO\ClientAgreementDTO;


class AgreementDTO
{
    protected string $dateFromClient;
    protected array $replyMarkup = [];
    protected int $mediaGroupId;
    protected ClientAgreementDTO $clientAgreementDTO;

    public function __construct(
        protected string $message,
        protected int $senderId,
        protected string $fileName,

    ){}

    /**
     * @param int $mediaGroupId
     */
    public function setMediaGroupId(int $mediaGroupId): void
    {
        $this->mediaGroupId = $mediaGroupId;
    }

    /**
     * @return int
     */
    public function getMediaGroupId(): int
    {
        return $this->mediaGroupId;
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
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getDateFromClient(): string
    {
        return $this->dateFromClient;
    }

    /**
     * @param string $dateFromClient
     */
    public function setDateFromClient(string $dateFromClient): void
    {
        $this->dateFromClient = $dateFromClient;
    }

    /**
     * @return array
     */
    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
    }

    /**
     * @param array $replyMarkup
     */
    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    /**
     * @return ClientAgreementDTO
     */
    public function getClientAgreementDTO(): ClientAgreementDTO
    {
        return $this->clientAgreementDTO;
    }

    /**
     * @param ClientAgreementDTO $clientAgreementDTO
     */
    public function setClientAgreementDTO(ClientAgreementDTO $clientAgreementDTO): void
    {
        $this->clientAgreementDTO = $clientAgreementDTO;
    }




}
