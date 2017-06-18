<?php

namespace Flowcode\ShopBundle\Repository;

/**
 * StockTransferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockTransferRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return integer
     */
    public function getTotalCount()
    {
        $queryBuilder = $this->createQueryBuilder('st');

        $queryBuilder->select('COUNT(st)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
