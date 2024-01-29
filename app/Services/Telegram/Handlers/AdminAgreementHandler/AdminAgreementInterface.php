<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler;

use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Closure;

interface AdminAgreementInterface
{
    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO;
}
