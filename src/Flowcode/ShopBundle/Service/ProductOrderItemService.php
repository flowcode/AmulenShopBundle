<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\ProductOrderItem;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * ProductOrderItem Service
 */
class ProductOrderItemService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em, EntityRepository $productOrderItemRepository)
    {
        $this->em = $em;
        $this->productOrderItemRepository = $productOrderItemRepository;
    }

    /**
     * Find al ProductOrderItems with pagination options.
     * @param  integer $page [description]
     * @param  integer $max  [description]
     * @return ArrayCollection        ProductOrderItems.
     */
    public function findAll($page = 1, $max = 50)
    {
        $offset = (($page-1) * $max);
        $productOrderItems = $this->productOrderItemRepository->findBy(array(), array(), $max, $offset);
        return $productOrderItems;
    }

    /**
     * Find by id.
     * @param  integer $id
     * @return ProductOrderItem        productOrderItem.
     */
    public function findById($id)
    {
        return $this->productOrderItemRepository->find($id);
    }

    /**
     * Create a new ProductOrderItem.
     * @param  ProductOrderItem   $productOrderItem the productOrderItem instance.
     * @return ProductOrderItem       the productOrderItem instance.
     */
    public function create(ProductOrderItem $productOrderItem)
    {
        $this->getEm()->persist($productOrderItem);
        $this->getEm()->flush();

        return $productOrderItem;
    }

    public function update(ProductOrderItem $productOrderItem)
    {
        $this->getEm()->flush();
        return $productOrderItem;
    }

    /**
     * Delete an ProductOrderItem.
     * @param  ProductOrderItem $productOrderItem the productOrderItem instance.
     */
    public function delete(ProductOrderItem $productOrderItem)
    {
        $this->getEm()->remove($productOrderItem);
        $this->getEm()->flush();
    }

    public function supportsClass($class)
    {
        return $class === 'Amulen\ShopBundle\Entity\ProductOrderItem';
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
