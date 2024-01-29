<?php

namespace App\Services\Telegram\Handlers\ClientCheckAgreementFalse;

use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use App\Services\Telegram\Handlers\ClientCheckAgreementFalse\DTO\ClientCheckAgreementFalseDTO;
use Closure;

interface ClientCheckAgreementFalseInterface
{
    public function handle(ClientCheckAgreementFalseDTO $agreementFalseDTO, Closure $next): ClientCheckAgreementFalseDTO;
}
