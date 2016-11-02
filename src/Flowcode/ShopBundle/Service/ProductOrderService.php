<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\ProductOrder;
use Amulen\ShopBundle\Entity\ProductOrderItem;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * ProductOrder Service
 */
class ProductOrderService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em, EntityRepository $productOrderRepository)
    {
        $this->em = $em;
        $this->productOrderRepository = $productOrderRepository;
    }

    /**
     * Find al ProductOrders with pagination options.
     * @param  integer $page [description]
     * @param  integer $max [description]
     * @return ArrayCollection        ProductOrders.
     */
    public function findAll($page = 1, $max = 50)
    {
        $offset = (($page - 1) * $max);
        $productOrders = $this->productOrderRepository->findBy(array(), array(), $max, $offset);
        return $productOrders;
    }

    /**
     * Find by id.
     * @param  integer $id
     * @return ProductOrder        productOrder.
     */
    public function findById($id)
    {
        return $this->productOrderRepository->find($id);
    }

    /**
     * Create a new productOrder.
     * @param  ProductOrder $productOrder the productOrder instance.
     * @return ProductOrder       the productOrder instance.
     */
    public function create(ProductOrder $productOrder)
    {
        $this->getEm()->persist($productOrder);
        $this->getEm()->flush();

        return $productOrder;
    }

    public function update(ProductOrder $productOrder)
    {
        $this->getEm()->flush();
        return $productOrder;
    }

    public function getProductOrder($productOrderId)
    {
        if ($productOrderId) {
            $productOrder = $this->findById($productOrderId);
        } else {
            $productOrder = new ProductOrder();
            $this->create($productOrder);
        }

        return $productOrder;
    }

    public function addProduct($product, $productOrder, $quantity = 1)
    {
        $item = $this->getProductItem($product, $productOrder);
        if (!$item) {
            $item = new ProductOrderItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setOrder($productOrder);
            $total = $productOrder->getTotal() + $product->getPrice();
            $productOrder->setTotal($total);
        } else {
            $item->setQuantity($quantity);
            $total = $product->getPrice() * $quantity;
            $productOrder->setTotal($total);
        }
        $this->getEm()->persist($item);
        $this->update($productOrder);

        return $item;
    }

    public function getProductItem($product, $productOrder)
    {
        $productOrder->getItems();
        foreach ($productOrder->getItems() as $item) {
            if ($product->getId() == $item->getProduct()->getId()) {
                return $item;
            }
        }
        return false;
    }

    public function updateOrderAmount($productOrder)
    {
        $total = 0;
        foreach ($productOrder->getItems() as $item) {
            $total += $item->getQuantity() * $item->getProduct()->getPrice();
        }
        $productOrder->setTotal($total);
        $this->update($productOrder);
        return $total;
    }

    public function supportsClass($class)
    {
        return $class === 'Amulen\ShopBundle\Entity\ProductOrder';
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
