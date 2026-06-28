<?php

namespace ApplicationTest\Middleware;

use Application\Middleware\DetectBrowserLocaleMiddleware;
use Laminas\Diactoros\ServerRequest;
use Laminas\I18n\Translator\Translator;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class DetectBrowserLocaleMiddlewareTest extends TestCase
{
    #[DataProvider('providerProcess')]
    public function testProcess(?string $preferred, ?string $sessionValue, string $expected): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with($expected);

        $session = $this->createMock(SessionInterface::class);
        $session->expects(self::once())
            ->method('get')
            ->willReturn($sessionValue);

        $request = new ServerRequest();
        $request = $request
            ->withQueryParams(['lang' => $preferred])
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $middleware = new DetectBrowserLocaleMiddleware($translator);
        $middleware->process($request, self::createStub(RequestHandlerInterface::class));
    }

    public static function providerProcess(): iterable
    {
        yield ['fr', null, 'fr'];
        yield ['en', null, 'en'];
        yield ['foo', null, 'en'];
        yield [null, null, 'en'];
        yield [null, 'ko', 'ko'];
        yield ['fr', 'ko', 'fr'];
        yield 'https://bugs.php.net/bug.php?id=81383 should be workaround-ed' => [str_repeat('a', 157), null, 'en'];
    }
}
