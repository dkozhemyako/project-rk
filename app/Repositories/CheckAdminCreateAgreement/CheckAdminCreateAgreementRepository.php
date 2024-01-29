<?php

namespace App\Repositories\CheckAdminCreateAgreement;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckAdminCreateAgreementRepository
{
    public function store(int $id, int $telegramId): void
    {
         DB::table('check_admin_create_agreement')
            ->insertGetId([
                'id' => $id,
                'telegram_id' => $telegramId,
                'created_at' => Carbon::createFromTimestamp(time()),
            ]);
    }

    public function checkAdmin(int $id): CheckAdminCreateAgreementIterator
    {
        return new CheckAdminCreateAgreementIterator(
            DB::table('check_admin_create_agreement')
                ->select([
                    'id',
                    'telegram_id',
                ])
                ->where('id', '=', $id)
                ->first()
        );

    }

    public function checkId(int $id): CheckIdCreateAgreementIterator
    {
        return new CheckIdCreateAgreementIterator(
            DB::table('check_admin_create_agreement')
                ->select([
                    'id',
                ])
                ->where('id', '=', $id)
                ->first()
        );

    }

}
