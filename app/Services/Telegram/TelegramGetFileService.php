<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramGetFileService
{
    public function __construct(
        protected Client $client,
    ){

    }

    /**
     * @throws GuzzleException
     */
    public function getFile(array $data): string
    {
        if(array_key_exists('message', $data)){
            if (array_key_exists('document', $data['message'])){
                $fileId = $data['message']['document']['file_id'];
                $filePath = $this->getPath($fileId);

                return $this->copyFile($filePath);
            }
            if (array_key_exists('photo', $data['message'])){
                $lastKey = end($data['message']['photo']);
                $fileId = $lastKey['file_id'];
                $filePath = $this->getPath($fileId);

                return $this->copyFile($filePath);
            }
        }

        return '';

    }

    private function copyFile(string $filePath): string
    {
        $file_from_tgrm = config('messenger.telegram.url_get_file')."/".$filePath;
        $ext =  explode(".", $filePath);

        $name_our_new_file = time().".".end($ext);


        if (end($ext) == 'p7s'){
            $name_our_new_file = time() . '.docx.' . 'p7s';
        }

        Storage::disk('public')->put($name_our_new_file, file_get_contents($file_from_tgrm));

        return $name_our_new_file;
    }
    /**
     * @throws GuzzleException
     */
    private function getPath($fileId): string
    {
        $result = $this->client->post(
            config('messenger.telegram.url_get_path'),
            [
                'json' => [
                    'file_id' => $fileId,
                ],
            ]
        );

        $array = json_decode($result->getBody()->getContents(), true);
        return $array['result']['file_path'];

    }


}
