<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * ProductOrder
 */
class ProductRawMaterial
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
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product", inversedBy="rawMaterials")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product")
     * @JoinColumn(name="raw_material_id", referencedColumnName="id")
     */
    protected $rawMaterial;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * ProductRawMaterial constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getRawMaterial()
    {
        return $this->rawMaterial;
    }

    /**
     * @param mixed $rawMaterial
     */
    public function setRawMaterial($rawMaterial)
    {
        $this->rawMaterial = $rawMaterial;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }


}
