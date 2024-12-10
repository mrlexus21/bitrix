<?php

namespace Bizapps\Rdp\Repositories;

use Bitrix\Main\Type\Date;
use Bizapps\Rdp\DTO\ProtocolAddDto;
use CIBlockElement;

class ProtocolRepository extends IBlockRepository
{
    protected static string $entityClass = 'Bitrix\Iblock\Elements\ElementProtocolTestsTable';
    protected static string $iblockTableCode = 'protocol_tests';

    public static function add(ProtocolAddDto $protocolAddDto)
    {
        //согласно руководству \Bitrix\Iblock\ElementTable::add() метод заблокирован, юзаем старое ядро
        //$dealExist = self::getList(['filter' => ['DEAL' => $protocolAddDto->dealId]]);
        $protocolIterator = CIBlockElement::getList(
            [],
            [
                'IBLOCK_ID' => self::getIblockId(),
                'PROPERTY_DEAL' => $protocolAddDto->dealId
            ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'PROPERTY_DEAL']
        );
        while ($protocol = $protocolIterator->fetch()) {
            self::$entityClass::delete($protocol['ID']);
        }
        $ob = new CIBlockElement();
        return $ob->add([
            'NAME' => 'Протокол испытания ' . $protocolAddDto->fileId . ' от ' . new Date(),
            'IBLOCK_ID' => parent::getIblockId(),
            'PROPERTY_VALUES' => [
                'TEST_REPORT' => $protocolAddDto->fileId,
                'DEAL' => $protocolAddDto->dealId,
                'LABORATORIYA' => $protocolAddDto->laboratoryId,
            ]
        ]);
    }

    public static function update($protocolId, ProtocolAddDto $protocolAddDto)
    {
        $ob = new CIBlockElement();
        return $ob->update($protocolId, [
            'NAME' => 'Протокол испытания ' . $protocolAddDto->fileId . ' от ' . new Date(),
            'IBLOCK_ID' => parent::getIblockId(),
            'PROPERTY_VALUES' => [
                'TEST_REPORT' => $protocolAddDto->fileId,
                'LABORATORIYA' => $protocolAddDto->laboratoryId,
            ]
        ]);
    }
}