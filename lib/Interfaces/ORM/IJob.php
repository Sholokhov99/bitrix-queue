<?php

namespace Task\Queue\Interfaces\ORM;

use Bitrix\Main\Type\Date;

/**
 * Описание структуры задачи.
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
     * Получение статуса задачи.
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Получение даты создания задачи.
     *
     * @return Date|null
     */
    public function getDateCreate(): ?Date;

    /**
     * Получение даты обновления задачи.
     *
     * @return Date|null
     */
    public function getDateUpdate(): ?Date;
}