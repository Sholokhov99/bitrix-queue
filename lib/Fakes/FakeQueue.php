<?php

namespace Task\Queue\Fakes;

use Task\Queue\Fakes\ORM\FakeORM;
use Task\Queue\Interfaces\ORM\IORM;
use Task\Queue\Service\QueueManager;

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