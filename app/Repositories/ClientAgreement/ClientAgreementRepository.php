<?php

namespace App\Repositories\ClientAgreement;

use App\Repositories\ClientAgreement\DTO\ClientAgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClientAgreementRepository
{
    public function store(ClientAgreementDTO $dto): int
    {
        return DB::table('client_agreements')
            ->insertGetId([
                'created_at' => Carbon::createFromTimestamp(time()),
                'date_from_client' => $dto->getDateFromClient(),
                'type' => $dto->getType()->name,
                'name' => $dto->getName(),
                'phone' => $dto->getPhone(),
                'client_inn' => $dto->getClientInn(),
                'passport_series_number' => $dto->getPassportSeriesNumber(),
                'passport_issue' => $dto->getPassportIssue(),
                'passport_date' => $dto->getPassportDate(),
                'fop_inn' => $dto->getFopInn(),
                'fop_register_date' => $dto->getFopRegisterDate(),
                'client_region' => $dto->getClientRegion(),
                'client_town' => $dto->getClientTown(),
                'client_street' => $dto->getClientStreet(),
                'client_house' => $dto->getClientHouse(),
                'client_flat' => $dto->getClientFlat(),
                'equip_region' => $dto->getEquipRegion(),
                'equip_town' => $dto->getEquipTown(),
                'equip_street' => $dto->getEquipStreet(),
                'equip_house' => $dto->getEquipHouse(),
                'equip_address_add' => $dto->getEquipAddressAdd(),
                'file_fop_edr' => $dto->getFileFopEdr(),
                'file_fop_agr_rent' => $dto->getFileFopAgrRent(),
                'file_fo_pas1st' => $dto->getFileFoPass1st(),
                'file_fo_pas2nd' => $dto->getFileFoPass2nd(),
                'file_fo_pasReg' => $dto->getFileFoPassReg(),
                'file_fo_agr_rent' => $dto->getFileFoAgrRent(),
                'file_draft_agreement' => $dto->getFileDraftAgreement(),
                'telegram_id' => $dto->getTelegramId(),
            ]);
    }

    public function getClientTelegramIdById(int $id): Collection
    {
        return DB::table('client_agreements')
            ->select([
                'telegram_id',
            ])
            ->where('id', '=', $id)
            ->get();

    }

    public function updateSignedAgreement(int $id, string $fileName): void
    {
        DB::table('client_agreements')
            ->where('id', '=', $id)
            ->update([
                'file_signed_agreement' => $fileName,
                'updated_at' => Carbon::createFromTimestamp(time()),
            ]);
    }

    public function getClientFilesById(int $id): ClientFilesIterator
    {
        //return iterator
        return new ClientFilesIterator(
            DB::table('client_agreements')
                ->select([
                    'file_fop_edr',
                    'file_fop_agr_rent',
                    'file_fo_pas1st',
                    'file_fo_pas2nd',
                    'file_fo_pasReg',
                    'file_fo_agr_rent',
                    'file_draft_agreement',
                    'file_signed_agreement',
                    'name',
                    'phone',
                    'equip_town',
                    'equip_street',
                    'equip_house',
                ])
                ->where('id', '=', $id)
                ->first()
        );

    }
}
