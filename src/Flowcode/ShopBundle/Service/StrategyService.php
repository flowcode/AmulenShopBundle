<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ClassificationBundle\Entity\Category;
use Amulen\ShopBundle\Entity\Strategy;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Strategy Service
 */
class StrategyService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find all Strategys with pagination options.
     * @param  integer $page [description]
     * @param  integer $max [description]
     * @return ArrayCollection        Strategys.
     */
    public function findAll($page = 1, $max = 50)
    {
        $offset = (($page - 1) * $max);
        $strategies = $this->getEm()->getRepository("AmulenShopBundle:Strategy")->findBy(array(), array(), $max, $offset);
        return $strategies;
    }

    /**
     * Find by id.
     * @param  integer $id
     * @return Strategy        strategy.
     */
    public function findById($id)
    {
        return $this->getEm()->getRepository("AmulenShopBundle:Strategy")->find($id);
    }

    /**
     * Create a new Strategy.
     * @param  Strategy $strategy the strategy instance.
     * @return Strategy       the strategy instance.
     */
    public function create(Strategy $strategy)
    {
        $this->getEm()->persist($strategy);
        $this->getEm()->flush();

        return $strategy;
    }

    public function getByCategory(Category $category = null)
    {
        return $this->getEm()->getRepository("AmulenShopBundle:Strategy")->getByCategory($category);
    }

    public function supportsClass($class)
    {
        return $class === 'Amulen\ShopBundle\Entity\Strategy';
    }

    /**
     * Set entityManager.
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get entityManager.
     * @return EntityManager Entity manager.
     */
    public function getEm()
    {
        return $this->em;
    }
}
