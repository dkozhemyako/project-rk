<?php

namespace App\Services\GoogleDrive;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GDriveSendDocumentService
{
    public function handle(string $document, string $parent): string
    {


            try {

                $client = new Client();
                putenv('GOOGLE_APPLICATION_CREDENTIALS='. Storage::path('credentials.json'));
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);


                $file = Storage::path('public/' . $document);
                $fileName = basename($file);
                $mimeType = mime_content_type($file);


                $fileMetadata = new Drive\DriveFile(
                    array(
                        'name' => $fileName,
                        'parents' => [$parent],
                    )
                );

                $file = $driveService->files->create($fileMetadata, array(
                    'data' => file_get_contents($file),
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id'));

                return $file->id;


            } catch(Exception $error) {
                Log::info('Google drive send file error: ' . $error);
            }

            return 'error';
        }


}
