<?php

namespace App\Services\Messenger\TelegramMessenger;

use App\Services\Messenger\MessengerInterface;
use App\Services\Messenger\MessageDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TelegramMessengerService implements MessengerInterface
{
    public function __construct(
        protected Client $client,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function send(MessageDTO $messageDTO): bool
    {
        if (is_null($messageDTO->getSenderId())) {
            $messageDTO->setSenderId(config('messenger.telegram.chat_id'));
        }

        if ($messageDTO->getFile() != null){
            $this->client->post(
                config('messenger.telegram.url_document'),
                [
                    'json' => [
                        'chat_id' => $messageDTO->getSenderId(),
                        'caption' => $messageDTO->getReplyMarkup()['caption'],
                        'document' => $messageDTO->getFile(),
                    ],
                ]
            );
            return true;
        }

        if ($messageDTO->getParseMode() != ''){
            $this->client->post(
                config('messenger.telegram.url'),
                [
                    'json' => [
                        'chat_id' => $messageDTO->getSenderId(),
                        'text' => $messageDTO->getMessage(),
                        'parse_mode' => $messageDTO->getParseMode(),
                        'reply_markup' => $messageDTO->getReplyMarkup(),
                    ],
                ]
            );
            return true;
        }

        if (array_key_exists('keyboard', $messageDTO->getReplyMarkup()) === true) {
            $this->client->post(
                config('messenger.telegram.url'),
                [
                    'json' => [
                        'chat_id' => $messageDTO->getSenderId(),
                        'text' => $messageDTO->getMessage(),
                        'reply_markup' => $messageDTO->getReplyMarkup(),

                    ],
                ]
            );
            return true;
        }

        if (array_key_exists('inline_keyboard', $messageDTO->getReplyMarkup()) === true) {
            $this->client->post(
                config('messenger.telegram.url'),
                [
                    'json' => [
                        'chat_id' => $messageDTO->getSenderId(),
                        'text' => $messageDTO->getMessage(),
                        'reply_markup' => $messageDTO->getReplyMarkup(),
                    ],
                ]
            );
            return true;
        }

        if (array_key_exists('caption', $messageDTO->getReplyMarkup()) === true) {
            $this->client->post(
                config('messenger.telegram.url_document'),
                [
                    'json' => [
                        'chat_id' => $messageDTO->getSenderId(),
                        'caption' => $messageDTO->getReplyMarkup()['caption'],
                        'document' => $messageDTO->getMessage(),
                    ],
                ]
            );
            return true;
        }



        $this->client->post(
            config('messenger.telegram.url'),
            [
                'json' => [
                    'chat_id' => $messageDTO->getSenderId(),
                    'text' => $messageDTO->getMessage(),
                ],
            ]
        );

        return true;
    }
}
