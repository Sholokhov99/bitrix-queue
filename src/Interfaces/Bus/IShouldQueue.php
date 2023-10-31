<?php

namespace Sholokhov\Broker\Interfaces\Bus;

use Bitrix\Main\Result;

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
     * @return Result
     */
    public function handle(): Result;
}