<?php

namespace App\Services\Telegram\Handlers\AdminSignedAgreementHandler;

use App\Enums\TelegramCommandEnum;
use App\Repositories\AdminAgreement\AdminAgreementRepository;
use App\Repositories\ClientAgreement\ClientAgreementRepository;
use App\Services\Messenger\MessageDTO;
use App\Services\Messenger\TelegramMessenger\TelegramMessengerService;
use App\Services\Telegram\CommandsInterface;
use App\Services\Telegram\Handlers\AdminAgreementHandler\DTO\AdminAgreementDTO;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;


class AdminSignedAgreementHandler implements CommandsInterface
{
    public function __construct(
        protected AdminAgreementRepository $adminAgreementRepository,
        protected ClientAgreementRepository $clientAgreementRepository,
        protected TelegramMessengerService $messengerService,
    ){}

    public const ADM_SIGNED_STAGE = '_ADM_SIGNED_STAGE';

    /**
     * @throws GuzzleException
     */
    public function handle(string $message, int $senderId, string $fileName, int $callback, int $mediaGroupId): MessageDTO
    {
        $key = $senderId . self::ADM_SIGNED_STAGE;
        $dto = new AdminAgreementDTO(
            $callback,
            $message,
            $senderId,
            $fileName
        );

        if ($dto->getFileName() === ''){
            Redis::set($key, $callback, 'EX', 260000);
            return new MessageDTO(
                '🤦 Ви не завантажили жодного документу, повторіть спробу. Необхідно завантажити підписаний файл з розширенням .p7s',
                $dto->getSenderId()

            );
        }

        $zip = explode('.',$dto->getFileName());
        if (trim(end($zip)) != 'p7s'){
            return new MessageDTO(
                '🤦 Помилка відправки файлу. Необхідно завантажити файл з розширенням .p7s',
                $dto->getSenderId()
            );
        }

        if (Redis::exists($key) == true) {

            $callback = Redis::get($key);
            $clientInfo = $this->clientAgreementRepository->getClientFilesById($callback);

            $newFileName = 'adm_signed_' . $clientInfo->getName() . '.docx.p7s';
            Storage::disk('public')->move($dto->getFileName(), $newFileName);

            $this->adminAgreementRepository->updateSignedAgreement($callback, $newFileName);

            $result = $this->clientAgreementRepository->getClientTelegramIdById($callback);
            $clientId = $result['0']->telegram_id; //не вариант, разобратся с коллекциями

            $message = '💬 Вітаю, надсилаємо вам підписаний договір орендодавцем.'.PHP_EOL;
            $message .= 'Для того щоб перевірити підписаний договір перейдіть за посиланням - https://ca.diia.gov.ua/verify'.PHP_EOL;
            $message .= 'Для того щоб підписати договір оренди перейдіть за посиланням та завантажте отриманий файл - https://ca.diia.gov.ua/sign'.PHP_EOL;
            $message .= 'Далі слідуйте інструкціям та завантажте документ з підписом, файл з розширенням .p7s'.PHP_EOL.PHP_EOL;
            $message .= 'Після підпису натисніть кнопку нижче 👇 та слідуйте інструкціям.'.PHP_EOL;

            $ClientDto = new MessageDTO(
                $message,
                $clientId,
            );
            $ClientDto->setReplyMarkup($this->replyMarkup($callback));
            $this->messengerService->send($ClientDto);

            $clientInstructionMsg = new MessageDTO(
                "<a href='https://www.youtube.com/watch?v=PeB_6qHbT3M'>Посилання на відео інструкцію підпису</a>",
                $clientId,
            );
            $clientInstructionMsg->setParseMode('HTML');
            $clientInstructionMsg->setReplyMarkup($this->replyMarkupMain());
            $this->messengerService->send($clientInstructionMsg);
            //

            $arrayQuery = array(
                'chat_id' => $clientId,
                'caption' => 'Договір для підпису',
                'document' => curl_file_create(storage_path('app/public/'.$newFileName))
            );
            $ch = curl_init('https://api.telegram.org/bot'. config('messenger.telegram.token') .'/sendDocument');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            curl_close($ch);
            /*
            $dtoFile = new MessageDTO(
                config('messenger.telegram.ngrok').'/storage/'.$adminAgreementDTO->getFileName(), //краще переробити
                $clientId,
            );
            $dtoFile->setReplyMarkup(['caption' => 'Договір для підпису']); //краще переробити

            $this->messengerService->send($dtoFile);
            */

            Redis::del($key);
            return new MessageDTO(
                '💬 Договір відправлено клієнту для підпису, очікуйте підписані клієнтом документи.',
                $dto->getSenderId()
            );
        }

        return new MessageDTO(
            '💬 Сталася помилка, зверніться до адміністратора.',
            $dto->getSenderId()
        );
    }

    private function replyMarkup(int $agreementId): array
    {
        return
            [
                'inline_keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::clientAgreement->value,
                                'callback_data' => $agreementId,
                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }

    private function replyMarkupMain(): array
    {
        return
            [
                'keyboard' =>
                    [
                        [ //строка
                            [ //кнопка
                                'text' => TelegramCommandEnum::returnMain->value,

                            ],
                        ],
                    ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ];
    }

}
