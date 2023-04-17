<?php

namespace Task\Queue;

use Error;
use Exception;
use InvalidArgumentException;

use Task\Queue\ORM\JobsTable;
use Task\Queue\ORM\FailedJobsTable;
use Task\Queue\Service\DTO\ORM\FailedJob;
use Task\Queue\Interfaces\ORM\IJob;

use Bitrix\Main\{ArgumentException,
    ObjectPropertyException,
    SystemException
};

/**
 * Обработчик очередей.
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
     * @var int
     */
    protected int $timeLimit = 50;

    /**
     * Запуск механизма выполнения задачи.
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function execute()
    {
        $filter = [JobsTable::FIELD_STATUS => JobsTable::STATUS_NEW];
        $order = ['ID' => 'ASC'];
        $limit = $this->limit;

        $parameters = compact('filter', 'limit', 'order');

        $jobs = JobsTable::getList($parameters);

        foreach ($jobs as $job) {
            $errorMessage = "";

            try {
                $this->checkTask($job);
                call_user_func_array($job->getTask(), [$job->getParameters()]);
                JobsTable::delete($job->getID());
            } catch (Exception $exception) {
                $errorMessage = $exception->getMessage();
            } catch (Error $error) {
                $errorMessage = $error->getMessage();
            } finally {
                if (!empty($errorMessage)) {
                    $failedJob = (new FailedJob())->setTask($job->getTask())
                        ->setParameters($job->getParameters())
                        ->setException($errorMessage);
                    FailedJobsTable::append($failedJob);
                }
            }

            JobsTable::delete($job->getID());
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
     * Валидация обработчика задачи.
     *
     * @param IJob $job
     * @return void
     */
    protected function checkTask(IJob $job): void
    {
        if (!is_callable($job->getTask())) {
            throw new InvalidArgumentException('The task handler cannot be called');
        }
    }
}