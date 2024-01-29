<?php

return [
    'telegram' => [
        'url' =>'https://api.telegram.org/bot'. env('TELEGRAM_TOKEN', null) . '/sendMessage',
        'url_document' =>'https://api.telegram.org/bot'. env('TELEGRAM_TOKEN', null) . '/sendDocument',
        'url_get_path' =>'https://api.telegram.org/bot'. env('TELEGRAM_TOKEN', null) . '/getFile',
        'url_get_file' =>'https://api.telegram.org/file/bot' . env('TELEGRAM_TOKEN', null),
        'url_media_group' => 'https://api.telegram.org/bot'. env('TELEGRAM_TOKEN', null) . '/sendMediaGroup',
        'url_video' => 'https://api.telegram.org/bot'. env('TELEGRAM_TOKEN', null) . '/sendVideo',
        'chat_id' => env('TELEGRAM_CHAT_ID', null),
        'token' => env('TELEGRAM_TOKEN', null),
        'admin_id' => env('TELEGRAM_ADMIN_GROUP_ID', null),
        'ngrok' => env('TELEGRAM_NGROK', null),
        'maintenance' => env('MAINTENANCE', null),
        'maintenance_admin' => env('MAINTENANCE_ADMIN', null),
    ],
];

