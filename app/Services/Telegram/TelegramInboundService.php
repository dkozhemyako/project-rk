<?php

namespace App\Services\Telegram;

use App\Enums\TelegramCommandEnum;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TelegramInboundService
{

    public function __construct(
        protected TelegramMessengerService $messengerService,
        protected CommandsFactory $commandsFactory,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function handle(InboundDTO $data)
    {

        $command = TelegramCommandEnum::tryFrom($data->getMessage());

        if (is_null($command)) {
            $command = TelegramCommandEnum::tryFrom(Redis::get('lastCommand' . $data->getSenderId()));
        }

        if (is_null($command)) {
            $command = TelegramCommandEnum::from('/start');
        }

        Redis::set('lastCommand' . $data->getSenderId(), $command->value, 'EX', 260000);

        #Технічне обслуговування
        if (config('messenger.telegram.maintenance') != null && $data->getSenderId() != config('messenger.telegram.maintenance_admin')){
            $dto = new MessageDTO(
                '🔧 Вибачте, проводяться технічні роботи, спробуйте пізніше.',
                $data->getSenderId(),
            );
            $this->messengerService->send($dto);
            return;
        }
        $service = $this->commandsFactory->handle($command);
        $this->messengerService->send(
            $service->handle($data->getMessage(), $data->getSenderId(), $data->getFileName(), $data->getCallBackData(), $data->getMediaGroupId()),
        );

    }
}
