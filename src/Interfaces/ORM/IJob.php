<?php

namespace Sholokhov\Broker\Interfaces\ORM;

use Bitrix\Main\Type\Date;

/**
 * Модель задачи.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
interface IJob
{
    /**
     * Получение идентификатора задачи.
     *
     * @return int
     */
    public function getID(): int;

    /**
     * Получения обработчика задачи.
     *
     * @return string
     */
    public function getTask(): string;

    /**
     * Получение параметров задачи.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Получение даты создания задачи.
     *
     * @return Date
     */
    public function getDateCreate(): Date;

    /**
     * Получение даты обновления задачи.
     *
     * @return Date
     */
    public function getDateUpdate(): Date;
}