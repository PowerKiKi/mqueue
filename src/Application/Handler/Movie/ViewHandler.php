<?php

namespace Application\Handler\Movie;

use Application\Handler\PageHandler;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Application\Paginator\PaginatorFactory;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ViewHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $id = Movie::extractId($routeResult->getMatchedParams()['id'] ?? null);

        $movie = null;
        if ($id) {
            $movie = _em()->getRepository(Movie::class)->findOneById($id);
        }

        if (!$movie) {
            return $this->error(_tr('Movie not found'));
        }

        $activity = PaginatorFactory::create(
            $request,
            _em()->getRepository(Status::class)->getActivityQuery($movie),
        );

        $data = [
            'movie' => $movie,
            'users' => _em()->getRepository(User::class)->getAll(),
            'movieActivity' => $activity,
        ];

        return new HtmlResponse($this->template->render('app::movie/view', $data));
    }
}
