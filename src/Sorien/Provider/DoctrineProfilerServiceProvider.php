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
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Sorien\Logger\DbalLogger;
use Sorien\DataCollector\DoctrineDataCollector;

/**
 * Silex Doctrine DBAL Profiler provider.
 *
 * @author Stanislav Turza
 */
class DoctrineProfilerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {

        $app->extend('data_collectors', function ($collectors, $app) {

            $collectors['db'] = function ($app) {

                $collector = new DoctrineDataCollector($app['dbs']);
                $timeLogger = new DbalLogger($app['logger'], $app['stopwatch']);

                foreach ($app['dbs.options'] as $name => $params)
                {
                    /** @var Connection $db */
                    $db = $app['dbs'][$name];

                    $loggerChain = new LoggerChain();
                    $logger = new DebugStack();

                    $loggerChain->addLogger($logger);
                    $loggerChain->addLogger($timeLogger);

                    $db->getConfiguration()->setSQLLogger($loggerChain);

                    $collector->addLogger($name, $logger);

                }

                return $collector;
            };

            return $collectors;
        });

        $app['data_collector.templates'] = $app->extend('data_collector.templates', function ($templates) {
            $templates[] = array('db', '@DoctrineBundle/Collector/db.html.twig');
            return $templates;
        });

        $app['twig.loader.filesystem'] = $app->extend('twig.loader.filesystem', function ($loader) {
            /** @var \Twig_Loader_Filesystem $loader */
            $loader->addPath(dirname(__DIR__).'/Resources/views', 'DoctrineBundle');
            return $loader;
        });
    }
}
