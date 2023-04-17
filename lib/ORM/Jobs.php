<?php

namespace Task\Queue\ORM;

use Exception;
use InvalidArgumentException;

use Task\Queue\Interfaces\ORM\IJob;
use Task\Queue\Service\DTO\ORM\Job;


use Bitrix\Main\{Entity,
    ArgumentException,
    ObjectPropertyException,
    SystemException
};
use Bitrix\Main\Type\Date;
use Bitrix\Main\ORM\Data\{AddResult, UpdateResult};

/**
 * ORM, для взаимодействия с очередью, которая в текущий момен обрабатывается
 * или ожидает взаимодействия.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class JobsTable extends Base
{
    /**
     * Новая задача, которая не находится в обработке.
     */
    public const STATUS_NEW = "N";

    /**
     * Задача, которая находится в процессе обработки.
     */
    public const STATUS_PROCESS = "P";

    /**
     * Код поля хранения обработчика задачи.
     */
    protected const FIELD_TASK = "TASK";

    /**
     * Код поля хранения статуса задачи.
     */
    protected const FIELD_STATUS = "STATUS";

    /**
     * Код поля хранения параметров задачи.
     */
    protected const FIELD_PARAMS = "PARAMS";

    /**
     * Код поля хранения даты создания задачи.
     */
    protected const FIELD_DATE_CREATE = "DATE_CREATE";

    /**
     * Код поля хранения даты обновления задачи.
     */
    protected const FIELD_DATE_UPDATE = "DATE_UPDATE";

    /**
     * Получение наименования таблицы с очередями.
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return "b_queue_jobs";
    }

    /**
     * Получение описания таблицы по правилам ORM.
     *
     * <li>ID - Уникальный идентификатор очереди
     * <li>TASK - Обработчик очереди
     * <li>STATUS - Текущий статус задачи
     * <li>PARAMS - Параметры задачи
     * <li>DATE_CREATE - Дата создания очереди
     * <li>DATE_UPDATE - Дата последнего обновления задачи
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
            new Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]),
            new Entity\StringField(static::FIELD_TASK, []),
            new Entity\EnumField(static::FIELD_STATUS, ['values' => [static::STATUS_NEW, static::STATUS_PROCESS]]),
            new Entity\TextField(static::FIELD_PARAMS, []),
            new Entity\DatetimeField(static::FIELD_DATE_CREATE, []),
            new Entity\DatetimeField(static::FIELD_DATE_UPDATE, []),
        ];
    }

    /**
     * Добавление новой задачи.
     *
     * @param array $data
     * @return AddResult
     * @throws Exception
     */
    public static function add(array $data = []): AddResult
    {
        if (empty($data['entity'])) {
            throw new InvalidArgumentException('The task object was not found');
        }

        if (!isset($data['params']) || !is_array($data['params'])) {
            $data['params'] = [];
        }

        $fields = [
            static::FIELD_TASK => $data['entity'],
            static::FIELD_PARAMS => serialize($data['params']),
            static::FIELD_STATUS => static::STATUS_NEW,
        ];

        return parent::add($fields);
    }

    /**
     * Получение списка задач.
     *
     * @param array $parameters
     *
     * @return IJob[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getList(array $parameters = []): array
    {
        $result = [];
        $iterator = parent::getList($parameters);

        while ($item = $iterator->fetch()) {
            $id = intval($item['ID'] ?? 0);

            $parameters = [];

            if (!empty($item[static::FIELD_PARAMS]) && CheckSerializedData($item[static::FIELD_PARAMS], 999)) {
                $parameters = unserialize(static::FIELD_PARAMS);
            }

            $job = (new Job())->setId($id)
                ->setTask($item[static::FIELD_TASK] ?? '')
                ->setStatus($item[static::FIELD_STATUS] ?? '');

            if (is_array($parameters)) {
                $job->setParameters($parameters);
            }

            $dateCreate = $item[static::FIELD_DATE_CREATE] ?? null;
            $dateUpdate = $item[static::FIELD_DATE_UPDATE] ?? null;

            if ($dateCreate instanceof Date) {
                $job->setDateCreate($dateCreate);
            }

            if ($dateUpdate instanceof Date) {
                $job->setDateUpdate($dateUpdate);
            }

            $result[$id] = $job;
        }

        return $result;
    }

    /**
     * Обновление статуса у задачи.
     *
     * @param int $id
     * @param string $status
     * @return UpdateResult
     * @throws Exception
     */
    public static function setStatus(int $id, string $status): UpdateResult
    {
        if (!static::validateStatus($status)) {
            throw new InvalidArgumentException('Unknown status');
        }

        return static::update($id, [static::FIELD_STATUS => $status]);
    }

    /**
     * Получение первной новой задачи.
     *
     * @return IJob|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getOne(): ?IJob
    {
        $filter = [static::FIELD_STATUS => static::STATUS_NEW];
        return static::getFirst($filter);
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
        $result = null;

        $order = ['ID' => 'ASC'];
        $limit = 1;

        $parameters = compact('filter', 'order', 'limit');

        $jobs = static::getList($parameters);

        if (!empty($jobs)) {
            $result = reset($jobs);
        }

        return $result;
    }

    /**
     * Валидация статуса
     *
     * @param string $status
     * @return bool
     */
    public static function validateStatus(string $status): bool
    {
        switch ($status) {
            case static::STATUS_NEW:
            case static::STATUS_PROCESS:
                $result = true;
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }
}