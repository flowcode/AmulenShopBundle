<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * ProductOrderStatus
 */
class ProductOrderStatus
{

    const STATUS_DRAFT = 'STATUS_DRAFT';
    const STATUS_PENDING = 'STATUS_PENDING';
    const STATUS_PAYED = 'STATUS_PAYED';
    const STATUS_COMPLETED = 'STATUS_COMPLETED';
    const STATUS_CANCELED = 'STATUS_CANCELED';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    protected $description;

    /**
     * @OneToMany(targetEntity="ProductOrder", mappedBy="status")
     * */
    protected $productorders;

    /**
     * @var boolean
     *
     * @ORM\Column(name="order_modificable", type="boolean")
     */
    protected $orderModificable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stock_modifier", type="boolean")
     */
    protected $stockModifier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="order_deleted", type="boolean")
     */
    protected $orderDeleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="order_canceled", type="boolean")
     */
    protected $orderCanceled;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productorders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderCanceled = false;
        $this->orderDeleted = false;
        $this->stockModifier = false;
        $this->orderModificable = true;
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
     * Set name
     *
     * @param string $name
     * @return ProductOrderStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ProductOrderStatus
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Add productorders
     *
     * @param \Flowcode\ShopBundle\Entity\ProductOrder $productorders
     * @return ProductOrderStatus
     */
    public function addProductorder(\Flowcode\ShopBundle\Entity\ProductOrder $productorders)
    {
        $this->productorders[] = $productorders;

        return $this;
    }

    /**
     * Remove productorders
     *
     * @param \Flowcode\ShopBundle\Entity\ProductOrder $productorders
     */
    public function removeProductorder(\Flowcode\ShopBundle\Entity\ProductOrder $productorders)
    {
        $this->productorders->removeElement($productorders);
    }

    /**
     * Get productorders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductorders()
    {
        return $this->productorders;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isOrderModificable(): bool
    {
        return $this->orderModificable;
    }

    /**
     * @param bool $orderModificable
     */
    public function setOrderModificable(bool $orderModificable)
    {
        $this->orderModificable = $orderModificable;
    }

    /**
     * @return bool
     */
    public function isStockModifier(): bool
    {
        return $this->stockModifier;
    }

    /**
     * @param bool $stockModifier
     */
    public function setStockModifier(bool $stockModifier)
    {
        $this->stockModifier = $stockModifier;
    }

    /**
     * @return bool
     */
    public function isOrderDeleted(): bool
    {
        return $this->orderDeleted;
    }

    /**
     * @param bool $orderDeleted
     */
    public function setOrderDeleted(bool $orderDeleted)
    {
        $this->orderDeleted = $orderDeleted;
    }

    /**
     * @return bool
     */
    public function isOrderCanceled(): bool
    {
        return $this->orderCanceled;
    }

    /**
     * @param bool $orderCanceled
     */
    public function setOrderCanceled(bool $orderCanceled)
    {
        $this->orderCanceled = $orderCanceled;
    }


}
