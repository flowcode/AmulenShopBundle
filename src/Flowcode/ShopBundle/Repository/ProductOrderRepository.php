<?php

namespace Flowcode\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductOrderRepository extends EntityRepository
{

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllQB()
    {
        $qb = $this->createQueryBuilder('po');
        return $qb;
    }

    /**
     * @param int $max
     * @return array
     */
    public function recentOrders($max = 20)
    {
        $qb = $this->findAllQB();
        $qb->orderBy('po.created', 'DESC');
        $qb->setMaxResults($max);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $filter
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllFilteredQB($filter)
    {
        $qb = $this->findAllQB();
        $qb->leftJoin('po.status', 's');
        $qb->leftJoin('po.user', 'u');


        if ($filter['q']) {
            $qb->andwhere('u.username LIKE :name')->setParameter('name', '%' . $filter['q'] . '%');
        }

        if ($filter['status']) {
            $qb->andWhere('s.id = :status_id')->setParameter('status_id', $filter['status']);
        }

        return $qb;
    }

    public function findProductInOrder($product, $order)
    {
        $query = null;
        if (!is_null($product) && !is_null($order)) {
            $query = $this->createQueryBuilder("o")
                ->select(array('o', 'i'))
                ->innerJoin("o.item", "i")
                ->innerJoin("i.product", "p")
                ->andWhere("o.id = :order")->setParameter("order", $order->getId())
                ->andWhere("p.id = :product")->setParameter("product", $product->getId())
                ->getQuery()->getOneOrNullResult();
        }
        return $query;
    }

    public function findByIdSorted($id)
    {
        $query = $this->createQueryBuilder("o")
            ->select(array('o', 'i'))
            ->leftJoin("o.items", "i")
            ->leftJoin("i.product", "p")
            ->andWhere("o.id = :order")->setParameter("order", $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()->getOneOrNullResult();
        return $query;
    }

}
