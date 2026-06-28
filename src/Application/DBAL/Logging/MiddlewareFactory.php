<?php

declare(strict_types=1);

namespace Application\DBAL\Logging;

use Doctrine\DBAL\Logging\Middleware;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Used to log all SQL queries to `logs/all.log`.
 *
 * Usage:
 *
 * ```
 * return [
 *     'doctrine' => [
 *         'configuration' => [
 *             'orm_default' => [
 *                 // Log all SQL queries from Doctrine (to logs/all.log)
 *                 'middlewares' => [\Doctrine\DBAL\Logging\Middleware::class],
 *             ],
 *         ],
 *     ],
 * ];
 * ```
 */
class MiddlewareFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Middleware
    {
        $logger = $container->get(LoggerInterface::class);

        return new Middleware($logger);
    }
}
