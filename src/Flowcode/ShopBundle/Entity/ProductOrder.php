<?php

namespace Flowcode\ShopBundle\Entity;

use Amulen\ShopBundle\Entity\ProductOrderStatusLog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Amulen\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductOrder
 */
class ProductOrder
{
    const DISCOUNT_PORCENTAJE = 1;
    const DISCOUNT_NUMBER = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    protected $total;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable = true)
     */
    protected $subTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    protected $discount;

    /**
     * @var float
     *
     * @ORM\Column(name="totalDiscount", type="float", nullable=true)
     */
    protected $totalDiscount;

    /**
     * @var integer
     *
     * @ORM\Column(name="discountType", type="integer", nullable=true)
     */
    protected $discountType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @ManyToOne(targetEntity="ProductOrderStatus", inversedBy="productorders")
     * @JoinColumn(name="product_order_status_id", referencedColumnName="id")
     * */
    protected $status;

    /**
     * @ManyToOne(targetEntity="Amulen\UserBundle\Entity\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @OneToMany(targetEntity="ProductOrderItem", mappedBy="order", cascade={"persist", "remove"}, orphanRemoval=true)
     * */
    protected $items;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on = "update")
     * @ORM\Column(type = "datetime")
     */
    protected $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    protected $street;

    /**
     * @var integer
     *
     * @ORM\Column(name="streetNumber", type="integer", nullable=true)
     */
    protected $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="apartment", type="string", length=255, nullable=true)
     */
    protected $apartment;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     */
    protected $locality;

    /**
     * @var integer
     *
     * @ORM\Column(name="zipCode", type="string", length=64, nullable=true)
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    protected $province;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * Fecha de recepcion del comprador
     *
     * @var \DateTime
     *
     * @ORM\Column(name="shippingTime", type="datetime", nullable=true)
     */
    protected $shippingTime;

    /**
     * Fecha de envio definida por vendedor
     *
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    protected $deliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=255, nullable=true)
     */
    protected $comment;

    /**
     * @OneToMany(targetEntity="\Amulen\ShopBundle\Entity\ProductOrderStatusLog", mappedBy="order")
     * */
    protected $logs;

    function __construct()
    {
        $this->items = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->enabled = true;
        $this->total = 0;
        $this->subTotal = 0;
        $this->discountType = self::DISCOUNT_PORCENTAJE;
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
     * Set total
     *
     * @param float $total
     * @return ProductOrder
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set subTotal
     *
     * @param float $subTotal
     * @return ProductOrder
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get subTotal
     *
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return ProductOrder
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set totalDiscount
     *
     * @param float $totalDiscount
     * @return ProductOrder
     */
    public function setTotalDiscount($totalDiscount)
    {
        $this->totalDiscount = $totalDiscount;

        return $this;
    }

    /**
     * Get totalDiscount
     *
     * @return float
     */
    public function getTotalDiscount()
    {
        return $this->totalDiscount;
    }

    /**
     * Set discountType
     *
     * @param float $discountType
     * @return ProductOrder
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get discountType
     *
     * @return float
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return ProductOrder
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add items
     *
     * @param ProductOrderItem $items
     * @return ProductOrder
     */
    public function addItem(ProductOrderItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param ProductOrderItem $items
     */
    public function removeItem(ProductOrderItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return ProductOrder
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param \Flowcode\ShopBundle\Entity\ProductOrderStatus $status
     * @return ProductOrder
     */
    public function setStatus(\Flowcode\ShopBundle\Entity\ProductOrderStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Flowcode\ShopBundle\Entity\ProductOrderStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return ProductOrder
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set streetNumber
     *
     * @param integer $streetNumber
     *
     * @return ProductOrder
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return integer
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set apartment
     *
     * @param string $apartment
     *
     * @return ProductOrder
     */
    public function setApartment($apartment)
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * Get apartment
     *
     * @return string
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return ProductOrder
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set province
     *
     * @param string $province
     *
     * @return ProductOrder
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return ProductOrder
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return ProductOrder
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return ProductOrder
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set shippingTime
     *
     * @param \DateTime $shippingTime
     *
     * @return ProductOrder
     */
    public function setShippingTime($shippingTime)
    {
        $this->shippingTime = $shippingTime;

        return $this;
    }

    /**
     * Get shippingTime
     *
     * @return \DateTime
     */
    public function getShippingTime()
    {
        return $this->shippingTime;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate(\DateTime $deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ProductOrder
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return ProductOrder
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return array
     */
    public function getProductsAdded()
    {
        $productIds = [];

        /** @var ProductOrderItem $item */
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()) {
                array_push($productIds, $item->getId());
            }
        }
        return $productIds;
    }

    public function getItemTotalCount()
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()) {
                $count += $item->getQuantity();
            }
        }
        return $count;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Add log
     *
     * @param ProductOrderStatusLog $log
     * @return ProductOrder
     */
    public function addLog(ProductOrderStatusLog $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param ProductOrderStatusLog $log
     */
    public function removeLog(ProductOrderStatusLog $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
