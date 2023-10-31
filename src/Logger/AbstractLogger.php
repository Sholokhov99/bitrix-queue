<?php

namespace Sholokhov\Broker\Logger;

use Sholokhov\Broker\Interfaces\Logger\ILogger;
use Sholokhov\Broker\Logger\Traits\LoggerTrait;

/**
 * Это простая реализация Logger, от которой могут наследоваться другие.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
abstract class AbstractLogger implements ILogger
{
    use LoggerTrait;
}