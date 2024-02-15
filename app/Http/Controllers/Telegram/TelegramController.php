<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\InboundDTO;
use App\Services\Telegram\TelegramInboundService;
use App\Services\Telegram\TelegramGetFileService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class TelegramController extends Controller
{
    public function __construct(
        protected TelegramInboundService $service,
        protected TelegramGetFileService $fileService,
    ){}

    /**
     * @throws GuzzleException
     */
    public function index(Request $request): string
    {

        $fix = $request->all();
        if (array_key_exists('my_chat_member', $fix)){
            if (array_key_exists('new_chat_member', $fix['my_chat_member'])){
                if (array_key_exists('status', $fix['my_chat_member']['new_chat_member'])){
                    if (
                        $fix['my_chat_member']['new_chat_member']['status'] === 'kicked'
                        ||
                        $fix['my_chat_member']['new_chat_member']['status'] === 'member'
                    ){
                            return '';
                    }
                }
            }
        }

        Log::info($request);

        Redis::incr('all_request_calculator30112023');
        $fileName = $this->fileService->getFile($request->all());
        $data = new InboundDTO($request->all(), $fileName);

        $this->service->handle($data);

        return '';

    }



}
