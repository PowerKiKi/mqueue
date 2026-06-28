<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @template T of \Application\Model\AbstractModel
 *
 * @extends EntityRepository<T>
 *
 * @method null|T findOneById(integer $id)
 */
abstract class AbstractRepository extends EntityRepository {}
