<?php

declare(strict_types=1);

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Doctrine\DBAL\Driver\PDO\MySQL\Driver::class,
                'params' => [
                    'host' => 'localhost',
                    'dbname' => 'mqueue',
                    'user' => 'mqueue',
                    'password' => '',
                    'port' => 3306,
                    'driverOptions' => [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4; SET sql_mode = 'STRICT_TRANS_TABLES';",
                    ],
                    'defaultTableOptions' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_unicode_520_ci',
                    ],
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => Doctrine\ORM\Mapping\Driver\AttributeDriver::class,
                'cache' => 'array',
                'paths' => ['src/Application/Model'],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'naming_strategy' => Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class,
                'numeric_functions' => [
                    'now' => DoctrineExtensions\Query\Mysql\Now::class,
                    'rand' => DoctrineExtensions\Query\Mysql\Rand::class,
                ],
            ],
        ],
        'types' => [
            'datetime' => Application\DBAL\Types\ChronosType::class,
            'date' => Application\DBAL\Types\DateType::class,
        ],
        // migrations configuration
        'migrations' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'doctrine_migration_versions',
                ],
                'custom_template' => 'config/migration-template.txt',
                'migrations_paths' => [
                    'Application\Migration' => 'src/Application/Migration',
                ],
            ],
        ],
    ],
];
