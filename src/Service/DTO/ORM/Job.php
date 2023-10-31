<?php

namespace Sholokhov\Broker\Service\DTO\ORM;

use Bitrix\Main\Type\Date;
use Sholokhov\Broker\Interfaces\ORM\IJob;

/**
 * Описание структуры данных задачи.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Job implements IJob
{
    /**
     * Идентификатор задачи.
     *
     * @var int
     */
    protected int $id;

    /**
     * Обработчик задачи.
     *
     * @var string
     */
    protected string $task;

    /**
     * Параметры задачи.
     *
     * @var array
     */
    protected array $parameters;

    /**
     * Дата создания задачи.
     *
     * @var Date
     */
    protected Date $dateCreate;

    /**
     * Дата обновления задачи.
     *
     * @var Date
     */
    protected Date $dateUpdate;

    /**
     * Получение идентификатора задачи.
     *
     * @return int
     */
    public function getID(): int
    {
        return $this->id ?? 0;
    }

    /**
     * Получения обработчика задачи.
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task ?? '';
    }

    /**
     * Получение параметров задачи.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters ?? [];
    }

    /**
     * Получение даты создания задачи.
     *
     * @return Date
     */
    public function getDateCreate(): Date
    {
        if (!isset($this->dateCreate)) {
            if (isset($this->dateUpdate)) {
                $this->dateCreate = clone $this->dateUpdate;
            } else {
                $this->dateCreate = new Date();
            }
        }

        return $this->dateCreate;
    }

    /**
     * Получение даты обновления задачи.
     *
     * @return Date
     */
    public function getDateUpdate(): Date
    {
        if (!isset($this->dateUpdate)) {
            $this->dateUpdate = new Date();
        }

        return $this->dateUpdate;
    }

    /**
     * Указание идентификатора задачи.
     *
     * @param int $value
     * @return $this
     */
    public function setId(int $value): self
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
     * @param array $value
     * @return $this
     */
    public function setParameters(array $value): self
    {
        $this->parameters = $value;
        return $this;
    }

    /**
     * Указание даты создания задачи.
     *
     * @param Date $value
     * @return $this
     */
    public function setDateCreate(Date $value): self
    {
        $this->dateCreate = $value;
        return $this;
    }

    /**
     * Указание даты обнвления задачи.
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