<?php

namespace Task\Queue\ORM;

use Exception;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Data\DataManager;

/**
 * Базовый класс описывающий ORM модуля.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Base extends DataManager
{
    /**
     * Код поля хранения даты создания задачи.
     */
    protected const FIELD_DATE_CREATE = "DATE_CREATE";

    /**
     * Код поля хранения даты обновления задачи.
     */
    protected const FIELD_DATE_UPDATE = "DATE_UPDATE";

    /**
     * Получение описания таблицы по правилам ORM.
     *
     * <li>ID - Уникальный идентификатор очереди
     * <li>DATE_CREATE - Дата создания очереди
     * <li>DATE_UPDATE - Дата последнего обновления задачи
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]),
            new Entity\DatetimeField(static::FIELD_DATE_CREATE, []),
            new Entity\DatetimeField(static::FIELD_DATE_UPDATE, []),
        ];
    }

    /**
     * Создание таблицы.
     *
     * @return bool
     */
    public static function createTable(): bool
    {
        try {
            $entity = self::getEntity();
            $connection = $entity->getConnection();
            if (!$connection->isTableExists($entity->getDBTableName())) {
                $sql = $entity->compileDbTableStructureDump();
                $connection->query($sql[0]);
                return $connection->isTableExists($entity->getDBTableName());
            }
            return true;
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Удаление таблицы.
     *
     * @return bool
     */
    public static function dropTable(): bool
    {
        try {
            $entity = self::getEntity();
            $connection = $entity->getConnection();
            if ($connection->isTableExists($entity->getDBTableName())) {
                $connection->dropTable($entity->getDBTableName());
                return !$connection->isTableExists($entity->getDBTableName());
            }
            return true;
        } catch (Exception $e) {
        }
        return false;
    }
}