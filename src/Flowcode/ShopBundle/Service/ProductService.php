<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Product Service
 */
class ProductService
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
     * Find al Products with pagination options.
     * @param  integer $page [description]
     * @param  integer $max  [description]
     * @return ArrayCollection        Products.
     */
    public function findAll($page = 1, $max = 50)
    {
        $offset = (($page-1) * $max);
        $products = $this->getEm()->getRepository("AmulenShopBundle:Product")->findBy(array(), array(), $max, $offset);
        return $products;
    }

    /**
     * Find by id.
     * @param  integer $id
     * @return Product        product.
     */
    public function findById($id)
    {
        return $this->getEm()->getRepository("AmulenShopBundle:Product")->find($id);
    }

    /**
     * Create a new Product.
     * @param  Product   $product the product instance.
     * @return Product       the product instance.
     */
    public function create(Product $product)
    {
        $this->getEm()->persist($product);
        $this->getEm()->flush();

        return $product;
    }

    public function supportsClass($class)
    {
        return $class === 'Amulen\ShopBundle\Entity\Product';
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
