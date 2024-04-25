<?php

namespace App\Enums;

enum TelegramCommandEnum: string
{
    case start = '/start';
    case agreement = '📋 Сформувати договір';
    case equipment = '❄ Холодильні вітрини';
    case equipmentCm = '☕ Кавоварки';
    case equipmentCg = '☕ Кавомолки';
    case adminAgreement = 'Продовжити';
    case clientAgreement = '📩 Відправити підписаний договір';
    case returnMain = '⬆ На головну';
    case equipmentWait = '⬜ Очікується';
    case equipmentBack = '⬅ Повернутись назад';
    case coffeeMachineBack = '↖ Повернутись назад';
    case coffeeGrinderBack = '↗ Повернутись назад';
    case equipmentFrosty75l = '™ Frosty 78L';
    case equipmentFrostyRT98l = '™ Frosty RT98L';

    case equipmentCassadioDieci = '📷 Фото зразків обладнання';
    case equipmentFiorenzatoF64 = '📸 Фото зразків обладнання';
    case equipmentFrosty75lVideo = '🎬 Відео інструкція 78L';
    case equipmentFrostyRT98LVideo = '🎬 Відео інструкція RT98L';
    case equipmentFrosty75lCharacteristics = '📌 Характеристики 78L';
    case equipmentFrostyRT98lCharacteristics = '📌 Характеристики RT98L';

    case equipmentCassadioDieciCharacteristics = '📌 Характеристики кавоварок';
    case equipmentFiorenzatoF64Characteristics = '📌 Характеристики кавомолок';

    case equipmentFrosty75lPhoto = '📸 Фото обладнання 78L';
    case equipmentFrostyRT98lPhoto = '📸 Фото обладнання RT98L';
    case equipmentRentalConditions = '📝 Умови оренди';
    case clientCheckAgreementTrue = '✔ Приймаю умови договору';
    case clientCheckAgreementFalse = '❗ Необхідні уточнення';
    case adminSignedAgreement = 'Відправити підписаний договір';





}
