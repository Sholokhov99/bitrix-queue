<?php

namespace Task\Queue;

/**
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Application
{
    /**
     * Экземпляр текущего приложения.
     *
     * @var self
     */
    private static self $instance;

    private function __construct() {}

    /**
     * Идентификатор модуля.
     *
     * @return string
     */
    public function getModuleID(): string
    {
        return 'task.queue';
    }

    /**
     * Получение объекта приложения.
     *
     * @return $this
     */
    public static function getInstance(): self
    {
        if (!isset(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }
}