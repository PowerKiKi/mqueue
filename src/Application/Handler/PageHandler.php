<?php

declare(strict_types=1);

namespace Application\Handler;

use Application\View\Helper\StatusLinks;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Laminas\View\Helper\PaginationControl;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\LaminasView\ServerUrlHelper;
use Mezzio\LaminasView\UrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class PageHandler implements RequestHandlerInterface
{
    public function __construct(
        protected readonly RouterInterface $router,
        protected readonly TemplateRendererInterface $template,
        protected readonly ServerUrlHelper $serverUrl,
        protected readonly UrlHelper $url,
        protected readonly StatusLinks $statusLinks,
    )
    {
        PaginationControl::setDefaultViewPartial('pagination');
    }

    protected function htmlOrRss(ServerRequestInterface $request, string $name, array $data): ResponseInterface
    {
        if (($request->getQueryParams()['format'] ?? null) === 'rss') {
            return new XmlResponse(
                $this->template->render(
                    $name . '-rss',
                    [
                        ...$data,
                        'layout' => false,
                    ],
                ),
                headers: ['content-type' => 'application/rss+xml; charset=utf-8'],
            );

        }

        return new HtmlResponse($this->template->render($name, $data));
    }

    protected function js(string $name, array $data = []): ResponseInterface
    {
        return new TextResponse(
            $this->template->render(
                $name,
                [
                    ...$data,
                    'layout' => false,
                ],
            ),
            headers: ['content-type' => 'application/javascript; charset=utf-8'],
        );
    }

    protected function error(string $message, int $status = 400): ResponseInterface
    {
        return new HtmlResponse($this->template->render('error::error', [
            'status' => $status,
            'message' => $message,

        ]));
    }

    protected function flashMessages(ServerRequestInterface $request): FlashMessagesInterface
    {
        return $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
    }
}
