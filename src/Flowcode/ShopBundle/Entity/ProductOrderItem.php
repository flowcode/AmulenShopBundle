<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

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
     * @ManyToOne(targetEntity="ProductOrder", inversedBy="items")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     * */
    protected $order;

    /**
     * @ManyToOne(targetEntity="Product", inversedBy="items")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $product;

    /**
     * @ManyToOne(targetEntity="Service", inversedBy="items")
     * @JoinColumn(name="service_id", referencedColumnName="id")
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
     * @param \Flowcode\ShopBundle\Entity\ProductOrder $order
     * @return ProductOrderItem
     */
    public function setOrder(\Flowcode\ShopBundle\Entity\ProductOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Flowcode\ShopBundle\Entity\ProductOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product
     *
     * @param \Flowcode\ShopBundle\Entity\Product $product
     * @return ProductOrderItem
     */
    public function setProduct(\Flowcode\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Flowcode\ShopBundle\Entity\Product
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
