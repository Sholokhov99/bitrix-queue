<?php

namespace Task\Queue\Service\DTO\ORM;

use Bitrix\Main\Type\Date;
use Task\Queue\Interfaces\ORM\IJob;

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
     * Статус задачи.
     *
     * @var string
     */
    protected string $status;

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
     * Получение статуса задачи.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status ?? '';
    }

    /**
     * Получение даты создания задачи.
     *
     * @return Date|null
     */
    public function getDateCreate(): ?Date
    {
        return $this->dateCreate ?? null;
    }

    /**
     * Получение даты обновления задачи.
     *
     * @return Date|null
     */
    public function getDateUpdate(): ?Date
    {
        return $this->dateUpdate ?? null;
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
     * Указание статуса задачи.
     *
     * @param string $value
     * @return $this
     */
    public function setStatus(string $value): self
    {
        $this->status = $value;
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