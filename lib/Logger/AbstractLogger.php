<?php

namespace Task\Queue\Logger;

use Task\Queue\Interfaces\Logger\ILogger;
use Task\Queue\Logger\Traits\LoggerTrait;

/**
 * Это простая реализация Logger, от которой могут наследоваться другие.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
abstract class AbstractLogger implements ILogger
{
    use LoggerTrait;
}