<?php

namespace App\Repositories\ClientAgreement\DTO;

use App\Enums\EqTypeClientEnum;
use App\Enums\TypeClientEnum;

class ClientAgreementDTO
{
    protected string $dateFromClient;
    protected TypeClientEnum $type;
    protected EqTypeClientEnum $eqType;
    protected string $name;
    protected int $phone;
    protected int $clientInn;
    protected ? string $passportSeriesNumber = null;
    protected ? string $passportIssue = null;
    protected ? string $passportDate = null;
    protected ? int $fopInn = null;
    protected ? string $fopRegisterDate = null;
    protected string $clientRegion;
    protected string $clientTown = 'empty';
    protected string $clientStreet = 'empty';
    protected string $clientHouse = 'empty';
    protected string $clientFlat = 'empty';
    protected string $equipRegion;
    protected string $equipTown = 'empty';
    protected string $equipStreet = 'empty';
    protected string $equipHouse = 'empty';
    protected string $equipAddressAdd;
    protected ? string $fileFopEdr = null;
    protected ? string $fileFopAgrRent = null;
    protected ? string $fileFoPass1st = null;
    protected ? string $fileFoPass2nd = null;
    protected ? string $fileFoPassReg = null;
    protected ? string $fileFoAgrRent = null;
    protected ? string $fileDraftAgreement = null;
    protected int $telegramId;

    /**
     * @return EqTypeClientEnum
     */
    public function getEqType(): EqTypeClientEnum
    {
        return $this->eqType;
    }

