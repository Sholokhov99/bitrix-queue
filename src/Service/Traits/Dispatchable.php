<?php

namespace Sholokhov\Broker\Service\Traits;

use Sholokhov\Broker\ORM\JobsTable;
use Sholokhov\Broker\Service\DTO\ORM\Job;
use Sholokhov\Broker\Service\QueueManager;

use Bitrix\Main\ORM\Data\AddResult;

/**
 * Позволяет формировать задачу в очередь на основе механизма разбора очереди (Job).
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
trait Dispatchable
{
    /**
     * Формирование задачи с указанными параметрами.
     *
     * @param ...$arguments
     * @return AddResult
     */
    public static function dispatch(...$arguments): AddResult
    {
        $job = (new Job())->setTask(static::class)
            ->setParameters($arguments);

        $manager = new QueueManager(new JobsTable());

        return $manager->push($job);
    }
}