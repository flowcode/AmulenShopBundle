<?php

namespace Flowcode\ShopBundle\Service;

use Doctrine\ORM\EntityRepository;
use Amulen\ShopBundle\Entity\Product;
use Flowcode\ShopBundle\Entity\StockChangeLog;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class StockChangeLogService
{

    /**
     * @var EntityRepository
     */
    private $stockChangeLogRepository;

    /**
     * StockChangeLogService constructor.
     * @param EntityRepository $stockChangeLogRepository
     */
    public function __construct(EntityRepository $stockChangeLogRepository)
    {
        $this->stockChangeLogRepository = $stockChangeLogRepository;
    }


    /**
     * @param Product $product
     * @param $quantity
     * @param $previousStock
     * @param \DateTime|null $dateTime
     * @param null $comments
     * @return StockChangeLog
     */
    public function getEntryChangeLog(Product $product, $quantity, $previousStock, \DateTime $dateTime = null, $comments = null)
    {
        $stockChangeLog = new StockChangeLog();
        $stockChangeLog->setProduct($product);
        $stockChangeLog->setAmount($quantity);
        $stockChangeLog->setBalance($previousStock + $quantity);
        $stockChangeLog->setType(StockChangeLog::TYPE_ENTRY);
        $stockChangeLog->setDate($dateTime);
        $stockChangeLog->setDescription($comments);

        return $stockChangeLog;
    }

    /**
     * @param Product $product
     * @param $quantity
     * @param $previousStock
     * @param \DateTime|null $dateTime
     * @param null $comments
     * @return StockChangeLog
     */
    public function getExitChangeLog(Product $product, $quantity, $previousStock, \DateTime $dateTime = null, $comments = null)
    {
        $stockChangeLog = new StockChangeLog();
        $stockChangeLog->setProduct($product);
        $stockChangeLog->setAmount($quantity);
        $stockChangeLog->setBalance($previousStock - $quantity);
        $stockChangeLog->setType(StockChangeLog::TYPE_EXIT);
        $stockChangeLog->setDate($dateTime);
        $stockChangeLog->setDescription($comments);

        return $stockChangeLog;
    }

    public function save(StockChangeLog $stockChangeLog)
    {
        return $this->stockChangeLogRepository->save($stockChangeLog);
    }

}