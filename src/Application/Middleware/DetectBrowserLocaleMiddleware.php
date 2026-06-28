<?php

declare(strict_types=1);

namespace Application\Middleware;

use Laminas\I18n\Translator\Translator;
use Locale;
use Mezzio\Session\SessionMiddleware;
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
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $requested = $request->getQueryParams()['lang'] ?? null;

        $locale = $this->pick(
            $requested, // Language switch by user
            $session->get('lang'), // Memorized choice
            Locale::acceptFromHttp($request->getHeaderLine('accept-language')), // If nothing else, read browser configuration
        );

        if ($requested === $locale) {
            $session->set('lang', $locale);
        }

        $this->translator->setLocale($locale);
        Locale::setDefault($locale);

        return $handler->handle($request);
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
