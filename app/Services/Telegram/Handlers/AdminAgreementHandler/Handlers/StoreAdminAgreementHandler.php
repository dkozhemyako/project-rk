<?php

namespace App\Services\Telegram\Handlers\AdminAgreementHandler\Handlers;


use App\Enums\TelegramCommandEnum;
use App\Enums\TypeClientEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\Handlers\AdminAgreementHandler\AdminAgreementInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use Aspose\Words\ApiException;
use Aspose\Words\Model\Requests\ConvertDocumentRequest;
use Aspose\Words\WordsApi;
use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class StoreAdminAgreementHandler implements AdminAgreementInterface
{
    public const AGR_STORE_ADMIN = '_ADMIN_STORE_AGREEMENT';
    public function __construct(
        protected AdminAgreementRepository $repository,
        protected TelegramMessengerService $messengerService,
    ){}

    /**
     * @throws GuzzleException
     * @throws ApiException
     */
    public function handle(AdminAgreementDTO $adminAgreementDTO, Closure $next): AdminAgreementDTO
    {
        $key = $adminAgreementDTO->getSenderId() . self::AGR_STORE_ADMIN;

        if (Redis::exists($key) == true) {
            return $next($adminAgreementDTO);
        }
        $this->repository->store($adminAgreementDTO);
        $adminAgreementDTO->setMessage(
            '💬 Договір сформовано.'.PHP_EOL.
            'За необхідності відредагуйте його та відправте настпуним повідомленням файл .docx який буде відправлено клієнту для погодження.'
        );

        $adminAgreementDTO->setReplyMarkup($this->replyMarkup());

        /*
         #відправка pdf
        $dtoFile = new MessageDTO(
            config('messenger.telegram.ngrok').'/storage/'.$adminAgreementDTO->getFileAgreementAdmin(), //краще переробити
            $adminAgreementDTO->getSenderId()
        );
        $dtoFile->setReplyMarkup(['caption' => 'Договір PDF для підпису']); //краще переробити

        $this->messengerService->send($dtoFile);

        #конвертація pdf to docx

        $wordsApi = new WordsApi(config('aspose.cloud.id'), config('aspose.cloud.secret'));
        $doc = storage_path('app/public/'.$adminAgreementDTO->getFileAgreementAdmin());

        $request = new ConvertDocumentRequest($doc, "docx");
        $convert = $wordsApi->convertDocument($request);
        $nameDocx = time().'.docx';
        Storage::disk('public')->put($nameDocx, $convert->fread($convert->getSize()));
        */


        #refactor
        $arrayQuery = array(
            'chat_id' => $adminAgreementDTO->getSenderId(),
            'caption' => 'Договір DOCX для корегування',
            'document' => curl_file_create(storage_path('app/public/'.$adminAgreementDTO->getFileAgreementAdmin()))
        );
        $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);

        Redis::set($key, 'checked', 'EX', 260000);

        return $adminAgreementDTO;
    }

    private function replyMarkup(): array
    {
        return [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => TelegramCommandEnum::agreementAdminBack->value,
                        ],
                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];
    }
}
