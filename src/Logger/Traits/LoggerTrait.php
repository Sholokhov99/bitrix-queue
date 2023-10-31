<?php

namespace Sholokhov\Broker\Logger\Traits;

use Sholokhov\Broker\Logger\LogLevel;

/**
 * Это простая черта Logger, классы которой не могут расширять LoggerBase.
 * (потому что они расширяют другой класс и т.д.).
 *
 * Он просто делегирует все методы, относящиеся к уровню журнала методу `log`
 * для уменьшения шаблонного кода.
 *
 * Сообщения вне зависимости от уровня ошибки должны реализовываться.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
trait LoggerTrait
{
    /**
     * Не пригодно для использования.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Действия должны быть приняты немедленно.
     *
     * Пример: Весь веб-сайт недоступен, база данных недоступна и т.д.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Критическая ошибка
     *
     * Пример: компонент приложения недоступен, неожиданное исключение.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Ошибка во время выполнения, которое не требует немедленного действия.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Исключительные случаи, не являющиеся ошибками.
     *
     * Пример: использование устаревших API, неправильное использование API.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Обычные, но важные события.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Интересные события.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Подробная отладочная информация.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Логи с произвольным уровнем.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    abstract public function log(string $level, $message, array $context = []): void;

    /**
     * Интерполирует значения контекста в заполнители сообщений.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = $value;
            }
        }

        return strtr($message, $replace) . PHP_EOL;
    }

}