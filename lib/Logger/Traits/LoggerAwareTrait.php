<?php

namespace Task\Queue\Logger\Traits;

use Task\Queue\Interfaces\Logger\ILogger;
use Task\Queue\Interfaces\Logger\ILoggerAware;

/**
 * Базовая реализация {@see ILoggerAware}.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
trait LoggerAwareTrait
{
    /**
     * Флаг активности использования логера.
     *
     * @var bool
     */
    protected bool $loggerExist = false;

    /**
     * Механизм логирования.
     *
     * @var ILogger
     */
    protected ILogger $logger;

    /**
     * Указание механизма логирования.
     *
     * @param ILogger $value
     * @return $this
     */
    public function setLogger(ILogger $value): self
    {
        $this->logger = $value;
        $this->loggerExist = true;
        return $this;
    }

    /**
     * Флаг наличия логера.
     *
     * @return bool
     */
    public function useLogger(): bool
    {
        return $this->loggerExist;
    }
}