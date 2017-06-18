<?php


namespace Flowcode\ShopBundle\Service;


use Doctrine\ORM\EntityRepository;
use Flowcode\ShopBundle\Service\StockService;
use Flowcode\ShopBundle\Entity\StockTransfer;
use Flowcode\ShopBundle\Form\Type\StockTransferType;
use Flowcode\ShopBundle\Entity\StockTransferItem;


class StockTransferService
{
    /**
     * @var EntityRepository
     */
    private $stockTransferRepository;

    /**
     * @var StockService
     */
    private $stockService;

    /**
     * StockTransferService constructor.
     * @param EntityRepository $productRepository
     * @param StockService $stockService
     */
    public function __construct(EntityRepository $productRepository,StockService $stockService)
    {
        $this->stockTransferRepository = $productRepository;
        $this->stockService = $stockService;
    }

    public function getNextCode()
    {
        $currentCount = $this->stockTransferRepository->getTotalCount();
        return $currentCount + 1;
    }

    public function updateStock(StockTransfer $stockTransfer)
    {

        /* @var StockService $stockService */
        $stockService = $this->stockService;

        /* @var StockTransferItem $item */
        foreach ($stockTransfer->getItems() as $item) {

            /* Decrease on target warehouse */
            $stockService->decreaseProduct(
                $stockTransfer->getWarehouseFrom(),
                $item->getProduct(),
                $item->getUnits(),
                null,
                "Transferencia entre depósitos: " . $stockTransfer->getCode(),
                $stockTransfer->getDate()
            );

            /* Increase on target warehouse */
            $stockService->increaseProduct(
                $stockTransfer->getWarehouseTo(),
                $item->getProduct(),
                $item->getUnits(),
                "Transferencia entre depósitos: " . $stockTransfer->getCode(),
                $stockTransfer->getDate()
            );

        }
    }

}