<?php

namespace Flowcode\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductOrderStatusRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductOrderItemRepository extends EntityRepository
{
    public function findAllQB()
    {
        $qb = $this->createQueryBuilder('oi');
        return $qb;
    }

    public function findOrderService($productOrder)
    {
        $qb = $this->findAllQB();

        $qb->andWhere('oi.order = :order')->setParameter('order', $productOrder);
        $qb->andWhere('oi.service IS NOT NULL');

        return $qb->getQuery()->getResult();
    }

    public function findRemovableProducts($order)
    {
        $qb = $this->findAllQB();

        $qb->andWhere('oi.order = :order')->setParameter('order', $order);
        $qb->andWhere('oi.service IS NULL');
        $qb->andWhere("oi.quantity = 0");

        return $qb->getQuery()->getResult();
    }
}
