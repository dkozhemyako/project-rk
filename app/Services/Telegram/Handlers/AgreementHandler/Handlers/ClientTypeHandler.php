<?php

namespace App\Services\Telegram\Handlers\AgreementHandler\Handlers;

use App\Enums\TypeClientEnum;
use App\Repositories\ClientAgreement\DTO\ClientAgreementDTO;
use App\Services\Telegram\Handlers\AgreementHandler\AgreementInterface;
use App\Services\Telegram\Handlers\AgreementHandler\DTO\AgreementDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientTypeHandler implements AgreementInterface
{
    public const AGR_STAGE_CLIENT_TYPE = '_CLIENT_TYPE';


    private array $replyMarkup =
        [
            'keyboard' =>
                [
                    [ //строка
                        [ //кнопка
                            'text' => '👨‍💻 Фізична особа-підприємець',
                        ],
                        [ //кнопка
                            'text' => '👨‍💼 Фізична особа',
                        ],

                    ],
                ],
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ];

    public function handle(AgreementDTO $agreementDTO, Closure $next): AgreementDTO
    {
        $key = $agreementDTO->getSenderId() . self::AGR_STAGE_CLIENT_TYPE;

        if (Redis::exists($key) == true){

            $agreementDTO->setClientAgreementDTO(new ClientAgreementDTO());
            $agreementDTO->getClientAgreementDTO()->setType(TypeClientEnum::from(Redis::get($key)));
            $agreementDTO->getClientAgreementDTO()->setTelegramId($agreementDTO->getSenderId());
            return $next($agreementDTO);
        }

        $checkValue = TypeClientEnum::tryFrom($agreementDTO->getMessage());

        if (is_null($checkValue) == true) {

            $agreementDTO->setMessage('🤦 Помилка вводу. Оберіть значення з меню 👇');
            $agreementDTO->setReplyMarkup($this->replyMarkup);
            return $agreementDTO;
        }
        Redis::set($key, $agreementDTO->getMessage(), 'EX', 260000);

        if ($agreementDTO->getMessage() === TypeClientEnum::FOP->value){
            $agreementDTO->setMessage(
                'Під час заповнення форми, Вам треба буде надати скріни/фото наступних документів:' .PHP_EOL.
                '- Витяг з ЄДР'.PHP_EOL.
                '- Фото договору оренди або права власності або талон на МАФ,'.PHP_EOL.
                'приміщення в яке планується встановлення орендованого обладнання.'.PHP_EOL.
                '(особисті данні орендодавця можна приховати/замалювати)'.PHP_EOL.PHP_EOL.
                'Завантажте витяг з ЄДР 📎'

            );
        }

        if ($agreementDTO->getMessage() === TypeClientEnum::FO->value){
            $agreementDTO->setMessage(
                'Під час заповнення форми, Вам треба буде надати скріни/фото наступних документів:' .PHP_EOL.
                '- Паспорт 1 і 2 сторінка'.PHP_EOL.
                '- Прописка або витяг промісце реєстрації'.PHP_EOL.
                '- Фото договору оренди або права власності або талон на МАФ,'.PHP_EOL.
                'приміщення в яке планується встановлення орендованого обладнання.'.PHP_EOL.
                '(особисті данні орендодавця можна приховати/замалювати)'.PHP_EOL.PHP_EOL.
                'Завантажте фото першої сторінки паспорту 📎'
            );
        }

        return $agreementDTO;
    }
}
