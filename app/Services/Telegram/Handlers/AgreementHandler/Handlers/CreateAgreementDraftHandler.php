<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class CreateAgreementDraftHandler implements AgreementInterface
{

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        if ($agreementDTO->getClientAgreementDTO()->getClientFlat() != 0){
            $agreementDTO->getClientAgreementDTO()->setClientFlat('кв. ' . $agreementDTO->getClientAgreementDTO()->getClientFlat());
        }else {
            $agreementDTO->getClientAgreementDTO()->setClientFlat('');
        }

        $arrayName = explode( " ", $agreementDTO->getClientAgreementDTO()->getName());
        $shortName = mb_substr($arrayName[1], 0, 1);
        $shortSurname = mb_substr($arrayName[2], 0, 1);

        $shortForDraft = $arrayName[0] . ' ' .$shortName.'. '.$shortSurname.'.';

        $fileName = 'cli_draft_'.$agreementDTO->getClientAgreementDTO()->getName() . '.docx';
        $outputFile = storage_path('app/public/'. $fileName);

        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FOP){
            #генерація word fop
            $document = new TemplateProcessor(storage_path('app/client_fop_agreement.docx'));

            $document->setValue('agreementNumber', time());
            $document->setValue('name', $agreementDTO->getClientAgreementDTO()->getName());
            $document->setValue('shortName', $shortForDraft);
            $document->setValue('registerNumber', $agreementDTO->getClientAgreementDTO()->getFopInn());
            $document->setValue('registerDate', $agreementDTO->getClientAgreementDTO()->getFopRegisterDate());
            $document->setValue('clientRegion', $agreementDTO->getClientAgreementDTO()->getClientRegion());
            $document->setValue('clientTown', $agreementDTO->getClientAgreementDTO()->getClientTown());
            $document->setValue('clientStreet', $agreementDTO->getClientAgreementDTO()->getClientStreet());
            $document->setValue('clientHouse', $agreementDTO->getClientAgreementDTO()->getClientHouse());
            $document->setValue('clientFlat', $agreementDTO->getClientAgreementDTO()->getClientFlat());
            $document->setValue('clientINN', $agreementDTO->getClientAgreementDTO()->getClientInn());
            $document->setValue('equipRegion', $agreementDTO->getClientAgreementDTO()->getEquipRegion());
            $document->setValue('equipTown', $agreementDTO->getClientAgreementDTO()->getEquipTown());
            $document->setValue('equipStreet', $agreementDTO->getClientAgreementDTO()->getEquipStreet());
            $document->setValue('equipHouse', $agreementDTO->getClientAgreementDTO()->getEquipHouse());
            $document->setValue('equipAdditional', $agreementDTO->getClientAgreementDTO()->getEquipAddressAdd());
            $document->setValue('phone', '0'.$agreementDTO->getClientAgreementDTO()->getPhone());
            //admin
            $document->setValue('adminDate', 'in progress...');
            $document->setValue('adminEquipModel', 'in progress...');
            $document->setValue('adminEquipCost', 'in progress...');
            $document->setValue('adminEquipCondition', 'in progress...');
            $document->setValue('adminPayDay', 'in progress...');
            $document->setValue('adminEquipRentCost', 'in progress...');

            $document->setImageValue('image', array('path' => storage_path('app/logo.png'), 'width' => 150, 'height' => 100, 'ratio' => false));

            $document->saveAs($outputFile);
        }


        if ($agreementDTO->getClientAgreementDTO()->getType() === TypeClientEnum::FO){
            #генерація word fo
            $document = new TemplateProcessor(storage_path('app/client_fo_agreement.docx'));

            $document->setValue('agreementNumber', time());
            $document->setValue('name', $agreementDTO->getClientAgreementDTO()->getName());
            $document->setValue('shortName', $shortForDraft);
            $document->setValue('registerNumber', $agreementDTO->getClientAgreementDTO()->getFopInn());
            $document->setValue('clientRegion', $agreementDTO->getClientAgreementDTO()->getClientRegion());
            $document->setValue('clientTown', $agreementDTO->getClientAgreementDTO()->getClientTown());
            $document->setValue('clientStreet', $agreementDTO->getClientAgreementDTO()->getClientStreet());
            $document->setValue('clientHouse', $agreementDTO->getClientAgreementDTO()->getClientHouse());
            $document->setValue('clientFlat', $agreementDTO->getClientAgreementDTO()->getClientFlat());
            $document->setValue('clientINN', $agreementDTO->getClientAgreementDTO()->getClientInn());
            $document->setValue('equipRegion', $agreementDTO->getClientAgreementDTO()->getEquipRegion());
            $document->setValue('equipTown', $agreementDTO->getClientAgreementDTO()->getEquipTown());
            $document->setValue('equipStreet', $agreementDTO->getClientAgreementDTO()->getEquipStreet());
            $document->setValue('equipHouse', $agreementDTO->getClientAgreementDTO()->getEquipHouse());
            $document->setValue('equipAdditional', $agreementDTO->getClientAgreementDTO()->getEquipAddressAdd());
            $document->setValue('passportNumber', $agreementDTO->getClientAgreementDTO()->getPassportSeriesNumber());
            $document->setValue('passportIssue', $agreementDTO->getClientAgreementDTO()->getPassportIssue());
            $document->setValue('passportDate', $agreementDTO->getClientAgreementDTO()->getPassportDate());
            $document->setValue('phone', '0'.$agreementDTO->getClientAgreementDTO()->getPhone());

            //admin
            $document->setValue('adminDate', 'in progress...');
            $document->setValue('adminEquipModel', 'in progress...');
            $document->setValue('adminEquipCost', 'in progress...');
            $document->setValue('adminEquipCondition', 'in progress...');
            $document->setValue('adminPayDay', 'in progress...');
            $document->setValue('adminEquipRentCost', 'in progress...');

            $document->setImageValue('image', array('path' => storage_path('app/logo.png'), 'width' => 150, 'height' => 100, 'ratio' => false));

            $document->saveAs($outputFile);
        }

        #генерація pdf
        /*
        $pdf = PDF::loadView('fop_agreement', $dataClientFOP);
        $fileName = time().'.pdf';
        $pdf->save($fileName, 'public');
        */

        $agreementDTO->getClientAgreementDTO()->setFileDraftAgreement($fileName);

        return $next($agreementDTO);

    }


}
