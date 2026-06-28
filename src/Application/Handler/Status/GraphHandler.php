<?php

namespace Application\Handler\Status;

use Application\Handler\PageHandler;
use Application\Model\Status;
use Application\Model\User;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GraphHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $percent = (bool) ($request->getQueryParams()['percent'] ?? false);
        /** @var RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        $id = $routeResult->getMatchedParams()['user'] ?? null;

        $user = _em()->getRepository(User::class)->findOneById($id);
        $data = _em()->getRepository(Status::class)->getGraph($user, $percent);
        $chart = [
            'chart' => [
                'zoomType' => 'x',
            ],
            'title' => [
                'text' => '',
            ],
            'xAxis' => [
                'type' => 'datetime',
            ],
            'yAxis' => [
                'title' => [
                    'text' => 'Movies',
                ],
                'min' => 0,
            ],
            'series' => $data,
        ];

        if ($percent) {
            $chart['chart']['type'] = 'area';
            $chart['yAxis']['title']['text'] = $chart['yAxis']['title']['text'] . ' [%]';
            $chart['plotOptions'] = [
                'area' => [
                    'stacking' => 'percent',
                    'marker' => [
                        'enabled' => false,
                    ],
                ],
            ];
        }

        return new JsonResponse($chart);
    }
}
