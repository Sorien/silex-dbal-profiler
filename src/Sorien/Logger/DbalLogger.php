<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sorien\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Doctrine\DBAL\Logging\SQLLogger;

/**
 * DbalLogger.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DbalLogger implements SQLLogger
{
    const MAX_STRING_LENGTH = 32;
    const BINARY_DATA_VALUE = '(binary value)';

    protected $logger;
    protected $stopwatch;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger    A LoggerInterface instance
     * @param Stopwatch       $stopwatch A Stopwatch instance
     */
    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->start('doctrine', 'doctrine');
        }

        if (is_array($params)) {
            array_walk($params, function(&$param) {
                if (!is_string($param)) {
                    return;
                }

                // non utf-8 strings break json encoding
                if (!preg_match('#[\p{L}\p{N} ]#u', $param)) {
                    $param = self::BINARY_DATA_VALUE;
                    return;
                }

                // detect if the too long string must be shorten
                if (function_exists('mb_detect_encoding') && false !== $encoding = mb_detect_encoding($param)) {
                    if (self::MAX_STRING_LENGTH < mb_strlen($param, $encoding)) {
                        $param = mb_substr($param, 0, self::MAX_STRING_LENGTH - 6, $encoding).' [...]';
                        return;
                    }
                } else {
                    if (self::MAX_STRING_LENGTH < strlen($param)) {
                        $param = substr($param, 0, self::MAX_STRING_LENGTH - 6).' [...]';
                        return;
                    }
                }
            });
        }

        if (null !== $this->logger) {
            $this->log($sql, null === $params ? array() : $params);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->stop('doctrine');
        }
    }

    /**
     * Logs a message.
     *
     * @param string $message A message to log
     * @param array  $params  The context
     */
    protected function log($message, array $params)
    {
        $this->logger->debug($message, $params);
    }
}
