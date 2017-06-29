<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Amulen\ShopBundle\Entity\ProductOrder;
use Amulen\ShopBundle\Entity\ProductOrderItem;
use Amulen\ShopBundle\Entity\ProductOrderStatus;
use Amulen\ShopBundle\Entity\Service;
use Amulen\UserBundle\Entity\User;
use Amulen\UserBundle\Entity\UserAddress;
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
    protected $stockService;
    protected $productOrderItemRepository;

    public function __construct(EntityManager $em, EntityRepository $productOrderRepository, EntityRepository $productOrderStatusRepository, EventDispatcherInterface $dispatcher, EntityRepository $productOrderItemRepository, $stockService)
    {
        $this->em = $em;
        $this->productOrderRepository = $productOrderRepository;
        $this->productOrderStatusRepository = $productOrderStatusRepository;
        $this->dispatcher = $dispatcher;
        $this->productOrderItemRepository = $productOrderItemRepository;
        $this->stockService = $stockService;
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
     * Find by id.
     * @param  integer $id
     * @return ProductOrder        productOrder.
     */
    public function findByIdSorted($id)
    {
        return $this->productOrderRepository->findByIdSorted($id);
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
            $productOrder = $this->findByIdSorted($productOrderId);
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
    public function addProduct(Product $product, ProductOrder $productOrder, $quantity = 1, $discount = 0)
    {
        $item = $this->getProductItem($product, $productOrder);
        if (!$item) {
            $item = new ProductOrderItem();
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $item->setUnitPrice($product->getPrice());
            $item->setOrder($productOrder);
            $productOrder->addItem($item);
        } else {
            $oldQty = $item->getQuantity();
            $item->setQuantity($quantity + $oldQty);
        }
        if($discount > 0){
            $item->setDiscount($discount);
        }

        $subTotal = $productOrder->getSubTotal() + $product->getPrice() * $quantity;
        $productOrder->setSubTotal($subTotal);
        $total = $productOrder->getTotal() + $product->getPrice() * $quantity - $discount;
        $productOrder->setTotal($total);

        $this->getEm()->persist($item);
        $this->update($productOrder);

        return $item;
    }

    /**
     * @param Service $service
     * @param ProductOrder $productOrder
     * @return ProductOrderItem|bool
     */
    public function setService(Service $service, ProductOrder $productOrder)
    {
        $item = $this->getUniqueServiceItem($productOrder);
        if (!$item) {
            $item = new ProductOrderItem();
            $item->setService($service);
            $item->setQuantity(1);
            $item->setOrder($productOrder);
            $item->setUnitPrice($service->getPrice());
            $productOrder->addItem($item);
            $prevValue = 0;
        } else {
            $oldService = $item->getService();
            $item->setService($service);
            $item->setUnitPrice($service->getPrice());
            $prevValue = $oldService->getPrice();
        }

        $subTotal = $productOrder->getSubTotal() + $service->getPrice() - $prevValue;
        $productOrder->setSubTotal($subTotal);
        $productOrder->setTotal($subTotal);

        $productOrder->setTotalDiscount(null);
        $productOrder->setDiscount(null);

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

        $subTotal = $productOrder->getSubTotal() - $product->getPrice() * $oldQty;
        $subTotal += $product->getPrice() * $newQty;
        $productOrder->setSubTotal($subTotal);

        $total = $productOrder->getTotal() - $product->getPrice() * $oldQty;
        $total += $product->getPrice() * $newQty;
        $productOrder->setTotal($total);

        if ($newQty <= 0) {
            $discount = $item->getDiscount();
            $productOrder->setTotal($productOrder->getTotal() + $discount);
            $item->setDiscount(0);
            $productOrder->getItems()->removeElement($item);
        }

        $this->update($productOrder);

        return $item;
    }

    public function getProductItem($product, $productOrder)
    {
        foreach ($productOrder->getItems() as $item) {
            if ($item->getProduct()) {
                if ($product->getId() == $item->getProduct()->getId()) {
                    return $item;
                }
            }
        }
        return false;
    }

    public function getUniqueServiceItem($productOrder)
    {
        $service = $this->productOrderItemRepository->findOrderService($productOrder);
        if ($service) {
            return $service[0];
        } else {
            return false;
        }
    }

    public function hasShipping($productOrder)
    {
        return $this->getUniqueServiceItem($productOrder);
    }

    public function updateOrderAmount($productOrder)
    {
        $productTotal = 0;
        $serviceTotal = 0;
        foreach ($productOrder->getItems() as $item) {
            if ($item->getProduct()) {
                $productTotal += $item->getQuantity() * $item->getProduct()->getPrice();
            } else {
                $serviceTotal += $item->getQuantity() * $item->getService()->getPrice();
            }

        }
        $productOrder->setSubTotal($productTotal + $serviceTotal);
        $productOrder->setTotal($productTotal);
        $this->update($productOrder);
        return $productTotal + $serviceTotal;
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

            /* Stock management */
            /* @var StockService $stockService */
            $stockService = $this->stockService;

            /* @var ProductOrderItem $item */
            foreach ($order->getItems() as $item) {

                // Only if is not manual stock.
                if ($item->getProduct() && !$item->getProduct()->isManualStock()) {
                    $stockService->exitStock($item->getProduct()->getWarehouse(), $item->getProduct(), $item->getQuantity(), $order);
                }
            }

            return true;
        }

        return false;
    }

    //FIXME: Sacar de amulen y pasar a hanga.
    public function checkBonification(User $user, ProductOrder $order, $zone)
    {
        $bonification = false;
        if ($user->getTribeStatus() == User::TRIBE_STATUS_ACCEPTED && $zone == ProductOrder::ZONE_CABA_GBA && $user->getMembership() == User::MEMBERSHIP_COLLECTOR) {
            $bonification = true;
        }
        $shipping = $this->hasShipping($order);
        $shippingPrice = $shipping ? $shipping->getService()->getPrice() : 0;
        if ($bonification) {
            $order->setDiscount(100);
            $order->setTotalDiscount($shipping->getService()->getPrice());
            $order->setTotal($order->getSubTotal() - $shippingPrice);
        } else {
            if ($order->getTotalDiscount()) {
                $order->setTotal($order->getSubTotal() - $shippingPrice);
                $order->setSubTotal($order->getSubTotal() - $shippingPrice);
            }
            $order->setDiscount(null);
            $order->setTotalDiscount(null);
        }
        $this->update($order);
        return $order;
    }

    public function setShippingAddress(ProductOrder $order, UserAddress $address)
    {
        $order = $this->clearShippingAddress($order);
        $order->setStreet($address->getStreet());
        $order->setApartment($address->getApartment());
        $order->setZipCode($address->getPostalCode());
        $order->setProvince($address->getState());
        $order->setCity($address->getCity());
        $order->setCountry($address->getCountry());
        $this->update($order);

        return $order;
    }

    public function clearShippingAddress(ProductOrder $order)
    {
        $order->setStreet(null);
        $order->setStreetNumber(null);
        $order->setApartment(null);
        $order->setLocality(null);
        $order->setZipCode(null);
        $order->setProvince(null);
        $order->setCity(null);
        $order->setCountry(null);
        $this->update($order);

        return $order;
    }

    public function removeEmptyItems($order)
    {
        $items = $this->productOrderItemRepository->findRemovableProducts($order);
        foreach ($items as $item) {
            $order->removeItem($item);
        }
        $this->update($order);
        return $order;
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
