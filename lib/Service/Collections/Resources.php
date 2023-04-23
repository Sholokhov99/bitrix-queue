<?php

namespace Task\Queue\Service\Collections;

use Iterator;
use RuntimeException;

/**
 * Итератор ресурсов.
 *
 * @see ResourcesTest
 * @author Daniil Sholokhov <sholohkov.daniil@gmail.com>
 */
class Resources implements Iterator
{
    /**
     * Список доступных ресурсов.
     *
     * @var array
     */
    protected array $resources;

    public function __construct(array $resources = [])
    {
        $this->resources = $resources;
    }

    /**
     * Получение всех ресурсов.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->resources;
    }

    /**
     * Получение первого значения коллекции.
     * @throws RuntimeException
     * @return mixed
     */
    public function first()
    {
        $this->rewind();
        $this->checkValidKey();
        return $this->current();
    }

    /**
     * Получение количества ресурсов.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->resources);
    }

    /**
     * Проверка на пустую коллекцию.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return $this->count() <= 0;
    }

    /**
     * Очистка всех ресурсов.
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->resources = [];
        return $this;
    }

    /**
     * Добавление нового ресурса.
     *
     * @param $resource
     * @return $this
     */
    public function push($resource): self
    {
        $this->resources[] = $resource;
        return $this;
    }

    /**
     * Добавление нового списка ресурсов.
     *
     * @param array $resources
     * @return $this
     */
    public function bulk(array $resources): self
    {
        foreach ($resources as $value) {
            $this->push($value);
        }

        return $this;
    }

    /**
     * Вытащить следующее значение.
     *
     * @throws RuntimeException
     * @return mixed
     */
    public function pop()
    {
        $this->checkValidEmpty();
        $resource = $this->current();
        $this->delete();

        return $resource;
    }

    /**
     * Удаление ресурса по позиции.
     *
     * @throws RuntimeException
     * @param $position
     * @return array
     */
    public function delete($position = null): array
    {
        if (is_null($position)) {
            $this->checkValidKey();
            $position = $this->key();
        } elseif (!$this->validPosition($position)) {
            $this->exceptionKey();
        }

        unset($this->resources[$position]);
        $this->resources = array_values($this->resources);

        return $this->resources;
    }

    /**
     * Получение текущего ресурса.
     *
     * @return mixed
     */
    public function current()
    {
        $this->checkValidEmpty();
        return current($this->resources);
    }

    /**
     * Перевод указателя на следующий ресурс
     *
     * @return void
     */
    public function next(): void
    {
        next($this->resources);
    }

    /**
     * Получение ключа ресурса.
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->resources);
    }

    /**
     * Проверка доступности ресурса.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->resources[$this->key()]);
    }

    /**
     * Перевод на указателя на первый ресурс.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->resources);
    }

    /**
     * Проверка доступности ресурса по позиции.
     *
     * @param $position
     * @return bool
     */
    protected function validPosition($position): bool
    {
        return isset($this->resources[$position]);
    }

    /**
     * Проверка валидности ключа.
     * Если проверка не будет пройдена, то произойдет вызов исключения.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function checkValidKey(): void
    {
        if (!$this->valid()) {
            $this->exceptionKey();
        }
    }

    /**
     * Проверка коллекции на пустоту.
     * Если проверка не будет пройдена, то произойдет вызов исключения.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function checkValidEmpty(): void
    {
        if ($this->empty()) {
            throw new RuntimeException('Collection empty', 0);
        }
    }

    /**
     * Получение исключения о недоступности ключа(позиции) ресурса.
     *
     * @throws RuntimeException
     * @return void
     */
    protected function exceptionKey(): void
    {
        throw new RuntimeException('Undefined resource key', 1);
    }
}