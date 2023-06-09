<?php

namespace Task\Queue;

use PHPUnit\Framework\TestCase;

use Task\Queue\Fakes;
use Task\Queue\Interfaces\Bus\IShouldQueue;
use Task\Queue\Service\QueueManager;

use Bitrix\Main\Result;

/**
 * Тестирование механизма запуска обработчиков задач.
 *
 * @author Daniil Sholohkov <sholokhov.daniil@gmail.com>
 */
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
     *  Проверка механизма выполнения задач при указании лимита.
     *  Все задачи имеют описание {@see IShouldQueue}
     *
     * @dataProvider providerExecuteCount
     * @param int $limit
     * @param int $actualLimit
     * @return void
     */
    public function testExecuteShouldQueue(int $limit, int $actualLimit): void
    {
        $this->executeCountShouldQueue($limit, $actualLimit);
    }

    public function testExecuteShouldQueueNotImplemetsInterface(): void
    {
        $task = 'Task\\Queue\\Fakes\\Tasks\\FakeCustomTask';
        $parameters = [55, 'UserName'];
        $this->orm::$resources->clear();
        $this->setDefaultTaskJob($task, $parameters);

        $this->orm::append($this->job);
        $result = $this->processor->execute();

        $this->assertCount(2, $result);
        $resultHandler = reset($result);
        $this->assertEquals($resultHandler->getData(), ['It is handler']);

        $this->checkIsLastQueueEmpty($result);
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
        $this->setDefaultTaskJob('', $parameters);

        $this->orm::append($this->job);

        $result = $this->processor->execute();

        $this->assertCount(2, $result);
        $resultHandler = reset($result);
        $this->assertEquals($resultHandler->getData(), ['It is static handler']);

        $this->checkIsLastQueueEmpty($result);
    }

    /**
     * Указание лимита выполнения задач.
     *
     * @dataProvider providerSetLimit
     * @param int $limit
     * @return void
     */
    public function testSetLimit(int $limit): void
    {
        $this->processor->setLimit($limit);
        $limit = max(0, $limit);
        $this->assertEquals($limit, $this->processor->getLimit());
    }

    /**
     * Набор данных, для указания лимита выполняемых задач.
     *
     * @return \int[][]
     */
    public function providerSetLimit(): array
    {
        return [
            [0],
            [256],
            [985],
            [-15],
            [256],
            [15878852845]
        ];
    }

    /**
     * Набор данных, для тестирования ограничения количества выполнения задач за один шаг.
     *
     * @return \int[][]
     */
    public function providerExecuteCount(): array
    {
        return [
            [5, 5],
            [156, 156],
            [0, 0],
            [-256, 0],
            [-1, 0],
            [985, 985]
        ];
    }

    /**
     * Старт механизма вызова обработчиков задач.
     *
     * @param int $count
     * @param int $actualCount
     * @return void
     */
    protected function executeCountShouldQueue(int $count, int $actualCount)
    {
        while ($this->orm::$resources->count() < $count + 5) {
            $this->orm::append($this->job);
        }

        $this->processor->setLimit($count);
        $results = $this->processor->execute();
        $this->assertCount($actualCount, $results);

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
     * @return void
     */
    protected function setDefaultTaskJob(string $task = '', array $parameters = []): void
    {
        $task = trim($task);

        if ($task === '') {
            $task = 'Task\\Queue\\Fakes\\Tasks\\FakeCustomTask::staticHandler';
        }

        $this->job->setTask($task);
        $this->job->setParameters($parameters);
    }
}