<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * StockTransferItem
 *
 * @ORM\Table(name="stock_transfer_item")
 * @ORM\Entity(repositoryClass="Flowcode\ShopBundle\Repository\StockTransferItemRepository")
 */
class StockTransferItem
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
     * @ManyToOne(targetEntity="StockTransfer", inversedBy="items")
     * @JoinColumn(name="stock_transfer_id", referencedColumnName="id")
     */
    private $stockTransfer;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product", inversedBy="items")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="units", type="integer")
     */
    private $units;

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
     * Set units
     *
     * @param integer $units
     *
     * @return StockTransferItem
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get units
     *
     * @return int
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set stockTransfer
     *
     * @param \Flowcode\ShopBundle\Entity\StockTransfer $stockTransfer
     *
     * @return StockTransferItem
     */
    public function setStockTransfer(\Flowcode\ShopBundle\Entity\StockTransfer $stockTransfer = null)
    {
        $this->stockTransfer = $stockTransfer;

        return $this;
    }

    /**
     * Get stockTransfer
     *
     * @return \Flowcode\ShopBundle\Entity\StockTransfer
     */
    public function getStockTransfer()
    {
        return $this->stockTransfer;
    }

    /**
     * Set product
     *
     * @param \Amulen\ShopBundle\Entity\Product $product
     *
     * @return StockTransferItem
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
     * Set measureUnit
     *
     * @param \Flowcode\ShopBundle\Entity\MeasureUnit $measureUnit
     *
     * @return StockTransferItem
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
}
