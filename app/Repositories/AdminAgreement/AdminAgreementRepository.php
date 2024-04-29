<?php

namespace App\Repositories\AdminAgreement;

use App\Repositories\ClientAgreement\ClientAgreementIterator;
use App\Repositories\ClientAgreement\DTO\ClientAgreementDTO;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAgreementRepository
{
    public function store(AdminAgreementDTO $dto): void
    {
        DB::table('admin_agreements')
            ->updateOrInsert(
                ['id' => $dto->getCallback()],
                [
                'created_at' => Carbon::createFromTimestamp(time()),
                'date_from_admin' => $dto->getDateFromAdmin(),
                'equipment_condition' => $dto->getEquipmentCondition()->name,
                'equipment_model' => $dto->getEquipmentModel(),
                'equipment_cost' => $dto->getEquipmentCost(),
                'equipment_rent_cost' => $dto->getEquipmentRentalCost(),
                'cm_model' => $dto->getEquipmentModelCoffeeMachine(),
                'cm_cost' => $dto->getEquipmentCostCoffeeMachine(),
                'cm_condition' => $dto->getEquipmentConditionCoffeeMachine()->name,
                'cg_model' => $dto->getEquipmentModelCoffeeGrinder(),
                'cg_cost' => $dto->getEquipmentCostCoffeeGrinder(),
                'cg_condition' => $dto->getEquipmentConditionCoffeeGrinder()->name,
                'file_agreement' => $dto->getFileAgreementAdmin(),
            ],
            );
    }

    public function updateSignedAgreement(int $id, string $fileName): void
    {
        DB::table('admin_agreements')
            ->where('id', '=', $id)
            ->update([
                'file_signed_agreement' => $fileName,
                'updated_at' => Carbon::createFromTimestamp(time()),
            ]);
    }
    public function updateDraftAgreement(int $id, string $fileName): void
    {
        DB::table('admin_agreements')
            ->where('id', '=', $id)
            ->update([
                'file_draft_agreement' => $fileName,
                'updated_at' => Carbon::createFromTimestamp(time()),
            ]);
    }

    public function getClientAgreementData(int $id) : ClientAgreementIterator
    {
        return new ClientAgreementIterator(
            DB::table('client_agreements')
                ->select([
                    'id',
                    'type',
                    'name',
                    'phone',
                    'client_inn',
                    'passport_series_number',
                    'passport_issue',
                    'passport_date',
                    'fop_inn',
                    'fop_register_date',
                    'client_region',
                    'client_town',
                    'client_street',
                    'client_house',
                    'client_flat',
                    'equip_region',
                    'equip_town',
                    'equip_street',
                    'equip_house',
                    'equip_address_add',
                    'file_fop_edr',
                    'file_fop_agr_rent',
                    'file_fo_pas1st',
                    'file_fo_pas2nd',
                    'file_fo_pasReg',
                    'file_fo_agr_rent',
                    'file_draft_agreement',
                    'telegram_id',
                    'eq_type',
                ])
                ->where('id', '=', $id)
                ->first()
        );
    }

    public function getClientInfoForFilesById(int $id): AdminInfoForFilesIterator
    {
        return new AdminInfoForFilesIterator(
            DB::table('admin_agreements')
                ->select([
                    'equipment_condition',
                    'equipment_model',
                    'file_agreement',
                    'file_draft_agreement',
                    'file_signed_agreement',
                ])
                ->where('id', '=', $id)
                ->first()
        );
    }

}
