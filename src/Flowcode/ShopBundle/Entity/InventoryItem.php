<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * InventoryItem
 *
 * @ORM\Table(name="stock_inventory_item")
 * @ORM\Entity(repositoryClass="Flowcode\ShopBundle\Repository\InventoryItemRepository")
 */
class InventoryItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Inventory", inversedBy="items")
     * @JoinColumn(name="inventory_id", referencedColumnName="id")
     */
    private $inventory;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product", inversedBy="items")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var float
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;

    /**
     * @var float
     *
     * @ORM\Column(name="real_stock", type="integer", nullable=true)
     */
    private $realStock;

    /**
     * @ManyToOne(targetEntity="MeasureUnit")
     * @JoinColumn(name="measure_unit_id", referencedColumnName="id")
     */
    protected $measureUnit;


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
     * Set inventory
     *
     * @param \Flowcode\ShopBundle\Entity\Inventory $inventory
     *
     * @return InventoryItem
     */
    public function setInventory(\Flowcode\ShopBundle\Entity\Inventory $inventory = null)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return \Flowcode\ShopBundle\Entity\Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return InventoryItem
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @return mixed
     */
    public function getRealStock()
    {
        return $this->realStock;
    }

    /**
     * @param mixed $realStock
     */
    public function setRealStock($realStock)
    {
        $this->realStock = $realStock;
    }



    /**
     * Set measureUnit
     *
     * @param \Flowcode\ShopBundle\Entity\MeasureUnit $measureUnit
     *
     * @return InventoryItem
     */
    public function setMeasureUnit(\Flowcode\ShopBundle\Entity\MeasureUnit $measureUnit = null)
    {
        $this->measureUnit = $measureUnit;

        return $this;
    }

    /**
     * Get measureUnit
     *
     * @return \Flowcode\ShopBundle\Entity\MeasureUnit
     */
    public function getMeasureUnit()
    {
        return $this->measureUnit;
    }

    /**
     * Set product
     *
     * @param \Amulen\ShopBundle\Entity\Product $product
     *
     * @return InventoryItem
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
}
