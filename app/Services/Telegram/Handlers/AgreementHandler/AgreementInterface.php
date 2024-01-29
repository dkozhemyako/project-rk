<?php

namespace App\Services\Telegram\Handlers\AgreementHandler;

use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;

interface AgreementInterface
{
    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO;
}