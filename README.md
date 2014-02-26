## Install

Composer

```json
    "require": {
        "sorien/silex-dbal-profiler": "1.1.0"
    }
```

Register

```php
    $app->register($p = new Silex\Provider\WebProfilerServiceProvider(), array(
        ...
    ));
    $app->mount('/_profiler', $p);

    $app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
```