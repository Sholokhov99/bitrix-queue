<?php

namespace Task\Queue\Interfaces\Queue;

use Task\Queue\Interfaces\ORM\IJob;

use Bitrix\Main\ORM\Data\AddResult;

/**
 * Описание механизма управления очередями.
 *
 * @author Daniil Sholohkov <sholokhov.daniil@gmail.com>
 */
interface IQueue
{
    /**
     * Получение размера очереди.
     *
     * @return int
     */
    public function size(): int;

    /**
     * Добавление новой задачи в очередь.
     *
     * @param IJob $job
     * @return AddResult
     */
    public function push(IJob $job): AddResult;

    /**
     * Добавление списка задач в очередь.
     *
     * @param array $jobs
     * @return AddResult[]
     */
    public function bulk(array $jobs): array;

    /**
     * Получение первой доступной задачи.
     *
     * @return IJob
     */
    public function first(): IJob;

    /**
     * Вытаскиваем следующее задание из очереди.
     *
     * @return IJob
     */
    public function pop(): IJob;
}