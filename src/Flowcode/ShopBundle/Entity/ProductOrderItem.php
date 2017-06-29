<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductOrderItem
 */
class ProductOrderItem
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\ProductOrder", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product", inversedBy="items")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="items")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     **/
    protected $service;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="unit_price", type="float")
     */
    protected $unitPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="discount", type="float")
     */
    protected $discount;

    public function __construct()
    {
        $this->discount = 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return ProductOrderItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * Set order
     *
     * @param \Amulen\ShopBundle\Entity\ProductOrder $order
     * @return ProductOrderItem
     */
    public function setOrder(\Amulen\ShopBundle\Entity\ProductOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Amulen\ShopBundle\Entity\ProductOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product
     *
     * @param \Amulen\ShopBundle\Entity\Product $product
     * @return ProductOrderItem
     */
    public function setProduct(\Amulen\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Amulen\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return int
     */
    public function getSubtotal()
    {
        return $this->unitPrice * $this->quantity;
    }

    /**
     * Set service
     *
     * @param \Flowcode\ShopBundle\Entity\Service $service
     * @return ProductOrderItem
     */
    public function setService(\Flowcode\ShopBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \Flowcode\ShopBundle\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }
}
