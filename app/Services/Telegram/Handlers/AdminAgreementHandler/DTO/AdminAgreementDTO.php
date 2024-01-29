<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\DTO;

use App\Enums\EquipmentConditionEnum;

class AdminAgreementDTO
{
    protected array $replyMarkup = [];
    protected string $fileAgreementAdmin;
    protected string $fileDraftAgreementAdmin;
    protected string $dateFromAdmin;
    protected string $equipmentModel;
    protected EquipmentConditionEnum $equipmentCondition;
    protected int $equipmentCost;
    protected int $equipmentRentalCost;


    public function __construct(
        protected ? int $callback,
        protected string $message,
        protected int $senderId,
        protected string $fileName,
    ){}

    /**
     * @return string
     */
    public function getFileAgreementAdmin(): string
    {
        return $this->fileAgreementAdmin;
    }

    /**
     * @param string $fileAgreementAdmin
     */
    public function setFileAgreementAdmin(string $fileAgreementAdmin): void
    {
        $this->fileAgreementAdmin = $fileAgreementAdmin;
    }

    /**
     * @return string
     */
    public function getFileDraftAgreementAdmin(): string
    {
        return $this->fileDraftAgreementAdmin;
    }

    /**
     * @param string $fileDraftAgreementAdmin
     */
    public function setFileDraftAgreementAdmin(string $fileDraftAgreementAdmin): void
    {
        $this->fileDraftAgreementAdmin = $fileDraftAgreementAdmin;
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
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int|null
     */
    public function getCallback(): ?int
    {
        return $this->callback;
    }

    /**
     * @param int|null $callback
     */
    public function setCallback(?int $callback): void
    {
        $this->callback = $callback;
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
     * @return string
     */
    public function getDateFromAdmin(): string
    {
        return $this->dateFromAdmin;
    }

    /**
     * @param string $dateFromAdmin
     */
    public function setDateFromAdmin(string $dateFromAdmin): void
    {
        $this->dateFromAdmin = $dateFromAdmin;
    }

    /**
     * @return string
     */
    public function getEquipmentModel(): string
    {
        return $this->equipmentModel;
    }

    /**
     * @param string $equipmentModel
     */
    public function setEquipmentModel(string $equipmentModel): void
    {
        $this->equipmentModel = $equipmentModel;
    }

    /**
     * @return EquipmentConditionEnum
     */
    public function getEquipmentCondition(): EquipmentConditionEnum
    {
        return $this->equipmentCondition;
    }

    /**
     * @param EquipmentConditionEnum $equipmentCondition
     */
    public function setEquipmentCondition(EquipmentConditionEnum $equipmentCondition): void
    {
        $this->equipmentCondition = $equipmentCondition;
    }

    /**
     * @return int
     */
    public function getEquipmentCost(): int
    {
        return $this->equipmentCost;
    }

    /**
     * @param int $equipmentCost
     */
    public function setEquipmentCost(int $equipmentCost): void
    {
        $this->equipmentCost = $equipmentCost;
    }

    /**
     * @return int
     */
    public function getEquipmentRentalCost(): int
    {
        return $this->equipmentRentalCost;
    }

    /**
     * @param int $equipmentRentalCost
     */
    public function setEquipmentRentalCost(int $equipmentRentalCost): void
    {
        $this->equipmentRentalCost = $equipmentRentalCost;
    }

}
