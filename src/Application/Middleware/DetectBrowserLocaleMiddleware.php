<?php

declare(strict_types=1);

namespace Application\Middleware;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Laminas\I18n\Translator\Translator;
use Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DetectBrowserLocaleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Translator $translator,
    ) {}

    /**
     * Detect browser locale or allow user to switch locale.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requested = $request->getQueryParams()['lang'] ?? null;

        $locale = $this->pick(
            $requested, // Language switch by user
            FigRequestCookies::get($request, 'lang')->getValue(), // Memorized choice
            Locale::acceptFromHttp($request->getHeaderLine('accept-language')), // If nothing else, read browser configuration
        );

        $this->translator->setLocale($locale);
        Locale::setDefault($locale);

        $response = $handler->handle($request);
        if ($requested === $locale) {
            $response = FigResponseCookies::set(
                $response,
                SetCookie::create('lang')
                    ->withValue($locale)
                    ->withPath('/'),
            );
        }

        return $response;
    }

    private function pick(null|false|string ...$choices): string
    {
        $supported = ['en', 'fr', 'ko'];

        foreach ($choices as $choice) {
            if (!$choice) {
                continue;
            }

            $valid = Locale::lookup($supported, $choice);
            if ($valid) {
                return $valid;
            }
        }

        return reset($supported);
    }
}
