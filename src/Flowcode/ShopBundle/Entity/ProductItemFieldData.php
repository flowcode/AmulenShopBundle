<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductItemFieldData
 */
class ProductItemFieldData
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
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\Product", inversedBy="productItemFieldDatas")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\ProductItemField")
     * @JoinColumn(name="product_item_field_id", referencedColumnName="id")
     */
    protected $productItemField;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=255, nullable=true)
     */
    protected $data;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

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
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * Set productItemField
     *
     * @param string $productItemField
     *
     * @return ProductItemFieldData
     */
    public function setProductItemField($productItemField)
    {
        $this->productItemField = $productItemField;

        return $this;
    }

    /**
     * Get productItemField
     *
     * @return string
     */
    public function getProductItemField()
    {
        return $this->productItemField;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return ProductItemFieldData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ProductItemFieldData
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
}

