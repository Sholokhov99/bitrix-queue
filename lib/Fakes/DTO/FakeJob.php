<?php

namespace Task\Queue\Fakes\DTO;

use Bitrix\Main\Type\Date;
use Task\Queue\Fakes\FakeTask;
use Task\Queue\Interfaces\ORM\IJob;

class FakeJob implements IJob
{
    protected int $id = 3;

    protected string $task;

    protected array $parameters = [];

    protected Date $dateCreate;

    protected Date $dateUpdate;

    public function __construct()
    {
        $this->task = FakeTask::class;
        $this->dateCreate = new Date();
        $this->dateUpdate = new Date();
    }

    /**
     * Получение идентификатора задачи.
     *
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * Получение обработчика задачи.
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * Получение параметров задачи.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Получение даты создания задачи.
     *
     * @return Date
     */
    public function getDateCreate(): Date
    {
        return $this->dateCreate;
    }

    /**
     * Получение даты обновления задачи.
     *
     * @return Date
     */
    public function getDateUpdate(): Date
    {
        return $this->dateUpdate;
    }

    /**
     * Указание идентификатора задача.
     *
     * @param int $value
     * @return $this
     */
    public function setID(int $value): self
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Указание обработчика задачи.
     *
     * @param string $value
     * @return $this
     */
    public function setTask(string $value): self
    {
        $this->task = $value;
        return $this;
    }

    /**
     * Указание параметров задачи.
     *
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Указание даты создания задачи.
     *
     * @param Date $value
     * @return $this
     */
    public function setDataCreate(Date $value): self
    {
        $this->dateCreate = $value;
        return $this;
    }

    /**
     * Указание даты обновления задачи.
     *
     * @param Date $value
     * @return $this
     */
    public function setDateUpdate(Date $value): self
    {
        $this->dateUpdate = $value;
        return $this;
    }
}