<?php

namespace Flowcode\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductOrderStatusLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductOrderStatusLogRepository extends EntityRepository
{

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllQB()
    {
        $qb = $this->createQueryBuilder('l');
        return $qb;
    }

    /**
     * @param $order
     * @return array()
     */
    public function checkStockModified($order)
    {
        $qb = $this->findAllQB();
        $qb->innerJoin('l.followingStatus', 'fs');

        $qb->where('l.order = :order')->setParameter('order', $order);
        $qb->andWhere('fs.stockModifier = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $order
     * @return array()
     */
    public function checkOrderCancelledOrDeleted($order)
    {
        $qb = $this->findAllQB();
        $qb->innerJoin('l.followingStatus', 'fs');

        $qb->where('l.order = :order')->setParameter('order', $order);
        $qb->andWhere('fs.orderDeleted = 1 OR fs.orderCanceled = 1');

        return $qb->getQuery()->getResult();
    }
}
