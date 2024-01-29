<?php

namespace App\Repositories\CheckAdminCreateAgreement;

class CheckIdCreateAgreementIterator
{
    protected ? int $id = null;

    public function __construct($data){
        if ($data != null){
            $this->id = $data->id;
        }

    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }



}
