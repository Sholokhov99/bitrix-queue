<?php

namespace Task\Queue\Interfaces\Logger;

/**
 * Описание абстракции логгера.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
interface ILogger
{
    /**
     * Не пригодно для использования.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * Действия должны быть приняты немедленно.
     *
     * Пример: Весь веб-сайт недоступен, база данных недоступна и т.д.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void;

    /**
     * Критическая ошибка
     *
     * Пример: компонент приложения недоступен, неожиданное исключение.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void;

    /**
     * Ошибка во время выполнения, которое не требует немедленного действия.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void;

    /**
     * Исключительные случаи, не являющиеся ошибками.
     *
     * Пример: использование устаревших API, неправильное использование API.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Обычные, но важные события.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Интересные события.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void;

    /**
     * Подробная отладочная информация.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Логи с произвольным уровнем.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(string $level, $message, array $context = []): void;

}