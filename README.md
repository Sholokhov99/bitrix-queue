Установка 

composer require sholokhov/broker

Модуль используется, для организации выполнения поставленных задач в указанной очередности.
Для выполнения задач рекомендуется использовать cron задачу.

Простой пример реализации обработчика задач:

```php
namespace Sholokhov\Broker\Fakes;

use Bitrix\Main\Result;

use Sholokhov\Broker\Service\Traits\Dispatchable;
use Sholokhov\Broker\Interfaces\Bus\IShouldQueue;

class FakeTask implements IShouldQueue
{
    use Dispatchable;

    protected array $arguments;

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    public function handle(): Result
    {
        return new Result();
    }

}
```
Каждый обработчик задачи должен возвращать объект с результатом, при неверном типе данных задача переместиться в список неудачных задач.
```php 
new \Bitrix\Main\Result
```

Примеры создания новой задачи:

Создание новой задачи на основе объекта, который реализует абстракцию Sholokhov\Broker\Interfaces\Bus\IShouldQueue 
и использует расширение Sholokhov\Broker\Service\Traits\Dispatchable.
```php
use Sholokhov\Broker\Fakes\FakeTask;
FakeTask::dispatch(55, 'TestName');
```

Добавление задач через обертку:

```php
use Sholokhov\Broker\ORM\JobsTable;
use Sholokhov\Broker\Service\QueueManager;
use Sholokhov\Broker\Service\DTO\ORM\Job;
use Sholokhov\Broker\Fakes\Tasks\FakeCustomTask;

$job = (new Job())->setTask(FakeCustomTask::class)
    ->setParameters([55, 'TestName']);

$manager = new QueueManager(new JobsTable());
$manager->push($job);
```
Добавление через ORM:

```php
use Sholokhov\Broker\ORM\JobsTable;
use Sholokhov\Broker\Service\DTO\ORM\Job;
use Sholokhov\Broker\Fakes\Tasks\FakeCustomTask;

$job = (new Job())->setTask(FakeCustomTask::class)
    ->setParameters([55, 'TestName']);

JobsTable::append($job);
```

Механизм запуска обработчика задачи может принимать следующие способы вызова:

В Task передается имя класса, который должен реализовывать абстрацию Sholokhov\Broker\Interfaces\Bus\IShouldQueue
```php
Sholokhov\Broker\Fakes\FakeTask
```

В Task можно и передать имя класса, который не реализовывает абстракцию Sholokhov\Broker\Interfaces\Bus\IShouldQueue.
В данном случае будет попытка объявить класс и вызвать функцию <b>handle</b>. Если все попытки не дадут результата, то задача отправится в список ошибочных задач.
```php
Sholokhov\Broker\Fakes\Tasks\FakeCustomTask
```

В Task можно передавать вызов статических функций, которые в последствии будут вызваны.
```php
Sholokhov\Broker\Fakes\Tasks\FakeCustomTask::staticHandler
```
