## Install

#### Silex 2 / Pimple 3 

Composer

```json
    "require": {
        "sorien/silex-dbal-profiler": "~2.0@dev"
    }
```

#### Silex 1.* 

Composer

```json
    "require": {
        "sorien/silex-dbal-profiler": "~1.1"
    }
```

##Register

```php
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(...));
    $app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
```
