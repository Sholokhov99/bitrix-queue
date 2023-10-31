<?php

namespace Sholokhov\Broker\Service;

use Sholokhov\Broker\Interfaces\ORM\IJob;
use Sholokhov\Broker\Interfaces\ORM\IORM;
use Sholokhov\Broker\Interfaces\Queue\IQueue;

use Bitrix\Main\Error;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ObjectNotFoundException;

/**
 * Мастер работы с очередями.
 *
 * @see QueueManagerTest - Актуальные тесты объекта.
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
     * @alias
     * @link IORM::getCount()
     * @return int
     */
    public function size(): int
    {
        return $this->entity::getCount();
    }

    /**
     * Добавление новой задачи в очередь.
     *
     * @alias
     * @link IORM::append()
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

        foreach ($jobs as $key => $job) {
            if (! $job instanceof IJob) {
                $error = new Error('Job not instanceof IJob. Key: ' . $key);
                $result[] = (new AddResult())->addError($error);
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

        if (null === $job) {
            throw new ObjectNotFoundException('Queue is empty');
        }

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
        $this->entity::delete($job->getID());

        return $job;
    }
}