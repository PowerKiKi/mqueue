<?php

declare(strict_types=1);

namespace ApplicationTest\Handler;

use Application\Handler\HomePageHandler;
use Application\Handler\PageHandlerFactory;
use PHPUnit\Framework\TestCase;

final class PageHandlerFactoryTest extends TestCase
{
    public function testFactoryWithTemplate(): void
    {
        global $container;

        $factory = new PageHandlerFactory();
        self::assertTrue($factory->canCreate($container, HomePageHandler::class));

        $homePage = $factory($container, HomePageHandler::class);
        self::assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
