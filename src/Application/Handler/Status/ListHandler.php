<?php

namespace Application\Handler\Status;

use Application\Handler\PageHandler;
use Application\Model\Movie;
use Application\Model\Status;
use Application\Model\User;
use Application\Response\JsonCallbackResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListHandler extends PageHandler
{
    // /status/list/movies/8962124,5519506,1441251,40036895,33560536,8599532,29195603,27788030,15140278,22397870,23629884,20865278,13406094,20383014,15740736,10072662,10287954,1745066,32141377,34819091,7137380,0118615,1127180,0077013,2388771,2467700,7097896,8611798,?format=json&jsoncallback=jQuery37109388152897430984_1777254361754&_=1777254361755
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        $idMovies = [];
        foreach (explode(',', $routeResult->getMatchedParams()['movies'] ?? '') as $idMovie) {
            $idMovie = Movie::extractId($idMovie);
            if ($idMovie) {
                $idMovies[] = $idMovie;
            }
        }

        $statuses = _em()->getRepository(Status::class)->getAllByMoviesAndUser($idMovies, User::getCurrent());

        $json = [];
        foreach ($statuses as $s) {
            $html = ($this->statusLinks)($s);
            $json[$s->getUniqueId()] = $html;
        }

        $jsonCallback = $request->getQueryParams()['jsoncallback'] ?? 'jsonp';

        return new JsonCallbackResponse(
            $jsonCallback,
            [
                'status' => $json,
            ],
        );
    }
}
