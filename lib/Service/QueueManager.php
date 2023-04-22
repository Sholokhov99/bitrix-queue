<?php

namespace Task\Queue\Service;

use Task\Queue\Interfaces\ORM\IJob;
use Task\Queue\Interfaces\ORM\IORM;
use Task\Queue\Interfaces\Queue\IQueue;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ObjectNotFoundException;

/**
 * Мастер работы с очередями.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class QueueManager implements IQueue
{
    /**
     * ORM через которую производятся запросы к таблице очередй.
     *
     * @var IORM
     */
    protected IORM $entity;

    /**
     * @param IORM $entity - ORM через которую будет настроено взаимодействие с таблицей очередей.
     */
    public function __construct(IORM $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Получение размера очереди.
     *
     * @return int
     */
    public function size(): int
    {
        return $this->entity::getCount();
    }

    /**
     * Добавление новой задачи в очередь.
     *
     * @param IJob $job
     * @return AddResult
     */
    public function push(IJob $job): AddResult
    {
        return $this->entity::append($job);
    }

    /**
     * Добавление списка задач в очередь.
     *
     * @param IJob[] $jobs
     * @return AddResult[]
     */
    public function bulk(array $jobs): array
    {
        $result = [];

        foreach ($jobs as $job) {
            if (! $job instanceof IJob) {
                continue;
            }

            $result[] = $this->push($job);
        }

        return $result;
    }

    /**
     * Получение первой доступной задачи.
     *
     * @return IJob
     * @throws ObjectNotFoundException
     */
    public function first(): IJob
    {
        $job = $this->entity::getFirst();
        $this->checkJob($job);

        return $job;
    }

    /**
     * Вытаскиваем следующее задание из очереди.
     *
     * @return IJob
     * @throws ObjectNotFoundException
     */
    public function pop(): IJob
    {
        $job = $this->first();
        $this->checkJob($job);
        $this->entity::delete($job->getID());

        return $job;
    }

    /**
     * Проверка задачи на доступность.
     *
     * @param IJob|null $job
     * @return void
     * @throws ObjectNotFoundException
     */
    protected function checkJob(?IJob $job)
    {
        if (null === $job) {
            throw new ObjectNotFoundException('Queue is empty');
        }
    }
}