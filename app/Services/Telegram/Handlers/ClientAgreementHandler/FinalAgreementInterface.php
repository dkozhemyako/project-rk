<?php

namespace App\Services\Telegram\Handlers\ClientAgreementHandler;

use App\Services\Telegram\Handlers\ClientAgreementHandler\DTO\FinalAgreementDTO;
use Closure;

interface FinalAgreementInterface
{
    public function handle(FinalAgreementDTO $finalAgreementDTO, Closure $next): FinalAgreementDTO;
}
