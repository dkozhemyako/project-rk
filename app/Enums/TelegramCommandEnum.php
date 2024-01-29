<?php

namespace App\Enums;

enum TelegramCommandEnum: string
{
    case start = '/start';
    case agreement = '📋 Сформувати договір';
    case equipment = '❄ Холодильні вітрини';
    case adminAgreement = 'Продовжити';
    case clientAgreement = '📩 Відправити підписаний договір';
    case returnMain = '⬆ На головну';
    case equipmentWait = '⬜ Очікується';
    case equipmentBack = '⬅ Повернутись назад';
    case equipmentFrosty75l = '™ Frosty 78L';
    case equipmentFrosty75lVideo = '🎬 Відео інструкція';
    case equipmentFrosty75lCharacteristics = '📌 Характеристики';

    case equipmentFrosty75lPhoto = '📸 Фото обладнання';
    case equipmentRentalConditions = '📝 Умови оренди';
    case clientCheckAgreementTrue = '✔ Приймаю умови договору';
    case clientCheckAgreementFalse = '❗ Необхідні уточнення';
    case adminSignedAgreement = 'Відправити підписаний договір';





}
