<?php


namespace Task\Queue\Fakes\ORM;

use Task\Queue\Fakes\DTO\FakeJob;
use Task\Queue\Interfaces\ORM\IJob;
use Task\Queue\Interfaces\ORM\IORM;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Task\Queue\Service\Collections\Resources;

/**
 * Имитация работы с ORM.
 *
 * @author Daniil Sholohkov <sholohkov.daniil@gmail.com>
 */
class FakeORM implements IORM
{
    public static Resources $resources;

    public function __construct(IJob $job = null)
    {
        if (is_null($job)) {
            $job = new FakeJob();
        }

        static::$resources = new Resources([$job]);
    }

    /**
     * Имитация добавленгия задачи.
     *
     * @param IJob $dto
     * @return AddResult
     */
    public static function append(IJob $dto): AddResult
    {
        static::$resources->push($dto);
        return new AddResult();
    }

    /**
     * Имитация получения списка доступных задач.
     *
     * @param array $parameters
     * @return array
     */
    public static function getAll(array $parameters = []): array
    {
        return static::$resources->all();
    }

    /**
     * Имитация получения задачи.
     *
     * @param array $filter
     * @return IJob|null
     */
    public static function getFirst(array $filter = []): ?IJob
    {
        try {
            $resource = static::$resources->first();
            return $resource instanceof IJob ? $resource : null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Имитация получения количества доступных задач.
     *
     * @param $filter
     * @param array $cache
     * @return int
     */
    public static function getCount($filter = [], array $cache = []): int
    {
        return static::$resources->count();
    }

    /**
     * Имитация удаления записи.
     *
     * @param int $primary
     * @return DeleteResult
     */
    public static function delete($primary): DeleteResult
    {
        static::$resources->pop();
        return new DeleteResult();
    }
}