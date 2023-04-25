<?php

namespace Task\Queue\Logger;

use Exception;
use CEventLog;
use Throwable;

use Task\Queue\Application;
use Bitrix\Main\IO\File;

/**
 * Механизм управления логированием.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Logger extends AbstractLogger
{
    /**
     * Путь ведения журнала.
     *
     * @var string
     */
    protected string $path;

    /**
     * @param string $path - Путь ведения журнала.
     */
    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    /**
     * Добавление log записи.
     *
     * @param string $level
     * @param $message
     * @param array $context
     * @return void
     */
    public function log(string $level, $message, array $context = []): void
    {
        try {
            File::putFileContents($this->path, $this->createLogLine($level, $message, $context));
        } catch (Exception $exception) {
            $this->sendBitrixEventLog($exception);
        }
    }

    /**
     * Получение пути ведения журнала.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Указание пути ведения журнала.
     *
     * @param string $value
     * @return $this
     */
    public function setPath(string $value): self
    {
        $this->path = $this->generateFileName($value);
        return $this;
    }

    /**
     * Формирование записи в рамках журнала.
     *
     * @param string $level
     * @param $message
     * @param array $context
     * @return string
     */
    public function createLogLine(string $level, $message, array $context = []): string
    {
        return $this->getSystemInfo($level) . $this->interpolate($message, $context);
    }

    /**
     * Добавление записи в лог сердствами bitrix.
     * Функция вызывается при появлении исключения во время попытки добавления запси в журнал.
     * Лог запись создается в рамках таблицы b_event_log.
     *
     * @param Throwable $throwable
     * @return void
     */
    protected function sendBitrixEventLog(Throwable $throwable): void
    {
        CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => static::class,
            'ITEM_ID' => $this->getPath(),
            'MODULE_ID' => Application::getInstance()->getModuleID(),
            'DESCRIPTION' => $throwable->getMessage() . PHP_EOL . implode(PHP_EOL, $throwable->getTrace()),
        ]);
    }

    /**
     * Генерация наименования файла.
     * В файл добавляется текущий timestamp.
     *
     * @param string $path
     * @return string
     */
    protected function generateFileName(string $path): string
    {
        $pathInfo = pathinfo($path);
        $baseName = date('Y-m-d') . "_" . $pathInfo['basename'] ?? '';
        return $pathInfo['dirname'] . "/" . $baseName;
    }

    /**
     * Формирование системной строки записи в журнале.
     *
     * @param string $level
     * @return string
     */
    protected function getSystemInfo(string $level): string
    {
        return "[" . date('Y-m-d H:i:s') . "] [{$level}] ";
    }
}