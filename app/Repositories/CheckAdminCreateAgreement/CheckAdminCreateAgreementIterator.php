<?php

namespace App\Repositories\CheckAdminCreateAgreement;

class CheckAdminCreateAgreementIterator
{
    protected ? int $id = null;
    protected ? string $telegramId = null;

    public function __construct($data){
        if($data !== null){
            $this->id = $data->id;
            $this->telegramId = $data->telegram_id;
        }

    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTelegramId(): ?string
    {
        return $this->telegramId;
    }





}
