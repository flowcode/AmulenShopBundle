<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Amulen\ShopBundle\Entity\ProductOrder;
use Amulen\ShopBundle\Entity\ProductOrderItem;
use Amulen\ShopBundle\Entity\ProductOrderStatus;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Flowcode\ShopBundle\Event\OrderStatusChangedEvent;
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

    protected $productOrderRepository;
    protected $productOrderStatusRepository;
    protected $dispatcher;

    public function __construct(EntityManager $em, EntityRepository $productOrderRepository, EntityRepository $productOrderStatusRepository, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->productOrderRepository = $productOrderRepository;
        $this->productOrderStatusRepository = $productOrderStatusRepository;
        $this->dispatcher = $dispatcher;
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

    /**
     * @param Product $product
     * @param ProductOrder $productOrder
     * @param int $quantity
     * @return ProductOrderItem|bool
     */
    public function addProduct(Product $product, ProductOrder $productOrder, $quantity = 1)
    {
        $item = $this->getProductItem($product, $productOrder);
        if (!$item) {
            $item = new ProductOrderItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setUnitPrice($product->getPrice());
            $item->setOrder($productOrder);
            $total = $productOrder->getTotal() + $product->getPrice();
            $productOrder->addItem($item);
            $productOrder->setTotal($total);
        } else {
            $oldQty = $item->getQuantity();
            $item->setQuantity($quantity + $oldQty);

            $total = $productOrder->getTotal() - $product->getPrice() * $oldQty;
            $total += $product->getPrice() * ($quantity + $oldQty);

            $productOrder->setTotal($total);
        }
        $this->getEm()->persist($item);
        $this->update($productOrder);

        return $item;
    }

    /**
     * @param Product $product
     * @param ProductOrder $productOrder
     * @param int $quantity
     * @return ProductOrderItem|bool
     */
    public function substractQuantity(Product $product, ProductOrder $productOrder, $quantity = 1)
    {
        $item = $this->getProductItem($product, $productOrder);

        $oldQty = $item->getQuantity();
        $newQty = $oldQty - $quantity;

        $item->setQuantity($newQty);

        $total = $productOrder->getTotal() - $product->getPrice() * $oldQty;
        $total += $product->getPrice() * $newQty;

        $productOrder->setTotal($total);

        if ($newQty <= 0) {
            $productOrder->getItems()->removeElement($item);
        }

        $this->update($productOrder);

        return $item;
    }


    public function getProductItem($product, $productOrder)
    {
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

    public function changeStatusTo(ProductOrder $order, $statusName)
    {
        /* @var ProductOrderStatus $orderStatusTo */
        $orderStatusTo = $this->productOrderStatusRepository->findOneBy([
            'name' => $statusName
        ]);

        if ($orderStatusTo) {

            $order->setStatus($orderStatusTo);
            $this->update($order);

            $OrderStatusChangedEvent = new OrderStatusChangedEvent($order);

            $this->dispatcher->dispatch(OrderStatusChangedEvent::NAME, $OrderStatusChangedEvent);

            return true;
        }

        return false;
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
