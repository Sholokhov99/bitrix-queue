<?php

namespace Task\Queue\Service;

use PHPUnit\Framework\TestCase;

use Task\Queue\ORM\JobsTable;
use Task\Queue\Fakes\DTO\FakeJob;
use Task\Queue\Fakes\ORM\FakeORM;
use Task\Queue\Interfaces\ORM\IJob;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ObjectNotFoundException;

/**
 * Комплексное тестирование механизма управления очередями.
 *
 * @author Daniil Sholohkov <sholokhov.daniil@gmail.com>
 */
class QueueManagerTest extends TestCase
{
    /**
     * Объект ORM, который производит запись в БД.
     *
     * @var FakeORM
     */
    protected FakeORM $orm;

    /**
     * Менеджер управления задачами.
     *
     * @var QueueManager
     */
    protected QueueManager $manager;

    public function setUp(): void
    {
        $this->orm = new FakeORM();
        $this->manager = new QueueManager($this->orm);
    }

    /**
     * Тестирование добавление задачи, которая реализует абстракцию {@see IJob}
     *
     * @return void
     */
    public function testPush(): void
    {
        $result = $this->manager->push(new FakeJob());
        $this->assertTrue($result->isSuccess());
    }

    /**
     * Тестирование добавление при отсутствии списка задач.
     *
     * @return void
     */
    public function testBulkEmpty(): void
    {
        $result = $this->manager->bulk([]);
        $this->assertEmpty($result);
    }

    /**
     * Тестирование добавления списка задач не реализующих интерфейс {@see IJob}
     *
     * @return void
     */
    public function testBulkNotJobs(): void
    {
        $jobs = [
            'notJob',
            static::class,
            new static()
        ];

        $results = $this->manager->bulk($jobs);

        $this->assertCount(count($jobs), $results);

        foreach ($results as $key => $result) {
            $this->assertInstanceOf($result::class, new AddResult());
            $this->assertFalse($result->isSuccess());
            $this->assertContains("Job not instanceof IJob. Key: {$key}",$result->getErrorMessages());
        }
    }

    /**
     * Ппрверка добавления списка задач реализующих абстракцию {@see IJob}
     *
     * @return void
     */
    public function testBulkOnlyJobs(): void
    {
        $jobs = [
            new FakeJob(),
            new FakeJob(),
        ];

        $results = $this->manager->bulk($jobs);
        $this->assertCount(count($jobs), $results);

        foreach ($results as $result) {
            $this->assertInstanceOf($result::class, new AddResult());
            $this->assertTrue($result->isSuccess());
        }
    }

    /**
     * Проверка смешенного добавления списка задач.
     * В тестирование входят:
     * <li>Задачи реализующие абстракцию {@see IJob}
     * <li>Задачи
     *
     * @return void
     */
    public function testBulkMixedJobs(): void
    {
        $jobs = [
            1254,
            new FakeJob(),
            FakeJob::class,
            new JobsTable()
        ];

        $results = $this->manager->bulk($jobs);
        $this->assertCount(count($jobs), $results);

        for ($index = 0; $index < count($jobs); $index++) {
            $this->assertInstanceOf($results[$index]::class, new AddResult());
            if ($jobs[$index] instanceof IJob) {
                $this->assertTrue($results[$index]->isSuccess());
            } else {
                $this->assertFalse($results[$index]->isSuccess());
            }
        }
    }

    /**
     * Проверка получения задачи, при ее наличии
     *
     * @throws ObjectNotFoundException
     * @return void
     */
    public function testFirst(): void
    {
        $job = $this->manager->first();
        $this->assertInstanceOf(IJob::class, $job);
    }

    /**
     * Проверка получения задачи при их отсутствии.
     *
     * @return void
     */
    public function testFirstException(): void
    {
        $this->expectException(ObjectNotFoundException::class);
        $this->orm::$resources->clear();
        $this->manager->first();
    }

    /**
     * Проверка получения задачи при их наличии.
     *
     * @throws ObjectNotFoundException
     * @return void
     */
    public function testPop(): void
    {
        $this->assertInstanceOf(IJob::class, $this->manager->pop());
    }

    /**
     * Проверка получения задач при их отсутствии.
     *
     * @return void
     */
    public function testPopExceptionEmptyCollection(): void
    {
        $this->orm::$resources->clear();
        $this->expectException(ObjectNotFoundException::class);
        $this->expectExceptionCode(510);
        $this->manager->pop();
    }
}