    /**
     * @param EqTypeClientEnum $eqType
     */
    public function setEqType(EqTypeClientEnum $eqType): void
    {
        $this->eqType = $eqType;
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
     * @return TypeClientEnum
     */
    public function getType(): TypeClientEnum
    {
        return $this->type;
    }

    /**
     * @param TypeClientEnum $type
     */
    public function setType(TypeClientEnum $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getPhone(): int
    {
        return $this->phone;
    }

    /**
     * @param int $phone
     */
    public function setPhone(int $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getClientInn(): int
    {
        return $this->clientInn;
    }

    /**
     * @param int $clientInn
     */
    public function setClientInn(int $clientInn): void
    {
        $this->clientInn = $clientInn;
    }

    /**
     * @return string|null
     */
    public function getPassportSeriesNumber(): ?string
    {
        return $this->passportSeriesNumber;
    }

    /**
     * @param string|null $passportSeriesNumber
     */
    public function setPassportSeriesNumber(?string $passportSeriesNumber): void
    {
        $this->passportSeriesNumber = $passportSeriesNumber;
    }

    /**
     * @return string|null
     */
    public function getPassportIssue(): ?string
    {
        return $this->passportIssue;
    }

    /**
     * @param string|null $passportIssue
     */
    public function setPassportIssue(?string $passportIssue): void
    {
        $this->passportIssue = $passportIssue;
    }

    /**
     * @return string|null
     */
    public function getPassportDate(): ?string
    {
        return $this->passportDate;
    }

    /**
     * @param string|null $passportDate
     */
    public function setPassportDate(?string $passportDate): void
    {
        $this->passportDate = $passportDate;
    }

    /**
     * @return int|null
     */
    public function getFopInn(): ?int
    {
        return $this->fopInn;
    }

    /**
     * @param int|null $fopInn
     */
    public function setFopInn(?int $fopInn): void
    {
        $this->fopInn = $fopInn;
    }

    /**
     * @return string|null
     */
    public function getFopRegisterDate(): ?string
    {
        return $this->fopRegisterDate;
    }

    /**
     * @param string|null $fopRegisterDate
     */
    public function setFopRegisterDate(?string $fopRegisterDate): void
    {
        $this->fopRegisterDate = $fopRegisterDate;
    }

    /**
     * @return string
     */
    public function getClientRegion(): string
    {
        return $this->clientRegion;
    }

    /**
     * @param string $clientRegion
     */
    public function setClientRegion(string $clientRegion): void
    {
        $this->clientRegion = $clientRegion;
    }

    /**
     * @return string
     */
    public function getClientTown(): string
    {
        return $this->clientTown;
    }

    /**
     * @param string $clientTown
     */
    public function setClientTown(string $clientTown): void
    {
        $this->clientTown = $clientTown;
    }

    /**
     * @return string
     */
    public function getClientStreet(): string
    {
        return $this->clientStreet;
    }

    /**
     * @param string $clientStreet
     */
    public function setClientStreet(string $clientStreet): void
    {
        $this->clientStreet = $clientStreet;
    }

    /**
     * @return string
     */
    public function getClientHouse(): string
    {
        return $this->clientHouse;
    }

    /**
     * @param string $clientHouse
     */
    public function setClientHouse(string $clientHouse): void
    {
        $this->clientHouse = $clientHouse;
    }

    /**
     * @return string
     */
    public function getClientFlat(): string
    {
        return $this->clientFlat;
    }

    /**
     * @param string $clientFlat
     */
    public function setClientFlat(string $clientFlat): void
    {
        $this->clientFlat = $clientFlat;
    }

    /**
     * @return string
     */
    public function getEquipRegion(): string
    {
        return $this->equipRegion;
    }

    /**
     * @param string $equipRegion
     */
    public function setEquipRegion(string $equipRegion): void
    {
        $this->equipRegion = $equipRegion;
    }

    /**
     * @return string
     */
    public function getEquipTown(): string
    {
        return $this->equipTown;
    }

    /**
     * @param string $equipTown
     */
    public function setEquipTown(string $equipTown): void
    {
        $this->equipTown = $equipTown;
    }

    /**
     * @return string
     */
    public function getEquipStreet(): string
    {
        return $this->equipStreet;
    }

    /**
     * @param string $equipStreet
     */
    public function setEquipStreet(string $equipStreet): void
    {
        $this->equipStreet = $equipStreet;
    }

    /**
     * @return string
     */
    public function getEquipHouse(): string
    {
        return $this->equipHouse;
    }

    /**
     * @param string $equipHouse
     */
    public function setEquipHouse(string $equipHouse): void
    {
        $this->equipHouse = $equipHouse;
    }

    /**
     * @return string
     */
    public function getEquipAddressAdd(): string
    {
        return $this->equipAddressAdd;
    }

    /**
     * @param string $equipAddressAdd
     */
    public function setEquipAddressAdd(string $equipAddressAdd): void
    {
        $this->equipAddressAdd = $equipAddressAdd;
    }

    /**
     * @return string|null
     */
    public function getFileFopEdr(): ?string
    {
        return $this->fileFopEdr;
    }

    /**
     * @param string|null $fileFopEdr
     */
    public function setFileFopEdr(?string $fileFopEdr): void
    {
        $this->fileFopEdr = $fileFopEdr;
    }

    /**
     * @return string|null
     */
    public function getFileFopAgrRent(): ?string
    {
        return $this->fileFopAgrRent;
    }

    /**
     * @param string|null $fileFopAgrRent
     */
    public function setFileFopAgrRent(?string $fileFopAgrRent): void
    {
        $this->fileFopAgrRent = $fileFopAgrRent;
    }

    /**
     * @return string|null
     */
    public function getFileFoPass1st(): ?string
    {
        return $this->fileFoPass1st;
    }

    /**
     * @param string|null $fileFoPass1st
     */
    public function setFileFoPass1st(?string $fileFoPass1st): void
    {
        $this->fileFoPass1st = $fileFoPass1st;
    }

    /**
     * @return string|null
     */
    public function getFileFoPass2nd(): ?string
    {
        return $this->fileFoPass2nd;
    }

    /**
     * @param string|null $fileFoPass2nd
     */
    public function setFileFoPass2nd(?string $fileFoPass2nd): void
    {
        $this->fileFoPass2nd = $fileFoPass2nd;
    }

    /**
     * @return string|null
     */
    public function getFileFoPassReg(): ?string
    {
        return $this->fileFoPassReg;
    }

    /**
     * @param string|null $fileFoPassReg
     */
    public function setFileFoPassReg(?string $fileFoPassReg): void
    {
        $this->fileFoPassReg = $fileFoPassReg;
    }

    /**
     * @return string|null
     */
    public function getFileFoAgrRent(): ?string
    {
        return $this->fileFoAgrRent;
    }

    /**
     * @param string|null $fileFoAgrRent
     */
    public function setFileFoAgrRent(?string $fileFoAgrRent): void
    {
        $this->fileFoAgrRent = $fileFoAgrRent;
    }

    /**
     * @return string|null
     */
    public function getFileDraftAgreement(): ?string
    {
        return $this->fileDraftAgreement;
    }

    /**
     * @param string|null $fileDraftAgreement
     */
    public function setFileDraftAgreement(?string $fileDraftAgreement): void
    {
        $this->fileDraftAgreement = $fileDraftAgreement;
    }

    /**
     * @return int
     */
    public function getTelegramId(): int
    {
        return $this->telegramId;
    }

    /**
     * @param int $telegramId
     */
    public function setTelegramId(int $telegramId): void
    {
        $this->telegramId = $telegramId;
    }




}
