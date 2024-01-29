<?php

namespace App\Services\Telegram;

use Illuminate\Support\Carbon;

class InboundDTO
{
    protected string $fileName;
    protected string $message = '';
    protected int $callBackData = 0;
    protected int $senderId;
    protected int $mediaGroupId = 0;

    public function __construct(array $data, string $fileName)
    {
        if (array_key_exists('callback_query',  $data)){
            $this->callBackData = $data['callback_query']['data'];
            $this->message = $data['callback_query']['message']['reply_markup']['inline_keyboard']['0']['0']['text']; //if only one inline_keyboard sand
            $this->senderId = $data['callback_query']['from']['id'];
        }
        if(array_key_exists('message', $data)){
            if (array_key_exists('text',  $data['message'])){
                $this->message = $data['message']['text'];

            }
            if (array_key_exists('from', $data['message'])){
                $this->senderId = $data['message']['from']['id'];
            }
            if (array_key_exists('media_group_id', $data['message'])){
                $this->mediaGroupId = $data['message']['media_group_id'];
            }
        }
        if(array_key_exists('edited_message', $data)){
            if (array_key_exists('text',  $data['edited_message'])){
                $this->message = $data['edited_message']['text'];

            }
            if (array_key_exists('from', $data['edited_message'])){
                $this->senderId = $data['edited_message']['from']['id'];
            }
        }



        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getMediaGroupId(): int
    {
        return $this->mediaGroupId;
    }

    /**
     * @return string
     */
    public function getCallBackData(): string
    {
        return $this->callBackData;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

}
/*
 * example inbound data json from telegram
{
    "update_id": 20629194,
  "message": {
    "message_id": 11,
    "from": {
        "id": 912016646,
      "is_bot": false,
      "first_name": "Dima",
      "last_name": "Kozhemyako",
      "language_code": "uk"
    },
    "chat": {
        "id": 912016646,
      "first_name": "Dima",
      "last_name": "Kozhemyako",
      "type": "private"
    },
    "date": 1696342523,
    "text": "test"
  }
}
*/
