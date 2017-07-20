<?php

namespace Flowcode\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductOrderStatusLog
 */
class ProductOrderStatusLog
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
     * @ManyToOne(targetEntity="\Amulen\UserBundle\Entity\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\ProductOrder", inversedBy="logs")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * */
    protected $order;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\ProductOrderStatus")
     * @JoinColumn(name="previous_status_id", referencedColumnName="id")
     */
    protected $previousStatus;

    /**
     * @ManyToOne(targetEntity="\Amulen\ShopBundle\Entity\ProductOrderStatus")
     * @JoinColumn(name="following_status_id", referencedColumnName="id")
     */
    protected $followingStatus;

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
     * @return \Amulen\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Amulen\UserBundle\Entity\User $user
     */
    public function setUser(\Amulen\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return \Amulen\ShopBundle\Entity\ProductOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param \Amulen\ShopBundle\Entity\ProductOrder $order
     */
    public function setOrder(\Amulen\ShopBundle\Entity\ProductOrder $order = null)
    {
        $this->order = $order;
    }

    /**
     * @return \Amulen\ShopBundle\Entity\ProductOrderStatus
     */
    public function getPreviousStatus()
    {
        return $this->previousStatus;
    }

    /**
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $previousStatus
     */
    public function setPreviousStatus(\Amulen\ShopBundle\Entity\ProductOrderStatus $previousStatus = null)
    {
        $this->previousStatus = $previousStatus;
    }

    /**
     * @return \Amulen\ShopBundle\Entity\ProductOrderStatus
     */
    public function getFollowingStatus()
    {
        return $this->followingStatus;
    }

    /**
     * @param \Amulen\ShopBundle\Entity\ProductOrderStatus $followingStatus
     */
    public function setFollowingStatus(\Amulen\ShopBundle\Entity\ProductOrderStatus $followingStatus = null)
    {
        $this->followingStatus = $followingStatus;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ProductOrderStatusLog
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
