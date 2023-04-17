<?php

namespace Task\Queue\ORM;

use Exception;

use Task\Queue\Interfaces\ORM\IFailedJob;
use Task\Queue\Service\DTO\ORM\FailedJob;

use Bitrix\Main\{Entity,
    ArgumentException,
    ObjectPropertyException,
    SystemException
};
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\Type\Date;

/**
 * ORM, для взаимодействия с очередью, которая была обработана с ошибкой.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class FailedJobsTable extends Base
{
    /**
     * Код поля хранения обработчика.
     */
    public const FIELD_TASK = "TASK";

    /**
     * Код поля хранения параметров обработчика.
     */
    public const FIELD_PARAMS = "PARAMS";

    /**
     * Код поля хранения текста исключения.
     */
    public const FIELD_EXCEPTION = "EXCEPTION";

    /**
     * Получение наименования таблицы с очередями.
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_task_queue_failed_jobs';
    }

    /**
     * Получение описания таблицы по правилам ORM.
     *
     * <li>ID - Уникальный идентификатор очереди
     * <li>TASK - Обработчик очереди
     * <li>PARAMS - Параметры задачи
     * <li>DATE_CREATE - Дата создания очереди
     * <li>DATE_UPDATE - Дата последнего обновления задачи
     *
     * @return array
     */
    public static function getMap(): array
    {
        $parentMap = parent::getMap();

        $map = [
            new Entity\StringField(static::FIELD_TASK, []),
            new Entity\TextField(static::FIELD_PARAMS, []),
            new Entity\TextField(static::FIELD_EXCEPTION, []),
        ];

        return array_merge($map, $parentMap);
    }

    /**
     * Добавление нового ошибочного результата работы задачи.
     *
     * @param IFailedJob $dto
     * @return AddResult
     * @throws Exception
     */
    public static function append(IFailedJob $dto): AddResult
    {
        $fields = [
            static::FIELD_TASK => $dto->getTask(),
            static::FIELD_PARAMS => $dto->getParameters(),
            static::FIELD_EXCEPTION => $dto->getException(),
            static::FIELD_DATE_UPDATE => $dto->getDateUpdate(),
            static::FIELD_DATE_CREATE => $dto->getDateCreate(),
        ];

        return parent::add($fields);
    }

    /**
     * Добавление нового ошибочного результата работы задачи.
     *
     * @param array $data
     * @return AddResult
     * @throws Exception
     */
    public static function add(array $data = []): AddResult
    {
        if (!isset($data['task']) || !is_string($data['task'])) {
            $data['task'] = '';
        }

        if (!isset($data['exception']) || !is_string($data['exception'])) {
            $data['exception'] = '';
        }

        if (!isset($data['params']) || !is_array($data['params'])) {
            $data['params'] = [];
        }

        $fields = [
            static::FIELD_TASK => $data['task'],
            static::FIELD_PARAMS => serialize($data['params']),
            static::FIELD_EXCEPTION => $data['exception'],
            static::FIELD_DATE_UPDATE => new Date(),
            static::FIELD_DATE_CREATE => new Date(),
        ];

        return parent::add($fields);
    }

    /**
     * Получение списка задач.
     *
     * @param array $parameters
     * @return array
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

            $dto = (new FailedJob())->setId($id)
                ->setTask($item[static::FIELD_TASK] ?? '');

            if (is_array($parameters)) {
                $dto->setParameters($parameters);
            }

            $dateCreate = $item[static::FIELD_DATE_CREATE] ?? null;
            $dateUpdate = $item[static::FIELD_DATE_UPDATE] ?? null;

            if ($dateCreate instanceof Date) {
                $dto->setDateCreate($dateCreate);
            }

            if ($dateUpdate instanceof Date) {
                $dto->setDateUpdate($dateUpdate);
            }

            $result[$id] = $dto;
        }

        return $result;
    }
}