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
    public function getByCategory(Category $category = null, $pathCat = null, $elemCount = null)
    {
        $qb = $this->findAllQB();
        $qb->innerJoin('s.product', 'p');

        if ($category) {
            $qb->where(':category MEMBER OF s.categories');
            $qb->setParameter("category", $category);

            //Incluir todos los padres de la categoria actual
            if($pathCat){
                $i = 1;
                foreach ($pathCat as $item) {
                    $qb->orWhere(':parentCat_'.$i.' MEMBER OF s.categories');
                    $qb->setParameter("parentCat_".$i, $item);
                    $i++;
                }
            }

            if($elemCount){
                $qb->andWhere('p.capacity = :elemCount')->setParameter("elemCount", $elemCount);
            }

            $qb->addOrderBy('p.capacity', 'ASC');
            $qb->addOrderBy('p.id', 'ASC');
            $qb->addOrderBy('s.factor', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }
}
