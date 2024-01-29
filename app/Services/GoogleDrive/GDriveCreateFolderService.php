<?php

namespace App\Services\GoogleDrive;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GDriveCreateFolderService
{
    private const GD_BASE_FOLDER_ID = '1h0FaiKvyzadgOfMqm25xS87pXVg20tq5';
    public function createFolder(array $path): string
    {
        $foldersId = ['0' => self::GD_BASE_FOLDER_ID];
        foreach ($path as $value){
            try {
                $client = new Client();
                putenv('GOOGLE_APPLICATION_CREDENTIALS='. Storage::path('credentials.json'));
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);

                $fileMetadata = new Drive\DriveFile(array(
                    'name' => $value,
                    'parents' => [end($foldersId)],
                    'mimeType' => 'application/vnd.google-apps.folder'));
                $file = $driveService->files->create($fileMetadata, array(
                    'fields' => 'id'));

                $foldersId[] = $file->id;

            }catch(Exception $error) {
                Log::info('Google drive create folder error: ' . $error);
            }
        }

        return end($foldersId);
    }
}
