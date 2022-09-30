# Laravel Helpers

Некоторые файлы для распространения между Laravel проектами.

- [ChainableMethods](https://github.com/BuglerV/laravel-helpers#buglervlaravelhelperstraitschainablemethods)
- [GroupEagerLoading](https://github.com/BuglerV/laravel-helpers#buglervlaravelhelperseloquentgroupeagerloading)

### Buglerv\LaravelHelpers\Traits\ChainableMethods

Трейт используется для того, чтобы объединять методы класса в цепочку для последовательного вызова.

```php
class A
{
  use \Buglerv\LaravelHelpers\Traits\ChainableMethods;

  protected $count = 2;
    
  public function sub()
  {
    return --$this->count;
  }
  
  public function add()
  {
    return ++$this->count;
  }
  
  public function echo()
  {
    echo $this->count;
    return $this->count;
  }
}

$a = new A;

// Объединения с OR
var_dump($a->subOrEcho()); // int(1)
var_dump($a->subOrEcho()); // 0 bool(false)

// Объединение с AND
var_dump($a->addAndEcho()); // 1 int(1)
var_dump($a->subAndEcho()); // bool(false)

// Так же есть ключевые слова True и False
var_dump($a->addAndFalse()); // bool(false)
var_dump($a->subOrTrue()); // bool(true)
```

### Buglerv\LaravelHelpers\Eloquent\GroupEagerLoading

Используется для ручной загрузки отношений Eloquent моделей.

Допустим, есть модель `Project` у которой есть 2 отношения на модель `Person` через методы `frontendProgrammer` и `backendProgrammer`. Через `Project::load()` можно загрузить оба отношения, но каждое из них в любом случае будет подгружаться через отдельное обращение к базе данных. Так можно добиться одного запроса:
```php
$models = Project::all();

\Buglerv\LaravelHelpers\Eloquent\GroupEagerLoading::load($models,Person::class,[
    'frontendProgrammer' => 'frontend_programmer_id',
    'backendProgrammer' => 'backend_programmer_id',
]);
```

Можно подгрузить `softDeleted` модели:
```php
\Buglerv\LaravelHelpers\Eloquent\GroupEagerLoading::loadTrashed(...);
```

Можно подгрузить модели с дополнительными отношениями:
```php
\Buglerv\LaravelHelpers\Eloquent\GroupEagerLoading::loadWith(...,Project::class);
```

Или объединить оба варианта:
```php
\Buglerv\LaravelHelpers\Eloquent\GroupEagerLoading::loadTrashedWith(...,Project::class);
```
