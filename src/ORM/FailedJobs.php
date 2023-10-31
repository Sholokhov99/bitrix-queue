<?php

namespace Sholokhov\Broker\ORM;

use Exception;

use Sholokhov\Broker\Interfaces\ORM\IFailedJob;
use Sholokhov\Broker\Interfaces\ORM\IJob;

use Bitrix\Main\{Entity,
    ArgumentException,
    ObjectPropertyException,
    SystemException
};
use Bitrix\Main\Type\Date;
use Bitrix\Main\ORM\Data\AddResult;

/**
 * ORM, для взаимодействия с очередью, которая была обработана с ошибкой.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 *
 * @method static IFailedJob getFirst(array $filter = [])
 */
class FailedJobsTable extends Base
{
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
     * <li>EXCEPTION - Ошибка с которой завершилось выполнение задачи
     * <li>DATE_CREATE - Дата создания очереди
     * <li>DATE_UPDATE - Дата последнего обновления задачи
     *
     * @return array
     */
    public static function getMap(): array
    {
        $parentMap = parent::getMap();

        $map = [
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
    public static function append(IJob $dto): AddResult
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
    public static function getAll(array $parameters = []): array
    {
        $result = [];
        $iterator = static::getList($parameters);

        while ($item = $iterator->fetch()) {
            $id = intval($item['ID'] ?? 0);
            $result[$id] = static::arrayToJob($item);
        }

        return $result;
    }
}