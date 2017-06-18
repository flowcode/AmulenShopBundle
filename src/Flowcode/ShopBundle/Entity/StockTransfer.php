<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * StockTransfer
 *
 * @ORM\Table(name="stock_transfer")
 * @ORM\Entity(repositoryClass="Flowcode\ShopBundle\Repository\StockTransferRepository")
 */
class StockTransfer
{

    const STATUS_DRAFT = 0;
    const STATUS_CONFIRMED = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse")
     * @ORM\JoinColumn(name="warehouse_from_id", referencedColumnName="id")
     */
    private $warehouseFrom;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse")
     * @ORM\JoinColumn(name="warehouse_to_id", referencedColumnName="id")
     */
    private $warehouseTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="StockTransferItem", mappedBy="stockTransfer")
     */
    private $items;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="\Amulen\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->date = new \DateTime();
        $this->status = self::STATUS_DRAFT;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return StockTransfer
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return StockTransfer
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
     *
     * @return StockTransfer
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
     * Set status
     *
     * @param integer $status
     *
     * @return StockTransfer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return StockTransfer
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add item
     *
     * @param \Flowcode\ShopBundle\Entity\StockTransferItem $item
     *
     * @return StockTransfer
     */
    public function addItem(\Flowcode\ShopBundle\Entity\StockTransferItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \Flowcode\ShopBundle\Entity\StockTransferItem $item
     */
    public function removeItem(\Flowcode\ShopBundle\Entity\StockTransferItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set user
     *
     * @param \Amulen\UserBundle\Entity\User $user
     *
     * @return StockTransfer
     */
    public function setUser(\Amulen\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Amulen\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set warehouseFrom
     *
     * @param \Flowcode\ShopBundle\Entity\Warehouse $warehouseFrom
     *
     * @return StockTransfer
     */
    public function setWarehouseFrom(\Flowcode\ShopBundle\Entity\Warehouse $warehouseFrom = null)
    {
        $this->warehouseFrom = $warehouseFrom;

        return $this;
    }

    /**
     * Get warehouseFrom
     *
     * @return \Flowcode\ShopBundle\Entity\Warehouse
     */
    public function getWarehouseFrom()
    {
        return $this->warehouseFrom;
    }

    /**
     * Set warehouseTo
     *
     * @param \Flowcode\ShopBundle\Entity\Warehouse $warehouseTo
     *
     * @return StockTransfer
     */
    public function setWarehouseTo(\Flowcode\ShopBundle\Entity\Warehouse $warehouseTo = null)
    {
        $this->warehouseTo = $warehouseTo;

        return $this;
    }

    /**
     * Get warehouseTo
     *
     * @return \Flowcode\ShopBundle\Entity\Warehouse
     */
    public function getWarehouseTo()
    {
        return $this->warehouseTo;
    }

    function __toString()
    {
        return $this->code;
    }


}
