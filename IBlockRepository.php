<?php

namespace Bizapps\Rdp\Repositories;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

if (!Loader::includeModule('iblock')) {
    $error = 'Модуль Iblock не установлен';

    throw new LoaderException($error);
}

/**
 *
 */
abstract class IBlockRepository
{
    /**
     * @var string
     */
    protected static string $entityClass;

    /**
     * @var string
     */
    protected static string $iblockTableCode;

    /**
     * @param $filter
     * @param $select
     * @return array
     */
    public static function getList($filter = [], $select = []): array
    {
        $result = [];

        $entityFilter = [];
        if ($filter) {
            $entityFilter = array_replace($entityFilter, $filter);
        }
        $entitySelect = ['*'];
        if ($filter) {
            $entitySelect = array_replace($entitySelect, $select);
        }
        $entityIterator = static::$entityClass::getList([
            'select' => $entitySelect,
            'filter' => $entityFilter
        ]);

        while ($entityObject = $entityIterator->fetchObject()) {
            $result[] = $entityObject;
        }

        return $result;
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getXmlId(int $id): ?string
    {
        $entityObject = static::$entityClass::getById($id)->fetchObject();

        return $entityObject?->getXmlId();
    }

    /**
     * @param int $id
     * @param $select
     * @return mixed
     */
    public static function getById(int $id, $select = []): mixed
    {
        if ($id) {
            $select[] = '*';
            return static::$entityClass::getByPrimary($id, ['select' => $select])->fetchObject();
        } else {
            return null;
        }
    }

    /**
     * @param string $xmlId
     * @return mixed
     */
    public static function getByXmlId(string $xmlId): mixed
    {
        if ($xmlId) {
            return self::getList(['XML_ID' => $xmlId])[0];
        } else {
            return null;
        }
    }

    /**
     * @return int|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getIblockId(): ?int
    {
        return IblockTable::getList(['filter' => ['CODE' => static::$iblockTableCode]])->fetchObject()?->getId();
    }
}