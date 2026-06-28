<?php

namespace Application\Paginator;

use Doctrine\ORM\Query;
use Laminas\Paginator\Paginator;
use Mezzio\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class PaginatorFactory
{
    /**
     * Create a new Paginator and configure it with GET or session variables.
     */
    public static function create(ServerRequestInterface $request, Query $query): Paginator
    {
        // Read perPage from session, or GET
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionInterface::class);
        $perPage = $session->get('perPage', 25);

        $userPerPage = $request->getQueryParams()['perPage'] ?? null;
        if ($userPerPage) {
            if ($perPage !== $userPerPage) {
                $session->set('perPage', $perPage);
            }

            $perPage = $userPerPage;
        }

        // If we export to csv or rss, override perPage parameter
        $format = $request->getQueryParams()['format'] ?? null;
        switch ($format) {
            case 'csv':
                $perPage = -1;

                break;
            case 'rss':
                $perPage = 200;

                break;
        }

        // Defines the paginator
        $adapter = new QueryAdapter($query);
        $paginator = new Paginator($adapter);

        $paginator->setCurrentPageNumber($request->getQueryParams()['page'] ?? 0);
        $paginator->setItemCountPerPage($perPage);

        return $paginator;
    }
}
