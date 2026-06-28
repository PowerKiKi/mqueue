<?php

declare(strict_types=1);

namespace Application\Handler;

use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Application\Paginator\PaginatorFactory;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ActivityHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $userId = $routeResult->getMatchedParams()['user'] ?? null;
        $movieId = $routeResult->getMatchedParams()['movie'] ?? null;

        // By default, we show overall activity
        $item = null;
        $title = _tr('Overall activity');

        // Try to show user's activity
        if ($userId) {
            $item = _em()->getRepository(User::class)->findOneById($userId);
            if ($item) {
                $title = _tr('Activity for %nickname%', ['nickname' => $item->nickname]);
            }
        }

        // Try to show movie's activity
        if ($movieId) {
            $item = _em()->getRepository(Movie::class)->findOneById($movieId);
            if ($item) {
                $title = _tr('Activity for %title%', ['title' => $item->title]);
            }
        }

        $activity = PaginatorFactory::create(
            $request,
            _em()->getRepository(Status::class)->getActivityQuery($item),
        );

        $data = [
            'title' => $title,
            'activity' => $activity,
        ];

        return $this->htmlOrRss($request, 'app::activity', $data);
    }
}
