<?php

namespace Task\Queue\Interfaces\Logger;

/**
 * Описывает экземпляр с поддержкой ведения логера.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
interface ILoggerAware
{
    /**
     * Установка логера.
     *
     * @param ILogger $value
     * @return $this
     */
    public function setLogger(ILogger $value): self;

    /**
     * Флаг готовности использования журналирования.
     *
     * @return bool
     */
    public function useLogger(): bool;
}