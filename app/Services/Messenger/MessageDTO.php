<?php

namespace App\Services\Messenger;

class MessageDTO
{
    protected array $replyMarkup = [];
    protected string $parseMode = '';
    protected $file = null;

    public function __construct(
        protected string $message,
        protected ?int   $senderId = null,


    ){}


    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): void
    {
        $this->file = $file;
    }



    /**
     * @return string
     */
    public function getParseMode(): string
    {
        return $this->parseMode;
    }

    /**
     * @param string $parseMode
     */
    public function setParseMode(string $parseMode): void
    {
        $this->parseMode = $parseMode;
    }

    /**
     * @param int|null $senderId
     */
    public function setSenderId(?int $senderId): void
    {
        $this->senderId = $senderId;
    }

    /**
     * @param array $replyMarkup
     */
    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    /**
     * @return array
     */
    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
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
    public function getSenderId(): ? int
    {
        return $this->senderId;
    }


}
