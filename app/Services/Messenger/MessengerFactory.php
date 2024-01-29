<?php

namespace App\Services\Messenger;

use App\Enums\MessengerEnum;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;

class MessengerFactory
{
    public function handle(MessengerEnum $messenger): MessengerInterface
    {
        return match($messenger){
          MessengerEnum::TELEGRAM=>app(TelegramMessengerService::class),
        };
    }
}
