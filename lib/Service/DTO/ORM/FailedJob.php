<?php

namespace Task\Queue\Service\DTO\ORM;

use Task\Queue\Interfaces\ORM\IFailedJob;

/**
 * Описание структуры данных задачи с ошибкой.
 *
 * @author Daniil Sholohkov <sholokhov.daniil@gmail.com>
 */
class FailedJob extends Job implements IFailedJob
{
    /**
     * Статус задачи.
     *
     * @var string
     */
    protected string $exception;

    /**
     * Получение текста ошибки.
     *
     * @return string
     */
    public function getException(): string
    {
        return $this->exception ?? '';
    }

    /**
     * Указание содержимого ошибки.
     *
     * @param string $value
     * @return $this
     */
    public function setException(string $value): self
    {
        $this->exception = $value;
        return $this;
    }
}