<?php

namespace Task\Queue\Interfaces\ORM;

use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Query\Filter\ConditionTree as Filter;

interface IORM
{
    /**
     * Добавление новой задачи.
     *
     * @param IJob $dto
     * @return AddResult
     */
    public static function append(IJob $dto): AddResult;

    /**
     * Получение списка записей согласно входным параметрам.
     *
     * @param array $parameters
     * @return array
     */
    public static function getAll(array $parameters = []): array;

    /**
     * Получение первой задачи.
     *
     * @param array $filter
     * @return IJob|null
     */
    public static function getFirst(array $filter = []): ?IJob;

    /**
     * Получение количество записей согласно фильтру.
     *
     * @param array|Filter $filter
     * @param array $cache
     * @return int
     */
    public static function getCount($filter = [], array $cache = []): int;

    /**
     * Удаление записи по ID.
     *
     * @param int $primary
     * @return DeleteResult
     */
    public static function delete($primary);
}