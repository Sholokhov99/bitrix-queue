<?php

namespace Task\Queue\Fakes;

use Bitrix\Main\Result;
use Task\Queue\Service\Traits\Dispatchable;
use Task\Queue\Interfaces\Bus\IShouldQueue;

/**
 * Фейковая задача.
 * Данный объект используется при тестировании функционала.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class FakeJob implements IShouldQueue
{
    use Dispatchable;

    /**
     * Аргументы задачи, которые используются, для ее решения.
     *
     * @var array
     */
    protected array $arguments;

    /**
     * @param ...$arguments - Аргументы задачи.
     */
    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Механизм запуска выполнения задачи.
     *
     * @return Result
     */
    public function handle(): Result
    {
        return new Result();
    }

    /**
     * Получение списка доступных аргументов задачи.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}