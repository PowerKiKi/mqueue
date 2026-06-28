<?php

namespace Application\Paginator;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Adapter for ORM queries to be used with Zend_Paginator.
 */
class QueryAdapter extends Paginator implements AdapterInterface
{
    public function getItems($offset, $itemCountPerPage): array
    {
        $this->getQuery()->setFirstResult($offset);
        $this->getQuery()->setMaxResults($itemCountPerPage);

        return $this->getQuery()->getResult();
    }
}
