<?php

namespace App\Repositories\AdminAgreement;

class AdminInfoForFilesIterator
{
    protected string $equipment_condition;
    protected ? string $equipment_model;
    protected ? string $file_draft_agreement = null;
    protected ? string $file_agreement = null;
    protected ? string $file_signed_agreement = null;

    public function __construct($data){
        $this->equipment_condition = $data->equipment_condition;
        $this->equipment_model = $data->equipment_model;

        if($data->file_draft_agreement != null){
            $this->file_draft_agreement = $data->file_draft_agreement;
        }
        if($data->file_agreement != null){
            $this->file_agreement = $data->file_agreement;
        }
        if($data->file_signed_agreement != null){
            $this->file_signed_agreement = $data->file_signed_agreement;
        }
    }

    /**
     * @return string|null
     */
    public function getFileAgreement(): ?string
    {
        return $this->file_agreement;
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
    public function getFileDraftAgreement(): ?string
    {
        return $this->file_draft_agreement;
    }

    /**
     * @return string
     */
    public function getEquipmentCondition(): string
    {
        return $this->equipment_condition;
    }

    /**
     * @return string
     */
    public function getEquipmentModel(): string
    {
        return $this->equipment_model;
    }

}
