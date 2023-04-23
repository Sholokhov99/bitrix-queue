<?php

namespace Task\Queue\Service\Collections;

use RuntimeException;

use PHPUnit\Framework\TestCase;

/**
 * Тестирование итератора ресурсов.
 *
 * @author Daniil Sholohkov <sholokhov.daniil@gmail.com>
 */
class ResourcesTest extends TestCase
{
    /**
     * Итератор ресурсов.
     *
     * @var Resources
     */
    protected Resources $resources;

    public function setUp(): void
    {
        $this->resources = new Resources();
    }

    /**
     * Проверка получения всех доступных ресурсов при их отсутствии
     *
     * @return void
     */
    public function testAllEmptyResources(): void
    {
        $result = $this->resources->all();
        $this->assertEmpty($result);
    }

    /**
     * Проврека получения всех задач
     *
     * @return void
     */
    public function testAllNotEmpty()
    {
        $count = 956;

        for ($index = 0; $index < $count; $index++) {
            $this->resources->push([$index]);
        }

        $result = $this->resources->all();
        $this->assertCount($count, $result);
    }

    /**
     * Проверка на получение первого ресурса итерации.
     *
     * @return void
     */
    public function testFirst(): void
    {
        $this->addDataTest();
        $this->assertEquals($this->resources->first(), 'test1');
        $this->assertEquals($this->resources->key(), 0);
        $this->resources->next();
        $this->assertEquals($this->resources->first(), 'test1');
        $this->assertEquals($this->resources->key(), 0);
    }

    /**
     * Валидация получения ресурса при их наличии.
     *
     * @return void
     */
    public function testPop(): void
    {
        $this->addDataTest();
        $this->assertEquals($this->resources->pop(), 'test1');
        $this->assertEquals($this->resources->all(), ['test2', 'test3']);

        $this->resources->next();
        $this->assertEquals($this->resources->pop(), 'test3');

        $this->assertEquals($this->resources->all(), ['test2']);
    }

    /**
     * Вытаскиваем задачу при их отсутствии.
     *
     * @return void
     */
    public function testPopEmptyResource(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(0);
        $this->resources->pop();
    }

    /**
     * Удаление ресурса, при указании существующего ключа.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->addDataTest();
        $this->resources->delete(1);

        $resources = $this->resources->all();
        $this->assertCount(2, $resources);

        $this->assertEquals($resources[0], 'test1');
        $this->assertEquals($resources[1], 'test3');
    }

    /**
     * Удаление ресурса при указании несуществующего ключа.
     *
     * @return void
     */
    public function testDeleteNotValidKey(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1);
        $this->resources->delete(15);
    }

    /**
     * Получение первого ресурса при их отсутствии.
     *
     * @return void
     */
    public function testCurrentExceptionEmptyResource(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(0);
        $this->resources->current();
    }

    /**
     * Проверка механизма получение текущего элемента итерации.
     *
     * @return void
     */
    public function testCurrentNotEmpty(): void
    {
        $this->addDataTest();

        $this->assertEquals($this->resources->current(), "test1");
        $this->resources->next();
        $this->assertEquals($this->resources->current(), "test2");
        $this->resources->next();
        $this->assertEquals($this->resources->current(), "test3");
    }

    /**
     * Проверка получения ключа ресурса при их отсутствии.
     *
     * @return void
     */
    public function testKeyEmptyResources(): void
    {
        $emptyKey = $this->resources->key();
        $this->assertNull($emptyKey);
    }

    /**
     * Проверка ключа ресурса при его наличии.
     *
     * @return void
     */
    public function testKeyNotEmptyResources(): void
    {
        $this->addDataTest();
        $this->assertEquals($this->resources->key(), 0);
        $this->resources->next();
        $this->assertEquals($this->resources->key(), 1);
        $this->resources->rewind();
        $this->assertEquals($this->resources->key(), 0);
    }

    /**
     * Тестирование валидации ключа при отсутствии ресурсов или неверном указателе.
     *
     * @return void
     */
    public function testValidEmptyResources(): void
    {
        $this->assertFalse($this->resources->valid());
    }

    /**
     * Валидацию ключа ресурса при указании на существующий диапозон данных.
     *
     * @return void
     */
    public function testValidNotEmptyResources(): void
    {
        $this->addDataTest();
        $this->assertTrue($this->resources->valid());
    }

    /**
     * Наполнение ресурса тестовыми данными.
     *
     * @return void
     */
    protected function addDataTest(): void
    {
        $this->resources->push('test1')
            ->push('test2')
            ->push('test3');
    }
}