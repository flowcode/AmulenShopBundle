<?php

namespace Flowcode\ShopBundle\Event;

use Amulen\ShopBundle\Entity\ProductOrder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Created by PhpStorm.
 * User: juanma
 * Date: 5/28/16
 * Time: 12:11 PM
 */
class OrderStatusChangedEvent extends Event
{
    const NAME = 'amulen.event.shop_order_status_changed';

    protected $order;

    public function __construct(ProductOrder $order)
    {
        $this->order = $order;
    }

    /**
     * @return ProductOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param ProductOrder $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }


}