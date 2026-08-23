<?php

namespace Application\Handler\Status;

use Application\Enum\Rating;
use Application\Handler\PageHandler;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $idMovie = Movie::extractId($routeResult->getMatchedParams()['movie'] ?? null);

        if (!$idMovie) {
            return $this->error('No valid movie specified.');
        }

        // If new rating is specified, and we are logged in, save it and create movie if needed
        $rating = $routeResult->getMatchedParams()['rating'] ?? null;
        if ($rating !== null && User::getCurrent()) {
            $movie = _em()->getRepository(Movie::class)->getOrCreate((int) $idMovie);

            $status = $movie->setStatus(User::getCurrent(), Rating::from((int) $rating));
            _em()->flush();
        } else {
            $status = _em()->getRepository(Status::class)->getOneByMovieAndUser($idMovie, User::getCurrent());
        }

        $format = $request->getQueryParams()['format'] ?? null;
        if ($format === 'json') {
            return new JsonResponse(
                [
                    'status' => ($this->statusLinks)($status),
                    'id' => $status->getUniqueId(),
                ],
                encodingOptions: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        }

        $data = [
            'status' => $status,
        ];

        return new HtmlResponse($this->template->render('app::status/index', $data));
    }
}
