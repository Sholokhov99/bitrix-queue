<?php

namespace Task\Queue\ORM;

use Exception;

use Bitrix\Main\ORM\Data\DataManager;

/**
 * Базовый класс описывающий ORM модуля.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Base extends DataManager
{
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