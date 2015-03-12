## Install

Composer

```json
    "require": {
        "sorien/silex-dbal-profiler": "~2.0@dev"
    }
```

Register

```php
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(...));
    $app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
```