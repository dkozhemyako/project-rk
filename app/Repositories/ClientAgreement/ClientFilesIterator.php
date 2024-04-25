<?php

namespace App\Repositories\ClientAgreement;

class ClientFilesIterator
{
    protected ? string $file_fop_edr;
    protected ? string $file_fop_agr_rent;
    protected ? string $file_fo_pas1st;
    protected ? string $file_fo_pas2nd;
    protected ? string $file_fo_pasReg;
    protected ? string $file_fo_agr_rent;

    protected ? string $file_draft_agreement;
    protected ? string $file_signed_agreement;
    protected string $name;
    protected string $phone;
    protected string $equip_town;
    protected string $equip_street;
    protected string $equip_house;

    protected string $eq_type;
    public function __construct(object $data)
    {
        $this->file_fop_edr = $data->file_fop_edr;
        $this->file_fop_agr_rent = $data->file_fop_agr_rent;
        $this->file_fo_pas1st = $data->file_fo_pas1st;
        $this->file_fo_pas2nd = $data->file_fo_pas2nd;
        $this->file_fo_pasReg = $data->file_fo_pasReg;
        $this->file_fo_agr_rent = $data->file_fo_agr_rent;
        $this->file_draft_agreement = $data->file_draft_agreement;
        $this->file_signed_agreement = $data->file_signed_agreement;
        $this->name = $data->name;
        $this->phone = $data->phone;
        $this->equip_town = $data->equip_town;
        $this->equip_street = $data->equip_street;
        $this->equip_house = $data->equip_house;
        $this->eq_type = $data->eq_type;

    }

    /**
     * @return string
     */
    public function getEqType(): string
    {
        return $this->eq_type;
    }

    /**
     * @return string|null
     */
    public function getFileDraftAgreement(): ?string
    {
        return $this->file_draft_agreement;
    }

    /**
     * @return string|null
     */
    public function getFileSignedAgreement(): ?string
    {
        return $this->file_signed_agreement;
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





}
