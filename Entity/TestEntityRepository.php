<?php

namespace Adadgio\DoctrineDQLBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TestEntityRepository extends EntityRepository
{
    public function findBy(array $where = array(), array $orderBy = array(), $limit = null, $offset = 0)
    {
		$builder = $this->createQueryBuilder('e');

		return $builder->getQuery()->getResult();
    }
}
