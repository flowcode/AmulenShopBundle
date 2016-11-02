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
    public function findProductInOrder($product, $order) {
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
}
