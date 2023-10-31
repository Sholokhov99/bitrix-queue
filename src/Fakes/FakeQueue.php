<?php

namespace Sholokhov\Broker\Fakes;

use Sholokhov\Broker\Fakes\ORM\FakeORM;
use Sholokhov\Broker\Interfaces\ORM\IORM;
use Sholokhov\Broker\Service\QueueManager;

class FakeQueue extends QueueManager
{
    /**
     * @var FakeORM
     */
    protected IORM $entity;

    public function __construct(FakeORM $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Получение механизма обработки
     *
     * @return FakeORM
     */
    public function getEntity(): FakeORM
    {
        return $this->entity;
    }
}