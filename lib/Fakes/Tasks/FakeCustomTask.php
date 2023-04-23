<?php

namespace Task\Queue\Fakes\Tasks;

class FakeCustomTask
{
    protected int $id;

    protected string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function staticHandler(int $id, string $name)
    {
        return "It is static handler";
    }

    public function haldler()
    {
        return 'It is handler';
    }
}