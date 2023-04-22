<?php

namespace Task\Queue;

use Error;
use Exception;
use RuntimeException;

use Task\Queue\Interfaces\ORM\IJob;
use Task\Queue\Interfaces\Queue\IQueue;
use Task\Queue\Interfaces\Bus\IShouldQueue;

use Bitrix\Main\Result;
use Bitrix\Main\ObjectNotFoundException;

/**
 * Обработчик очередей.
 *
 * @todo Внедрить логирование.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class Processor
{
    /**
     * Количество задач, которые будут выполняться в рамках одного шага.
     *
     * @var int
     */
    protected int $limit = 10;

    /**
     * Время выполнения одного шага.
     *
     * Если значение менее 1, то многошаговость не применяется.
     *
     * @var int
     */
    protected int $timeLimit;

    /**
     * Механизм обработки задач.
     *
     * @var IQueue
     */
    protected IQueue $queue;

    /**
     * Механизм обработки задач имеющие ошибку выполнения.
     *
     * @var IQueue
     */
    protected IQueue $queueFailed;

    /**
     * @param int $timeLimit - Время выполнения скрипта.
     */
    public function __construct(IQueue $queue, int $timeLimit = 0)
    {
        $this->timeLimit = $timeLimit;
        $this->queue = $queue;
    }

    /**
     * Запуск механизма выполнения задачи.
     *
     * @return void
     */
    public function execute(): void
    {
        $start = microtime(true);

        for ($index = $this->limit; $index > 0; $index--) {
            $errorMessage = "";

            try {
                $job = $this->queue->pop();
                $this->call($job);
            } catch (ObjectNotFoundException $exception) {
                break;
            } catch (Exception $exception) {
                $errorMessage = $exception->getMessage();
            } catch (Error $error) {
                $errorMessage = $error->getMessage();
            }

            if (!empty($errorMessage) && isset($this->queueFailed) && isset($job)) {
                $this->error($job, $errorMessage);
            }

            if ($this->timeLimit > 0 && microtime(true) - $start > $this->timeLimit) {
                break;
            }
        }
    }

    /**
     * Указание лимита при выборке задач.
     *
     * @param int $value
     * @return $this
     */
    public function setLimit(int $value): self
    {
        if ($value <= 0) {
            $value = 1;
        }

        $this->limit = $value;

        return $this;
    }

    /**
     * Вызов механизма выполнения задачи.
     *
     * @param IJob $job
     * @return mixed
     */
    protected function call(IJob $job)
    {
        $task = $job->getTask();

        if (is_a($task, IShouldQueue::class, true)) {
            return $this->callShouldQueue($job);
        } elseif (is_callable($task)) {
            return call_user_func($task, $job->getParameters());
        }

        throw new RuntimeException('Invalid task handler');
    }

    /**
     * Вызов обработчика согласно стандартной абстракции..
     *
     * @param IJob $job
     * @return Result
     */
    protected function callShouldQueue(IJob $job): Result
    {
        $task = $this->getTaskQueue($job);
        return $task->handle();
    }

    /**
     * Вызов пользовательского обработчика задачи.
     *
     * @param IJob $job
     * @return IShouldQueue
     */
    protected function getTaskQueue(IJob $job): IShouldQueue
    {
        $task = $job->getTask();

        if (class_exists($task)) {
            return new $task(...$job->getParameters());
        }

        throw new RuntimeException('Task queue not found');
    }

    /**
     * Обработка ошибки во время выполнении задачи.
     *
     * @param IJob $job
     * @param string $exception
     * @return $this
     */
    protected function error(IJob $job, string $exception): self
    {
        if (method_exists($job, 'setException')) {
            $job->setException($exception);
        }

        $this->queueFailed->push($job);

        return $this;
    }
}