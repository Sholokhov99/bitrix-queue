<?php

namespace Task\Queue\Interfaces\Bus;

/**
 * Описание структуры обработчика задачи.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
interface IShouldQueue
{
    /**
     * Запуск механизма обработки задачи.
     *
     * @return void
     */
    public function handle(): void;
}