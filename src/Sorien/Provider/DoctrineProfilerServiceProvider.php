<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Stanislav Turza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sorien\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\LoggerChain;
use Sorien\Doctrine\DataCollector\DoctrineDataCollector;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Sorien\DbalLogger;

/**
 * Symfony Web Profiler provider.
 *
 * @author Stanislav Turza
 */
class DoctrineProfilerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $dataCollectors = $app['data_collectors'];
        $dataCollectors['db'] = $app->share(function ($app) {
            /** @var Connection $db */
            $db = $app['db'];
            $collector = new DoctrineDataCollector($db);

            $loggerChain = new LoggerChain();
            $logger = new DebugStack();

            $loggerChain->addLogger($logger);
            $loggerChain->addLogger(new DbalLogger($app['logger'], $app['stopwatch']));

            $db->getConfiguration()->setSQLLogger($loggerChain);

            $collector->addLogger($logger);
            return $collector;
        });
        $app['data_collectors'] = $dataCollectors;

        $dataCollectorTemplates = $app['data_collector.templates'];
        $dataCollectorTemplates[] = array('db', '@DoctrineBundle/Collector/db.html.twig');
        $app['data_collector.templates'] = $dataCollectorTemplates;

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
            $loader->addPath(dirname(__DIR__).'/Resources/views', 'DoctrineBundle');
            return $loader;
        }));
    }

    public function boot(Application $app)
    {
    }
}
