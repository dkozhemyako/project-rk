<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use Aspose\Words\ApiException;
use Aspose\Words\Model\ListFormatUpdate;
use Aspose\Words\Model\PageSetup;
use Aspose\Words\Model\ParagraphFormatUpdate;
use Aspose\Words\Model\ParagraphInsert;
use Aspose\Words\Model\Requests\ConvertDocumentRequest;
use Aspose\Words\Model\Requests\CopyStylesFromTemplateRequest;
use Aspose\Words\Model\Requests\GetDocumentPropertiesOnlineRequest;
use Aspose\Words\Model\Requests\GetParagraphFormatOnlineRequest;
use Aspose\Words\Model\Requests\GetParagraphListFormatOnlineRequest;
use Aspose\Words\Model\Requests\GetParagraphTabStopsOnlineRequest;
use Aspose\Words\Model\Requests\GetSectionPageSetupOnlineRequest;
use Aspose\Words\Model\Requests\GetSectionsOnlineRequest;
use Aspose\Words\Model\Requests\GetStyleOnlineRequest;
use Aspose\Words\Model\Requests\GetStylesOnlineRequest;
use Aspose\Words\Model\Requests\InsertParagraphOnlineRequest;
use Aspose\Words\Model\Requests\UpdateParagraphFormatOnlineRequest;
use Aspose\Words\Model\Requests\UpdateParagraphListFormatOnlineRequest;
use Aspose\Words\Model\Requests\UpdateSectionPageSetupOnlineRequest;
use Aspose\Words\WordsApi;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class TestController extends Controller
{
    public function __construct(
        protected TelegramMessengerService $service,
    ){}

    /**
     * @throws GuzzleException
     */
    public function index(Request $request)
    {
        $document = new TemplateProcessor(storage_path('app/client_fop_agreement.docx'));
        $outputFile = storage_path('app/public/'. time() . '.docx');

        $document->setValue('agreementNumber', time());
        $document->setValue('name', 'test');
        $document->setValue('shortName', 'test');
        $document->setValue('registerNumber', 'test');
        $document->setValue('registerDate', 'test');
        $document->setValue('clientRegion', 'test');
        $document->setValue('clientTown', 'test');
        $document->setValue('clientStreet', 'test');
        $document->setValue('clientHouse', 'test');
        $document->setValue('clientFlat', 'test');
        $document->setValue('clientINN', 'test');
        $document->setValue('equipRegion', 'test');
        $document->setValue('equipTown', 'test');
        $document->setValue('equipStreet', 'test');
        $document->setValue('equipHouse', 'test');
        $document->setValue('equipAdditional', 'test');
        //admin
        $document->setValue('adminDate', 'in progress...');
        $document->setValue('adminEquipModel', 'in progress...');
        $document->setValue('adminEquipCost', 'in progress...');
        $document->setValue('adminEquipCondition', 'in progress...');
        $document->setValue('adminPayDay', 'in progress...');
        $document->setValue('adminEquipRentCost', 'in progress...');

        $document->setImageValue('image', array('path' => storage_path('app/logo.png'), 'width' => 150, 'height' => 100, 'ratio' => false));

        $document->saveAs($outputFile);

        /*
        $token = env('TELEGRAM_TOKEN');

        $arrayQuery = array(
            'chat_id' => 912016646,
            'caption' => 'Проверка',
            'document' => curl_file_create(public_path('/storage/word.docx'))
        );
        $ch = curl_init('https://api.telegram.org/bot'. $token .'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        /*
        $dataClientFOP = [
            'name' => 'Кожемяко Дмитро Володимирович',
            'shortName' => 'Кожемяко Д.В.',
            'registerNumber' =>'1234567890123456789',
            'registerDate' => '11.10.2020',

            'clientRegion' => 'Чернігвська',
            'clientTown' => 'Чернігів',
            'clientStreet' => 'просп. Перемоги',
            'clientHouse' => '80',
            'clientFlat' => '35', ///кв. в ДТО через if
            'clientINN' => '3302403414',

            'equipRegion' => 'Київська',
            'equipTown' => 'Київ',
            'equipStreet' => 'вул. Миколи Василенка',
            'equipHouse' => '14в',
            'equipAdditional' => 'МАФ біля магазину Фора, 5кв.м.',

            //admin

            'adminDate' => '01.12.2023',
            'adminEquipModel' => 'Frosty RT78B1',
            'adminEquipCost' => '16000',
            'adminEquipCondition' => 'не було у використанні',
            'adminPayDay' => '01', //get dd from adminDate
            'adminEquipRentCost' => '3000',
        ];

        $dataClientFO = [
            ''
        ];
        /*
        $pdf = PDF::loadView('fop_agreement', $dataClientFOP);
        $fileName = 'storage_client_files.pdf';
        $pdf->save($fileName);
        */

        /*
        $wordsApi = new WordsApi(config('aspose.cloud.id'), config('aspose.cloud.secret'));

        $doc = storage_path('app/public/storage_client_files.pdf');
        $request = new ConvertDocumentRequest($doc, "docx");


        $convert = $wordsApi->convertDocument($request);

        var_dump($convert);
        Storage::put('new2.docx', $convert->fread($convert->getSize()));
        */




    }

}



