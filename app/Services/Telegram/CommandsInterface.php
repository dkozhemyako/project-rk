<?php

namespace App\Services\Telegram;

use App\Services\Messenger\MessageDTO;

interface CommandsInterface
{
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO;
}
