<?php

namespace ApplicationTest\Middleware;

use Application\Middleware\DetectBrowserLocaleMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\I18n\Translator\Translator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DetectBrowserLocaleMiddlewareTest extends TestCase
{
    #[DataProvider('providerProcess')]
    public function testProcess(?string $preferred, ?string $cookieValue, string $expected): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with($expected);

        $request = new ServerRequest();
        $request = $request
            ->withQueryParams(['lang' => $preferred])
            ->withHeader('Cookie', $cookieValue ? 'lang=' . $cookieValue : '');

        $middleware = new DetectBrowserLocaleMiddleware($translator);
        $response = $middleware->process($request, new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        });

        self::assertSame($preferred === $expected ? "lang=$expected; Path=/" : '', $response->getHeaderLine('Set-Cookie'));
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
