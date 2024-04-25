<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\DTO;

use App\Enums\EquipmentConditionEnum;

class AdminAgreementDTO
{
    protected array $replyMarkup = [];
    protected string $fileAgreementAdmin;
    protected string $fileDraftAgreementAdmin;
    protected string $dateFromAdmin;
    protected ? string $equipmentModel = null;
    protected ? EquipmentConditionEnum $equipmentCondition = EquipmentConditionEnum::FALSE;
    protected ? int $equipmentCost = null;
    protected int $equipmentRentalCost;

    protected string $eqType;

    protected ? string $equipmentModelCoffeeMachine = null;
    protected ? string $equipmentModelCoffeeGrinder = null;

    protected ? int $equipmentCostCoffeeMachine = null;
    protected ? int $equipmentCostCoffeeGrinder = null;

    protected ? EquipmentConditionEnum $equipmentConditionCoffeeMachine = EquipmentConditionEnum::FALSE;
    protected ? EquipmentConditionEnum $equipmentConditionCoffeeGrinder = EquipmentConditionEnum::FALSE;


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
     * @param array $replyMarkup
     */
    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

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
     * @return string|null
     */
    public function getEquipmentModel(): ?string
    {
        return $this->equipmentModel;
    }

    /**
     * @param string|null $equipmentModel
     */
    public function setEquipmentModel(?string $equipmentModel): void
    {
        $this->equipmentModel = $equipmentModel;
    }

    /**
     * @return EquipmentConditionEnum|null
     */
    public function getEquipmentCondition(): ?EquipmentConditionEnum
    {
        return $this->equipmentCondition;
    }

    /**
     * @param EquipmentConditionEnum|null $equipmentCondition
     */
    public function setEquipmentCondition(?EquipmentConditionEnum $equipmentCondition): void
    {
        $this->equipmentCondition = $equipmentCondition;
    }

    /**
     * @return int|null
     */
    public function getEquipmentCost(): ?int
    {
        return $this->equipmentCost;
    }

    /**
     * @param int|null $equipmentCost
     */
    public function setEquipmentCost(?int $equipmentCost): void
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

    /**
     * @return string
     */
    public function getEqType(): string
    {
        return $this->eqType;
    }

    /**
     * @param string $eqType
     */
    public function setEqType(string $eqType): void
    {
        $this->eqType = $eqType;
    }

    /**
     * @return string|null
     */
    public function getEquipmentModelCoffeeMachine(): ?string
    {
        return $this->equipmentModelCoffeeMachine;
    }

    /**
     * @param string|null $equipmentModelCoffeeMachine
     */
    public function setEquipmentModelCoffeeMachine(?string $equipmentModelCoffeeMachine): void
    {
        $this->equipmentModelCoffeeMachine = $equipmentModelCoffeeMachine;
    }

    /**
     * @return string|null
     */
    public function getEquipmentModelCoffeeGrinder(): ?string
    {
        return $this->equipmentModelCoffeeGrinder;
    }

    /**
     * @param string|null $equipmentModelCoffeeGrinder
     */
    public function setEquipmentModelCoffeeGrinder(?string $equipmentModelCoffeeGrinder): void
    {
        $this->equipmentModelCoffeeGrinder = $equipmentModelCoffeeGrinder;
    }

    /**
     * @return int|null
     */
    public function getEquipmentCostCoffeeMachine(): ?int
    {
        return $this->equipmentCostCoffeeMachine;
    }

    /**
     * @param int|null $equipmentCostCoffeeMachine
     */
    public function setEquipmentCostCoffeeMachine(?int $equipmentCostCoffeeMachine): void
    {
        $this->equipmentCostCoffeeMachine = $equipmentCostCoffeeMachine;
    }

    /**
     * @return int|null
     */
    public function getEquipmentCostCoffeeGrinder(): ?int
    {
        return $this->equipmentCostCoffeeGrinder;
    }

    /**
     * @param int|null $equipmentCostCoffeeGrinder
     */
    public function setEquipmentCostCoffeeGrinder(?int $equipmentCostCoffeeGrinder): void
    {
        $this->equipmentCostCoffeeGrinder = $equipmentCostCoffeeGrinder;
    }

    /**
     * @return EquipmentConditionEnum|null
     */
    public function getEquipmentConditionCoffeeMachine(): ?EquipmentConditionEnum
    {
        return $this->equipmentConditionCoffeeMachine;
    }

    /**
     * @param EquipmentConditionEnum|null $equipmentConditionCoffeeMachine
     */
    public function setEquipmentConditionCoffeeMachine(?EquipmentConditionEnum $equipmentConditionCoffeeMachine): void
    {
        $this->equipmentConditionCoffeeMachine = $equipmentConditionCoffeeMachine;
    }

    /**
     * @return EquipmentConditionEnum|null
     */
    public function getEquipmentConditionCoffeeGrinder(): ?EquipmentConditionEnum
    {
        return $this->equipmentConditionCoffeeGrinder;
    }

    /**
     * @param EquipmentConditionEnum|null $equipmentConditionCoffeeGrinder
     */
    public function setEquipmentConditionCoffeeGrinder(?EquipmentConditionEnum $equipmentConditionCoffeeGrinder): void
    {
        $this->equipmentConditionCoffeeGrinder = $equipmentConditionCoffeeGrinder;
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
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * @param int $senderId
     */
    public function setSenderId(int $senderId): void
    {
        $this->senderId = $senderId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }







}
