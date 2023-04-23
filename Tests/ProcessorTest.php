<?php

namespace Task\Queue;

use PHPUnit\Framework\TestCase;

use Task\Queue\Fakes;
use Task\Queue\Interfaces\Bus\IShouldQueue;
use Task\Queue\Service\QueueManager;

use Bitrix\Main\Result;

class ProcessorTest extends TestCase
{
    protected Processor $processor;

    protected QueueManager $queue;

    protected Fakes\ORM\FakeORM $orm;

    protected Fakes\DTO\FakeJob $job;

    public function setUp(): void
    {
        $this->job = new Fakes\DTO\FakeJob();
        $this->orm = new Fakes\ORM\FakeORM($this->job);
        $this->queue = new QueueManager($this->orm);
        $this->processor = new Processor($this->queue);
    }

    /**
     * Проверка поведения механизма разбора очередей при отсутствии задач.
     *
     * @return void
     */
    public function testExecuteEmptyJobs(): void
    {
        $this->orm::$resources->clear();
        $result = $this->processor->execute();

        $this->assertCount(1, $result);
        $this->checkIsLastQueueEmpty($result);
    }

    /**
     * Проверка механизма выполнения задач при лимите 10
     * Все задачи имеют описание {@see IShouldQueue}
     *
     * @return void
     */
    public function testExecuteTenShouldQueue(): void
    {
        $this->executeCountShouldQueue(10);
    }

    /**
     * Проверка механизма выполнения при лимите 0
     * Все задачи имеют описание {@see IShouldQueue}
     *
     * @return void
     */
    public function testExecuteZeroShouldQueue(): void
    {
        $this->executeCountShouldQueue(0);
    }

    /**
     * Проверка механизма импорта задач не имеющих абстрактное описание {@see IShouldQueue}
     *
     * @return void
     */
    public function testExecuteNotShouldQueueOne(): void
    {
        $parameters = [55, 'UserName'];
        $this->orm::$resources->clear();
        $this->job->setTask('Task\\Queue\\Fakes\\Tasks\\FakeCustomTask::staticHandler');
        $this->job->setParameters($parameters);

        $this->orm::append($this->job);

        $result = $this->processor->execute();

        $this->assertCount(2, $result);
        $this->assertEquals(reset($result), 'It is static handler');

        $this->checkIsLastQueueEmpty($result);
    }

    /**
     * Указание лимита выполнения задач натуральным числом.
     *
     * @return void
     */
    public function testSetLimitNatural(): void
    {
        $this->processor->setLimit(256);
        $this->assertEquals(256, $this->processor->getLimit());
    }

    /**
     * Указание отрицательного лимита.
     *
     * @return void
     */
    public function testSetLimitNegative(): void
    {
        $this->processor->setLimit(-156);
        $this->assertEquals(0, $this->processor->getLimit());
    }

    protected function executeCountShouldQueue(int $count)
    {
        while ($this->orm::$resources->count() < $count + 5) {
            $this->orm::append($this->job);
        }

        $this->processor->setLimit($count);
        $results = $this->processor->execute();
        $this->assertCount($count, $results);

        foreach ($results as $result) {
            /**
             * @var Result $result
             */
            $this->assertInstanceOf($result::class, new Result);

            $this->assertTrue($result->isSuccess());
            $this->assertEmpty($result->getData());
        }
    }

    /**
     * Проверка наличия сообщения о конце очереди.
     * Поиск происходит по последнему результату
     * т.е. проверка не учитывает, что в пуле результатов будет встречаться несколько подобных сообщений.
     * Для этого проверку производим выше.
     *
     * @param array $results
     * @return self
     */
    protected function checkIsLastQueueEmpty(array $results): self
    {
        /**
         * @var Result $lastResult
         */
        $lastResult = end($results);

        $this->assertInstanceOf($lastResult::class, new Result());

        $resultData = $lastResult->getData();
        $this->assertCount(1, $resultData);
        $this->assertEquals(reset($resultData), 'Queue is empty');

        return $this;
    }

    /**
     * Указываем стандартный JOB ответа менеджера очередей.
     *
     * @param string $task
     * @param array $parameters
     * @return $this
     */
    protected function setDefaultTaskJob(string $task = '', array $parameters = []): self
    {
        $task = trim($task);

        if ($task === '') {
            $task = 'Task\\Queue\\Fakes\\Tasks\\FakeCustomTask::staticHandler';
        }

        $this->job->setTask($task);
        $this->job->setParameters($parameters);

        return $this;
    }
}