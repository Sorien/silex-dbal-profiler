silex-dbal-profiler
===================

Provides missing Doctrine database queries logging for [Silex Web Profiler](https://github.com/silexphp/Silex-WebProfiler) 

Installation
------------
Install the silex-dbal-profiler using [composer](http://getcomposer.org/).  This project uses [sematic versioning](http://semver.org/).

**Silex 2.0**

```bash
composer require sorien/silex-dbal-profiler "~2.0@dev"
```

**Silex 1.x**

```bash
composer require sorien/silex-dbal-profiler "~1.1"
```

Registering
-----------
```php
$app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
```

Be sure to do this after registering `WebProfilerServiceProvider`.
