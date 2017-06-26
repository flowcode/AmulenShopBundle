<?php

namespace Flowcode\ShopBundle\Repository;

use Amulen\ClassificationBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

/**
 * StrategyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StrategyRepository extends EntityRepository
{
    public function findAllQB()
    {
        $qb = $this->createQueryBuilder('s');
        return $qb;
    }

    /**
     * @param $filter
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllFilteredQB($filter)
    {
        $qb = $this->findAllQB();

        if ($filter['q']) {
            $qb->andwhere('s.name LIKE :name')->setParameter('name', '%' . $filter['q'] . '%');
        }

        return $qb;
    }

    /**
     * @param Category $category
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByCategory(Category $category = null)
    {
        $qb = $this->findAllQB();
        $qb->join('s.product', 'p');

        if ($category) {
            $qb->where(':category MEMBER OF s.categories');
            $qb->setParameter("category", $category);
            $qb->orderBy('s.price', 'ASC');
            $qb->orderBy('p.capacity', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }
}