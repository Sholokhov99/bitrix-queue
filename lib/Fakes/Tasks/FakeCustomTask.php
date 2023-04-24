<?php

namespace Task\Queue\Fakes\Tasks;

use Bitrix\Main\Result;

class FakeCustomTask
{
    protected int $id;

    protected string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function staticHandler(int $id, string $name): Result
    {
        return (new Result())->setData(["It is static handler"]);
    }

    public function handle(): Result
    {
        return (new Result())->setData(['It is handler']);
    }
}