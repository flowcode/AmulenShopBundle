<?php

namespace Flowcode\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{

    public function findEnabledByPageAndCategory($category_slug = null)
    {
        $query = null;
        if (!is_null($category_slug)) {
            $query = $this->createQueryBuilder("p")->innerJoin("p.category", "c", Join::WITH, "c.slug = :category_slug");
            $query->setParameter("category_slug", $category_slug);
        } else {
            $query = $this->createQueryBuilder("p");
        }
        $query->andWhere("p.enabled = 1");
        $query->addOrderBy("p.updated", "DESC");
        return $query;
    }
}
