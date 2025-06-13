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
    case agreementBack = '⬅ Попередній крок';
    case agreementAdminBack = '↖ Попередній крок';
    case coffeeMachineBack = '↖ Повернутись назад';
    case coffeeGrinderBack = '↗ Повернутись назад';
    case equipmentFrosty75l = '™ Frosty 78L';
    case equipmentFrostyRT98l = '™ Frosty RT98L';
    case equipmentGooderXC68L = '™ Gooder XC68L';
    case equipmentGooderXCW100L = '™ Gooder XCW-100L';
    case equipmentGooderXCW120LS = '™ Gooder XCW-120LS';
    case equipmentGooderXCW160CUBE = '™ Gooder XCW-160 CUBE';
    case equipmentGooderXCW160LS = '™ Gooder XCW-160LS';

    case equipmentGooderXCW120CUBE = '™ Gooder XCW-120 CUBE';

    case equipmentCassadioDieci = '📷 Фото зразків обладнання';
    case equipmentFiorenzatoF64 = '📸 Фото зразків обладнання';
    case equipmentFrosty75lVideo = '🎬 Відео інструкція 78L';
    case equipmentFrostyRT98LVideo = '🎬 Відео інструкція RT98L';
    case equipmentGooderXC68LVideo = '🎬 Відео інструкція XC68L';
    case equipmentGooderXCW100LVideo = '🎬 Відео інструкція XCW-100L';
    case equipmentGooderXCW120LSVideo = '🎬 Відео інструкція XCW-120LS';
    case equipmentGooderXCW160CUBEVideo = '🎬 Відео інструкція XCW-160 CUBE';
    case equipmentGooderXCW160LSVideo = '🎬 Відео інструкція XCW-160LS';
    case equipmentGooderXCW120CUBEVideo = '🎬 Відео інструкція XCW-120 CUBE';
    case equipmentFrosty75lCharacteristics = '📌 Характеристики 78L';
    case equipmentFrostyRT98lCharacteristics = '📌 Характеристики RT98L';

    case equipmentGooderXC68LCharacteristics = '📌 Характеристики XC68L';
    case equipmentGooderXCW100LCharacteristics = '📌 Характеристики XCW-100L';
    case equipmentGooderXCW120LSCharacteristics = '📌 Характеристики XCW-120LS';
    case equipmentGooderXCW160CUBECharacteristics = '📌 Характеристики XCW-160 CUBE';

    case equipmentGooderXCW120CUBECharacteristics = '📌 Характеристики XCW-120 CUBE';

    case equipmentGooderXCW160LSCharacteristics = '📌 Характеристики XCW-160LS';

    case equipmentCassadioDieciCharacteristics = '📌 Характеристики кавоварок';
    case equipmentFiorenzatoF64Characteristics = '📌 Характеристики кавомолок';

    case equipmentFrosty75lPhoto = '📸 Фото обладнання 78L';
    case equipmentFrostyRT98lPhoto = '📸 Фото обладнання RT98L';
    case equipmentGooderXC68LPhoto = '📸 Фото обладнання XC68L';
    case equipmentGooderXCW100LPhoto = '📸 Фото обладнання XCW-100L';
    case equipmentGooderXCW120LSPhoto = '📸 Фото обладнання XCW-120LS';
    case equipmentGooderXCW160CUBEPhoto = '📸 Фото обладнання XCW-160 CUBE';
    case equipmentGooderXCW160LSPhoto = '📸 Фото обладнання XCW-160LS';
    case equipmentGooderXCW120CUBEPhoto = '📸 Фото обладнання XCW-120 CUBE';
    case equipmentRentalConditions = '📝 Умови оренди';
    case clientCheckAgreementTrue = '✅ Приймаю умови договору';
    case clientCheckAgreementFalse = '❗ Необхідні уточнення';
    case adminSignedAgreement = 'Відправити підписаний договір';





}
