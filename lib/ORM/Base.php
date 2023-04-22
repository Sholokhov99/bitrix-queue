<?php

namespace Task\Queue\ORM;

use Exception;

use Task\Queue\Service\DTO\ORM\Job;
use Task\Queue\Interfaces\ORM\IJob;
use Task\Queue\Interfaces\ORM\IORM;

use Bitrix\Main\{
    Entity,
    ArgumentException,
    SystemException,
    ObjectPropertyException
};
use Bitrix\Main\Type\Date;
use Bitrix\Main\ORM\Data\DataManager;

/**
 * Базовый класс описывающий ORM модуля.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
abstract class Base extends DataManager implements IORM
{
    /**
     * Код поля хранения обработчика задачи.
     */
    public const FIELD_TASK = "TASK";

    /**
     * Код поля хранения параметров задачи.
     */
    public const FIELD_PARAMS = "PARAMS";

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
            new Entity\StringField(static::FIELD_TASK, []),
            new Entity\TextField(static::FIELD_PARAMS, []),
            new Entity\DatetimeField(static::FIELD_DATE_CREATE, []),
            new Entity\DatetimeField(static::FIELD_DATE_UPDATE, []),
        ];
    }

    /**
     * Получение количество задач в очереди.
     *
     * @param $filter
     * @param array $cache
     * @return int
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getCount($filter = [], array $cache = []): int
    {
        return intval(parent::getCount($filter, $cache));
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

    /**
     * Получение модели задачи на основе массива.
     *
     * @todo Переделать на нормальный механизм.
     *
     * @param array $data
     * @return IJob
     */
    protected static function arrayToJob(array $data): IJob
    {
        $id = intval($data['ID'] ?? 0);

        $parameters = !empty($data[static::FIELD_PARAMS]) ? unserialize($data[static::FIELD_PARAMS]) : [];
        $task = (string)($data[static::FIELD_TASK] ?? '');
        $dateCreate = $data[static::FIELD_DATE_CREATE] ?? null;
        $dateUpdate = $data[static::FIELD_DATE_UPDATE] ?? null;

        $job = (new Job())->setId($id)
            ->setTask($task);

        if (is_array($parameters)) {
            $job->setParameters($parameters);
        }

        if ($dateCreate instanceof Date) {
            $job->setDateCreate($dateCreate);
        }

        if ($dateUpdate instanceof Date) {
            $job->setDateUpdate($dateUpdate);
        }

        return $job;
    }

    /**
     * Получение первой задачи.
     *
     * @param array $filter
     * @return IJob|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getFirst(array $filter = []): ?IJob
    {
        $order = ['ID' => 'ASC'];
        $limit = 1;

        $parameters = compact('filter', 'order', 'limit');

        if ($item = static::getList($parameters)->fetch()) {
            return static::arrayToJob($item);
        }

        return null;
    }
}