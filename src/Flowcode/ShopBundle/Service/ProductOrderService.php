<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Amulen\ShopBundle\Entity\ProductOrder;
use Amulen\ShopBundle\Entity\ProductOrderItem;
use Amulen\ShopBundle\Entity\ProductOrderStatus;
use Amulen\ShopBundle\Entity\Service;
use Amulen\UserBundle\Entity\User;
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
            $productOrder->setSubTotal($total);
        } else {
            $oldQty = $item->getQuantity();
            $item->setQuantity($quantity + $oldQty);

            $total = $productOrder->getTotal() - $product->getPrice() * $oldQty;
            $total += $product->getPrice() * ($quantity + $oldQty);

            $productOrder->setTotal($total);
            $productOrder->setSubTotal($total);
        }
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
        if ($productOrder->getDiscount()) {
            $productOrder->setTotalDiscount($service->getPrice());
        } else {
            $productOrder->setTotalDiscount(0);
        }
        $subTotal = $productOrder->getSubTotal() + $service->getPrice() - $prevValue;
        $productOrder->setSubTotal($subTotal);
        $total = $subTotal - $productOrder->getTotalDiscount();
        $productOrder->setTotal($total);

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
        $productOrder->setSubTotal($total);

        if ($newQty <= 0) {
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
        foreach ($productOrder->getItems() as $item) {
            if (!is_null($item->getService())) {
                return $item;
            }
        }
        return false;
    }

    public function hasShipping($productOrder)
    {
        return $this->getUniqueServiceItem($productOrder);
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

    public function checkBonification(User $user, ProductOrder $order, $zone)
    {
        $bonification = false;
        if ($user->getTribeStatus() == User::TRIBE_STATUS_ACCEPTED && $zone == ProductOrder::ZONE_CABA_GBA) {
            switch ($user->getMembership()) {
                case User::MEMBERSHIP_COLLECTOR:
                    $bonification = true;
                    break;
                case User::MEMBERSHIP_FANATIC:
                    // Beneficio por 1año desde membresia
                    $now = new \DateTime();
                    $elapsed = $now->diff($user->getMembershipCreated());
                    if ($elapsed->format('%Y') == 0) {
                        // Beneficio para 1 solo pedido por mes
                        if (!$this->productOrderRepository->oneOrderPerMonth($user)) {
                            $bonification = true;
                        }
                    }
                    break;
                case User::MEMBERSHIP_EXPLORER:
                    // Beneficio por 6meses desde membresia
                    $now = new \DateTime();
                    $elapsed = $now->diff($user->getMembershipCreated());
                    if ($elapsed->format('%Y') == 0 && $elapsed->format('%m') < 6) {
                        // Beneficio para 1 solo pedido por mes
                        if (!$this->productOrderRepository->oneOrderPerMonth($user)) {
                            $bonification = true;
                        }
                    }
                    break;
                default:
                    $bonification = false;
            }
            if ($bonification) {
                $order->setDiscount(100);
            } else {
                $order->setDiscount(null);
            }
            $this->update($order);
        }
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
