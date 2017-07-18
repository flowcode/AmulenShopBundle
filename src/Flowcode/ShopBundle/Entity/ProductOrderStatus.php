<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

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
     * @ORM\Column(name="invoiceable", type="boolean")
     */
    protected $invoiceable;

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
     * @ManyToMany(targetEntity="\Amulen\ShopBundle\Entity\ProductOrderStatus", inversedBy="followingSteps")
     * @JoinTable(name="shop_status_previous_following",
     *      joinColumns={@JoinColumn(name="order_status_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="related_order_status_id", referencedColumnName="id")}
     *      )
     */
    protected $previousSteps;

    /**
     * @ManyToMany(targetEntity="\Amulen\ShopBundle\Entity\ProductOrderStatus", mappedBy="previousSteps")
     */
    protected $followingSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productorders = new ArrayCollection();
        $this->orderCanceled = false;
        $this->orderDeleted = false;
        $this->stockModifier = false;
        $this->orderModificable = true;
        $this->invoiceable = false;
        $this->previousSteps = new ArrayCollection();
        $this->followingSteps = new ArrayCollection();
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
    public function isInvoiceable(): bool
    {
        return $this->invoiceable;
    }

    /**
     * @param bool $invoiceable
     */
    public function setInvoiceable(bool $invoiceable)
    {
        $this->invoiceable = $invoiceable;
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

    /**
     * Add previousStep
     *
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $previousStep
     * @return ProductOrderStatus
     */
    public function addPreviousStep(\Amulen\ShopBundle\Entity\ProductOrderStatus $previousStep = null)
    {
        $this->previousSteps[] = $previousStep;

        return $this;
    }

    /**
     * Remove previousStep
     *
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $previousStep
     */
    public function removePreviousStep(\Amulen\ShopBundle\Entity\ProductOrderStatus $previousStep = null)
    {
        $this->previousSteps->removeElement($previousStep);
    }

    /**
     * Get previousSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreviousSteps()
    {
        return $this->previousSteps;
    }

    /**
     * Add followingStep
     *
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $followingStep
     * @return ProductOrderStatus
     */
    public function addFollowingStep(\Amulen\ShopBundle\Entity\ProductOrderStatus $followingStep = null)
    {
        $followingStep->addPreviousStep($this);
        $this->followingSteps[] = $followingStep;

        return $this;
    }

    /**
     * Remove followingStep
     *
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $followingStep
     */
    public function removeFollowingStep(\Amulen\ShopBundle\Entity\ProductOrderStatus $followingStep = null)
    {
        $this->followingSteps->removeElement($followingStep);
    }

    /**
     * Get followingStep
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowingSteps()
    {
        return $this->followingSteps;
    }
}
