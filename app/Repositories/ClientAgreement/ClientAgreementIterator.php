<?php

namespace App\Repositories\ClientAgreement;

class ClientAgreementIterator
{
    protected int $id;
    protected string $type;
    protected string $name;
    protected string $phone;
    protected string $client_inn;
    protected ? string $passport_series_number;
    protected ? string $passport_issue;
    protected ? string $passport_date;
    protected ? int $fop_inn;
    protected ? string $fop_register_date;
    protected string $client_region;
    protected string $client_town;
    protected string $client_street;
    protected string $client_house;
    protected string $client_flat;
    protected string $equip_region;
    protected string $equip_town;
    protected string $equip_street;
    protected string $equip_house;
    protected string $equip_address_add;
    protected ? string $file_fop_edr;
    protected ? string $file_fop_agr_rent;
    protected ? string $file_fo_pas1st;
    protected ? string $file_fo_pas2nd;
    protected ? string $file_fo_pasReg;
    protected ? string $file_fo_agr_rent;
    protected ? string $file_draft_agreement;
    protected int $telegram_id;
    public function __construct(object $data)
    {
        $this->id = $data->id;
        $this->type = $data->type;
        $this->name = $data->name;
        $this->phone = $data->phone;
        $this->client_inn = $data->client_inn;
        $this->passport_series_number = $data->passport_series_number;
        $this->passport_issue = $data->passport_issue;
        $this->passport_date = $data->passport_date;
        $this->fop_inn = $data->fop_inn;
        $this->fop_register_date = $data->fop_register_date;
        $this->client_region = $data->client_region;
        $this->client_town = $data->client_town;
        $this->client_street = $data->client_street;
        $this->client_house = $data->client_house;
        $this->client_flat = $data->client_flat;
        $this->equip_region = $data->equip_region;
        $this->equip_town = $data->equip_town;
        $this->equip_street = $data->equip_street;
        $this->equip_house = $data->equip_house;
        $this->equip_address_add = $data->equip_address_add;
        $this->file_fop_edr = $data->file_fop_edr;
        $this->file_fop_agr_rent = $data->file_fop_agr_rent;
        $this->file_fo_pas1st = $data->file_fo_pas1st;
        $this->file_fo_pas2nd = $data->file_fo_pas2nd;
        $this->file_fo_pasReg = $data->file_fo_pasReg;
        $this->file_fo_agr_rent = $data->file_fo_agr_rent;
        $this->file_draft_agreement = $data->file_draft_agreement;
        $this->telegram_id = $data->telegram_id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getClientInn(): string
    {
        return $this->client_inn;
    }

    /**
     * @return string|null
     */
    public function getPassportSeriesNumber(): ?string
    {
        return $this->passport_series_number;
    }

    /**
     * @return string|null
     */
    public function getPassportIssue(): ?string
    {
        return $this->passport_issue;
    }

    /**
     * @return string|null
     */
    public function getPassportDate(): ?string
    {
        return $this->passport_date;
    }

    /**
     * @return int|null
     */
    public function getFopInn(): ?int
    {
        return $this->fop_inn;
    }

    /**
     * @return string|null
     */
    public function getFopRegisterDate(): ?string
    {
        return $this->fop_register_date;
    }

    /**
     * @return string
     */
    public function getClientRegion(): string
    {
        return $this->client_region;
    }

    /**
     * @return string
     */
    public function getClientTown(): string
    {
        return $this->client_town;
    }

    /**
     * @return string
     */
    public function getClientStreet(): string
    {
        return $this->client_street;
    }

    /**
     * @return string
     */
    public function getClientHouse(): string
    {
        return $this->client_house;
    }

    /**
     * @return string
     */
    public function getClientFlat(): string
    {
        return $this->client_flat;
    }

    /**
     * @return string
     */
    public function getEquipRegion(): string
    {
        return $this->equip_region;
    }

    /**
     * @return string
     */
    public function getEquipTown(): string
    {
        return $this->equip_town;
    }

    /**
     * @return string
     */
    public function getEquipStreet(): string
    {
        return $this->equip_street;
    }

    /**
     * @return string
     */
    public function getEquipHouse(): string
    {
        return $this->equip_house;
    }

    /**
     * @return string
     */
    public function getEquipAddressAdd(): string
    {
        return $this->equip_address_add;
    }

    /**
     * @return string|null
     */
    public function getFileFopEdr(): ?string
    {
        return $this->file_fop_edr;
    }

    /**
     * @return string|null
     */
    public function getFileFopAgrRent(): ?string
    {
        return $this->file_fop_agr_rent;
    }

    /**
     * @return string|null
     */
    public function getFileFoPas1st(): ?string
    {
        return $this->file_fo_pas1st;
    }

    /**
     * @return string|null
     */
    public function getFileFoPas2nd(): ?string
    {
        return $this->file_fo_pas2nd;
    }

    /**
     * @return string|null
     */
    public function getFileFoPasReg(): ?string
    {
        return $this->file_fo_pasReg;
    }

    /**
     * @return string|null
     */
    public function getFileFoAgrRent(): ?string
    {
        return $this->file_fo_agr_rent;
    }

    /**
     * @return string|null
     */
    public function getFileDraftAgreement(): ?string
    {
        return $this->file_draft_agreement;
    }

    /**
     * @return int
     */
    public function getTelegramId(): int
    {
        return $this->telegram_id;
    }



}
