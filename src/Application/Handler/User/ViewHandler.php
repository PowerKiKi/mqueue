<?php

namespace Application\Handler\User;

use Application\Handler\PageHandler;
use Application\Model\Status;
use Application\Model\User;
use Application\Paginator\PaginatorFactory;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ViewHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $id = $routeResult->getMatchedParams()['id'] ?? null;

        $user = null;
        if ($id) {
            $user = _em()->getRepository(User::class)->findOneById($id);
        }

        if (!$user) {
            return $this->error(_tr('User not found'));
        }

        $activity = PaginatorFactory::create(
            $request,
            _em()->getRepository(Status::class)->getActivityQuery($user),
        );
        $data = [
            'user' => $user,
            'userActivity' => $activity,
        ];

        return $this->htmlOrRss($request, 'app::user/view', $data);
    }
}
