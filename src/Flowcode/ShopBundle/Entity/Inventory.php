<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Flower\UserBundle\Model\TenantAwareInterface;
use Flower\UserBundle\Model\TenantInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Inventory
 *
 * @ORM\Table(name="stock_inventory")
 * @ORM\Entity(repositoryClass="Flowcode\ShopBundle\Repository\InventoryRepository")
 */
class Inventory
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="InventoryItem", mappedBy="inventory", cascade={"persist"})
     */
    private $items;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="\Flowcode\ShopBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ManyToOne(targetEntity="\Amulen\UserBundle\Entity\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->date = new \DateTime();
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
     * Set code
     *
     * @param string $code
     *
     * @return Inventory
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
     * Set name
     *
     * @param string $name
     *
     * @return Inventory
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
     * Set status
     *
     * @param integer $status
     *
     * @return Inventory
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Inventory
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
     * @return Inventory
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
     * Add item
     *
     * @param \Flowcode\ShopBundle\Entity\InventoryItem $item
     *
     * @return Inventory
     */
    public function addItem(\Flowcode\ShopBundle\Entity\InventoryItem $item)
    {
        $this->items[] = $item;
        $item->setInventory($this);
        return $this;
    }

    /**
     * Remove item
     *
     * @param \Flowcode\ShopBundle\Entity\InventoryItem $item
     */
    public function removeItem(\Flowcode\ShopBundle\Entity\InventoryItem $item)
    {
        $this->items->removeElement($item);
        $item->setInventory(null);
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
     * @return Inventory
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

    function __toString()
    {
        return $this->getCode() . " " . $this->getName();
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Inventory
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
     * Set warehouse
     *
     * @param \Flowcode\ShopBundle\Entity\Warehouse $warehouse
     *
     * @return Inventory
     */
    public function setWarehouse(\Flowcode\ShopBundle\Entity\Warehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return \Flowcode\ShopBundle\Entity\Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}
