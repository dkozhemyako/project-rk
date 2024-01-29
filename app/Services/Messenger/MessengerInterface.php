<?php

namespace App\Services\Messenger;

interface MessengerInterface
{
    public function send(MessageDTO $messageDTO) : bool;
}
