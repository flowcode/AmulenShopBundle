<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_featured", type="boolean")
     */
    protected $featured;

    /**
     * @ManyToOne(targetEntity="Amulen\ClassificationBundle\Entity\Category")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     * */
    protected $category;

    /**
     * @ManyToMany(targetEntity="Amulen\ClassificationBundle\Entity\Tag")
     * @JoinTable(name="product_tag",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     * */
    protected $tags;

    /**
     * @OneToOne(targetEntity="Amulen\MediaBundle\Entity\Gallery")
     * @JoinColumn(name="media_gallery_id", referencedColumnName="id")
     * */
    protected $mediaGallery;

    /**
     * @OneToOne(targetEntity="Amulen\MediaBundle\Entity\Gallery")
     * @JoinColumn(name="video_gallery_id", referencedColumnName="id")
     * */
    protected $videoGallery;

    /**
     * @ManyToOne(targetEntity="Amulen\ShopBundle\Entity\Brand")
     * @JoinColumn(name="brand_id", referencedColumnName="id")
     * */
    protected $brand;

    /**
     * @OneToMany(targetEntity="ProductOrderItem", mappedBy="product", cascade={"persist", "remove"})
     * */
    protected $items;

    /**
     * @var float
     *
     * @ORM\Column(name="stock", type="integer")
     */
    protected $stock;

    /**
     * Es el defecto por el producto.
     *
     * @ORM\ManyToOne(targetEntity="\Flowcode\ShopBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    /**
     * @OneToMany(targetEntity="\Flowcode\ShopBundle\Entity\WarehouseProduct", mappedBy="product")
     */
    protected $warehousesStock;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPack", type="boolean")
     */
    protected $pack;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->featured = false;
        $this->pack = false;
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
     * @return Product
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
     * @return Product
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
     * Set content
     *
     * @param string $content
     * @return Product
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Product
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
     * Set category
     *
     * @param \Amulen\ClassificationBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\Amulen\ClassificationBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Amulen\ClassificationBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add tags
     *
     * @param \Amulen\ClassificationBundle\Entity\Tag $tags
     * @return Product
     */
    public function addTag(\Amulen\ClassificationBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Amulen\ClassificationBundle\Entity\Tag $tags
     */
    public function removeTag(\Amulen\ClassificationBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set mediaGallery
     *
     * @param \Amulen\MediaBundle\Entity\Gallery $mediaGallery
     * @return Product
     */
    public function setMediaGallery(\Amulen\MediaBundle\Entity\Gallery $mediaGallery = null)
    {
        $this->mediaGallery = $mediaGallery;

        return $this;
    }

    /**
     * Get mediaGallery
     *
     * @return \Amulen\MediaBundle\Entity\Gallery
     */
    public function getMediaGallery()
    {
        return $this->mediaGallery;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Product
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
     * @return Product
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

    public function getImage()
    {
        if (!is_null($this->mediaGallery) && $this->mediaGallery->getGalleryItems()->count() > 0) {
            $media = $this->getMediaGallery()->getGalleryItems()->first()->getMedia();
        } else {
            $media = new \Amulen\MediaBundle\Entity\Media();
            $media->setName("default image");
            $media->setPath("uploads/default.jpg");
        }
        return $media;
    }

    /**
     * Set videoGallery
     *
     * @param \Amulen\MediaBundle\Entity\Gallery $videoGallery
     * @return Product
     */
    public function setVideoGallery(\Amulen\MediaBundle\Entity\Gallery $videoGallery = null)
    {
        $this->videoGallery = $videoGallery;

        return $this;
    }

    /**
     * Get videoGallery
     *
     * @return \Amulen\MediaBundle\Entity\Gallery
     */
    public function getVideoGallery()
    {
        return $this->videoGallery;
    }

    /**
     * Set brand
     *
     * @param \Amulen\ShopBundle\Entity\Brand $brand
     * @return Product
     */
    public function setBrand(\Amulen\ShopBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \Amulen\ShopBundle\Entity\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Add items
     *
     * @param ProductOrderItem $items
     * @return Product
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
     * @return boolean
     */
    public function isFeatured()
    {
        return $this->featured;
    }

    /**
     * @param boolean $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @return bool
     */
    public function isPack(): bool
    {
        return $this->pack;
    }

    /**
     * @param bool $pack
     */
    public function setPack(bool $pack)
    {
        $this->pack = $pack;
    }

    /**
     * @return mixed
     */
    public function getWarehouseStock($warehouseId)
    {
        /* @var WarehouseProduct $warehouseStock */
        foreach ($this->warehousesStock as $warehouseStock) {
            if ($warehouseStock->getWarehouse()->getId() == $warehouseId) {
                return $warehouseStock->getStock();
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getWarehousesStock()
    {
        return $this->warehousesStock;
    }

    /**
     * @param mixed $warehousesStock
     */
    public function setWarehousesStock($warehousesStock)
    {
        $this->warehousesStock = $warehousesStock;
    }


    /**
     * @return float
     */
    public function getStock(): float
    {
        return $this->stock;
    }

    /**
     * @param float $stock
     */
    public function setStock(float $stock)
    {
        $this->stock = $stock;
    }


}
