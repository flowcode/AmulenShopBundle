<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 */
class Product {

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
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
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
    protected $enabled;

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
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    function __construct() {
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Product
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Product
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set category
     *
     * @param \Amulen\ClassificationBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\Amulen\ClassificationBundle\Entity\Category $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Amulen\ClassificationBundle\Entity\Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Add tags
     *
     * @param \Amulen\ClassificationBundle\Entity\Tag $tags
     * @return Product
     */
    public function addTag(\Amulen\ClassificationBundle\Entity\Tag $tags) {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Amulen\ClassificationBundle\Entity\Tag $tags
     */
    public function removeTag(\Amulen\ClassificationBundle\Entity\Tag $tags) {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Set mediaGallery
     *
     * @param \Amulen\MediaBundle\Entity\Gallery $mediaGallery
     * @return Product
     */
    public function setMediaGallery(\Amulen\MediaBundle\Entity\Gallery $mediaGallery = null) {
        $this->mediaGallery = $mediaGallery;

        return $this;
    }

    /**
     * Get mediaGallery
     *
     * @return \Amulen\MediaBundle\Entity\Gallery
     */
    public function getMediaGallery() {
        return $this->mediaGallery;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug) {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Product
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Product
     */
    public function setUpdated($updated) {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated() {
        return $this->updated;
    }

    public function getImage() {
        if (!is_null($this->mediaGallery) && $this->mediaGallery->getGalleryItems()->count() > 0) {
            $media = $this->getMediaGallery()->getGalleryItems()->first()->getMedia();
        }else{
            $media = new \Amulen\MediaBundle\Entity\Media();
            $media->setName("default image");
            $media->setPath("uploads/default.jpg");
        }
        return $media;
    }

}
