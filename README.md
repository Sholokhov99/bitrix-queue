Модуль используется, для организации выполнения поставленных задач в указанной очередности.
Для выполнения задач рекомендуется использовать cron задачу.

Простой пример реализации обработчика задач:
```php
namespace Task\Queue\Fakes;

use Bitrix\Main\Result;

use Task\Queue\Service\Traits\Dispatchable;
use Task\Queue\Interfaces\Bus\IShouldQueue;

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

Создание новой задачи на основе объекта, который реализует абстракцию Task\Queue\Interfaces\Bus\IShouldQueue 
и использует расширение Task\Queue\Service\Traits\Dispatchable.
```php
use Task\Queue\Fakes\FakeTask;
FakeTask::dispatch(55, 'TestName');
```

Добавление задач через обертку:
```php
use Task\Queue\ORM\JobsTable;
use Task\Queue\Service\QueueManager;
use Task\Queue\Service\DTO\ORM\Job;
use Task\Queue\Fakes\Tasks\FakeCustomTask;

$job = (new Job())->setTask(FakeCustomTask::class)
    ->setParameters([55, 'TestName']);

$manager = new QueueManager(new JobsTable());
$manager->push($job);
```
Добавление через ORM:
```php
use Task\Queue\ORM\JobsTable;
use Task\Queue\Service\DTO\ORM\Job;
use Task\Queue\Fakes\Tasks\FakeCustomTask;

$job = (new Job())->setTask(FakeCustomTask::class)
    ->setParameters([55, 'TestName']);

JobsTable::append($job);
```

Механизм запуска обработчика задачи может принимать следующие способы вызова:

В Task передается имя класса, который должен реализовывать абстрацию Task\Queue\Interfaces\Bus\IShouldQueue
```php
Task\Queue\Fakes\FakeTask
```

В Task можно и передать имя класса, который не реализовывает абстракцию Task\Queue\Interfaces\Bus\IShouldQueue.
В данном случае будет попытка объявить класс и вызвать функцию <b>handle</b>. Если все попытки не дадут результата, то задача отправится в список ошибочных задач.
```php
Task\Queue\Fakes\Tasks\FakeCustomTask
```

В Task можно передавать вызов статических функций, которые в последствии будут вызваны.
```php
Task\Queue\Fakes\Tasks\FakeCustomTask::staticHandler
```
