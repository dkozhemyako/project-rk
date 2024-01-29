<?php

namespace App\Console\Commands;

use App\Services\GoogleDrive\GDriveCreateFolderService;
use App\Services\GoogleDrive\GDriveSendDocumentService;
use Aspose\Words\ApiException;
use Exception;
use Illuminate\Console\Command;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:example-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws ApiException
     */
    public function handle(GDriveCreateFolderService $createFolder, GDriveSendDocumentService $sendFiles)
    {
/*
 *  send file to drive
            try {


                $client = new Client();
                putenv('GOOGLE_APPLICATION_CREDENTIALS='. Storage::path('credentials.json'));
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);

                $file = Storage::path('public/cli_draft_Полівчук Аліна Валеріївна.docx');
                $fileName = basename($file);
                $mimeType = mime_content_type($file);


                $fileMetadata = new Drive\DriveFile(
                    array(
                        'name' => $fileName,
                        'parents' => ['1h0FaiKvyzadgOfMqm25xS87pXVg20tq5'],
                    )
                    );
                $content = file_get_contents($file);
                $file = $driveService->files->create($fileMetadata, array(
                    'data' => $content,
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id'));

                Log::info($file->id);

            } catch(Exception $error) {
                Log::info($error);
            }

//create folder drive
        try {
            $client = new Client();
            putenv('GOOGLE_APPLICATION_CREDENTIALS='. Storage::path('credentials.json'));
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile(array(
                'name' => 'Тест укр',
                'parents' => ['1h0FaiKvyzadgOfMqm25xS87pXVg20tq5'],
                'mimeType' => 'application/vnd.google-apps.folder'));
            $file = $driveService->files->create($fileMetadata, array(
                'fields' => 'id'));

            Log::info($file->id);

        }catch(Exception $e) {
            echo "Error Message: ".$e;
        }
*/
        $array = [
            '0' => 'Чокінта Ілона Вікторівна 666995649',
            '1' => 'м. Бровари вул. Василя Симоненка. 2 б',
            '2' => 'Good Food RT78l чорного кольору , СН:GF78293739',
        ];










    }
}
