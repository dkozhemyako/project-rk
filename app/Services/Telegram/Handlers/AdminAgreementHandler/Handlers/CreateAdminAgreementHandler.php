<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;



use App\Enums\EqTypeClientEnum;
use App\Enums\EquipmentConditionEnum;
use App\Enums\TypeClientEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpWord\TemplateProcessor;

class CreateAdminAgreementHandler implements AdminAgreementInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
    ){}
    public const AGR_CREATE_ADMIN = '_ADMIN_CREATE_AGREEMENT';


    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $clientAgreementData = $this->adminAgreementRepository->getClientAgreementData($adminAgreementDTO->getCallback());

        if ($clientAgreementData->getId() > 0){

            $shortDate = explode('.', $adminAgreementDTO->getDateFromAdmin());

            $arrayName = explode( " ", $clientAgreementData->getName());
            $shortName = mb_substr($arrayName[1], 0, 1);
            $shortSurname = mb_substr($arrayName[2], 0, 1);

            $shortForDraft = $arrayName[0] . ' ' .$shortName.'. '.$shortSurname.'.';

            $flat = '';
            if($clientAgreementData->getClientFlat() != 0){
                $flat = $clientAgreementData->getClientFlat();
            }
            $phone = '0'.$clientAgreementData->getPhone();

            $fileName = 'adm_draft_'.$clientAgreementData->getName() . '.docx';
            $outputFile = storage_path('app/public/'. $fileName);


            if($clientAgreementData->getType() === TypeClientEnum::FOP->name){
                #генерація word fop
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fop_agreement.docx'));
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::KK->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fop_agreement_kk.docx'));
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::PACK->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fop_agreement_pack.docx'));
                }



                $document->setValue('agreementNumber', time());
                $document->setValue('name', $clientAgreementData->getName());
                $document->setValue('shortName', $shortForDraft);
                $document->setValue('registerNumber', $clientAgreementData->getFopInn());
                $document->setValue('registerDate', $clientAgreementData->getFopRegisterDate());
                $document->setValue('clientRegion', $clientAgreementData->getClientRegion());
                $document->setValue('clientTown', $clientAgreementData->getClientTown());
                $document->setValue('clientStreet', $clientAgreementData->getClientStreet());
                $document->setValue('clientHouse', $clientAgreementData->getClientHouse());
                $document->setValue('clientFlat', $flat);
                $document->setValue('clientINN', $clientAgreementData->getClientInn());
                $document->setValue('equipRegion', $clientAgreementData->getEquipRegion());
                $document->setValue('equipTown', $clientAgreementData->getEquipTown());
                $document->setValue('equipStreet', $clientAgreementData->getEquipStreet());
                $document->setValue('equipHouse', $clientAgreementData->getEquipHouse());
                $document->setValue('equipAdditional', $clientAgreementData->getEquipAddressAdd());
                $document->setValue('phone', $phone);

                //admin
                $document->setValue('adminDate', $adminAgreementDTO->getDateFromAdmin());
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
                    $document->setValue('adminEquipModel', $adminAgreementDTO->getEquipmentModel());
                    $document->setValue('adminEquipCost', $adminAgreementDTO->getEquipmentCost());
                    $document->setValue('adminEquipCondition', $adminAgreementDTO->getEquipmentCondition()->value);
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::KK->value){
                    $document->setValue('adminCoffeeMachineModel', $adminAgreementDTO->getEquipmentModelCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCost', $adminAgreementDTO->getEquipmentCostCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCondition', $adminAgreementDTO->getEquipmentConditionCoffeeMachine()->value);

                    $document->setValue('adminCoffeeGrinderModel', $adminAgreementDTO->getEquipmentModelCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCost', $adminAgreementDTO->getEquipmentCostCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCondition', $adminAgreementDTO->getEquipmentConditionCoffeeGrinder()->value);
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::PACK->value) {
                    $document->setValue('adminEquipModel', $adminAgreementDTO->getEquipmentModel());
                    $document->setValue('adminEquipCost', $adminAgreementDTO->getEquipmentCost());
                    $document->setValue('adminEquipCondition', $adminAgreementDTO->getEquipmentCondition()->value);

                    $document->setValue('adminCoffeeMachineModel', $adminAgreementDTO->getEquipmentModelCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCost', $adminAgreementDTO->getEquipmentCostCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCondition', $adminAgreementDTO->getEquipmentConditionCoffeeMachine()->value);

                    $document->setValue('adminCoffeeGrinderModel', $adminAgreementDTO->getEquipmentModelCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCost', $adminAgreementDTO->getEquipmentCostCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCondition', $adminAgreementDTO->getEquipmentConditionCoffeeGrinder()->value);
                }

                $document->setValue('adminPayDay', $shortDate[0]);
                $document->setValue('adminEquipRentCost', $adminAgreementDTO->getEquipmentRentalCost());

                $document->setImageValue('image', array('path' => storage_path('app/logo.png'), 'width' => 150, 'height' => 100, 'ratio' => false));

                $document->saveAs($outputFile);
            }

            if($clientAgreementData->getType() === TypeClientEnum::FO->name){
                #генерація word fo
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fo_agreement.docx'));
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::KK->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fo_agreement_kk.docx'));
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::PACK->value){
                    $document = new TemplateProcessor(storage_path('app/admin_fo_agreement_pack.docx'));
                }

                $document->setValue('agreementNumber', time());
                $document->setValue('name', $clientAgreementData->getName());
                $document->setValue('shortName', $shortForDraft);
                $document->setValue('clientRegion', $clientAgreementData->getClientRegion());
                $document->setValue('clientTown', $clientAgreementData->getClientTown());
                $document->setValue('clientStreet', $clientAgreementData->getClientStreet());
                $document->setValue('clientHouse', $clientAgreementData->getClientHouse());
                $document->setValue('clientFlat', $flat);
                $document->setValue('clientINN', $clientAgreementData->getClientInn());
                $document->setValue('equipRegion', $clientAgreementData->getEquipRegion());
                $document->setValue('equipTown', $clientAgreementData->getEquipTown());
                $document->setValue('equipStreet', $clientAgreementData->getEquipStreet());
                $document->setValue('equipHouse', $clientAgreementData->getEquipHouse());
                $document->setValue('equipAdditional', $clientAgreementData->getEquipAddressAdd());
                $document->setValue('passportNumber', $clientAgreementData->getPassportDate());
                $document->setValue('passportIssue', $clientAgreementData->getPassportIssue());
                $document->setValue('passportDate', $clientAgreementData->getPassportDate());
                $document->setValue('phone', $phone);

                //admin
                $document->setValue('adminDate', $adminAgreementDTO->getDateFromAdmin());
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::HV->value){
                    $document->setValue('adminEquipModel', $adminAgreementDTO->getEquipmentModel());
                    $document->setValue('adminEquipCost', $adminAgreementDTO->getEquipmentCost());
                    $document->setValue('adminEquipCondition', $adminAgreementDTO->getEquipmentCondition()->value);
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::KK->value){
                    $document->setValue('adminCoffeeMachineModel', $adminAgreementDTO->getEquipmentModelCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCost', $adminAgreementDTO->getEquipmentCostCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCondition', $adminAgreementDTO->getEquipmentConditionCoffeeMachine()->value);

                    $document->setValue('adminCoffeeGrinderModel', $adminAgreementDTO->getEquipmentModelCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCost', $adminAgreementDTO->getEquipmentCostCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCondition', $adminAgreementDTO->getEquipmentConditionCoffeeGrinder()->value);
                }
                if ($adminAgreementDTO->getEqType() == EqTypeClientEnum::PACK->value) {
                    $document->setValue('adminEquipModel', $adminAgreementDTO->getEquipmentModel());
                    $document->setValue('adminEquipCost', $adminAgreementDTO->getEquipmentCost());
                    $document->setValue('adminEquipCondition', $adminAgreementDTO->getEquipmentCondition()->value);

                    $document->setValue('adminCoffeeMachineModel', $adminAgreementDTO->getEquipmentModelCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCost', $adminAgreementDTO->getEquipmentCostCoffeeMachine());
                    $document->setValue('adminCoffeeMachineCondition', $adminAgreementDTO->getEquipmentConditionCoffeeMachine()->value);

                    $document->setValue('adminCoffeeGrinderModel', $adminAgreementDTO->getEquipmentModelCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCost', $adminAgreementDTO->getEquipmentCostCoffeeGrinder());
                    $document->setValue('adminCoffeeGrinderCondition', $adminAgreementDTO->getEquipmentConditionCoffeeGrinder()->value);
                }
                $document->setValue('adminPayDay', $shortDate[0]);
                $document->setValue('adminEquipRentCost', $adminAgreementDTO->getEquipmentRentalCost());

                $document->setImageValue('image', array('path' => storage_path('app/logo.png'), 'width' => 150, 'height' => 100, 'ratio' => false));

                $document->saveAs($outputFile);
            }




            #генерація word fo
            /*
            $pdf = PDF::loadView('fop_admin_agreement', $dataClientFOP);
            $fileName = time().'.pdf';
            $pdf->save($fileName, 'public');
            */

            $adminAgreementDTO->setFileAgreementAdmin($fileName);
            return $next($adminAgreementDTO);
        }
        $adminAgreementDTO->setMessage(
            'Сталася помилка, зверніться до адміністратора :)'
        );
        return $next($adminAgreementDTO);
    }
}
