<?php

namespace Sholokhov\Broker\Interfaces\ORM;

/**
 * Описание структуры задачи.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
interface IFailedJob extends IJob
{
    /**
     * Получение статуса задачи.
     *
     * @return string
     */
    public function getException(): string;
}