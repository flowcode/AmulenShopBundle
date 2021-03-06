<?php

namespace Flowcode\ShopBundle\Repository;

/**
 * InventoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InventoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('COUNT(i)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
