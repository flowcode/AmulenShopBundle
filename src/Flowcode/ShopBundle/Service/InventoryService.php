<?php

namespace Flowcode\ShopBundle\Service;

use Flowcode\ShopBundle\Entity\Inventory;
use Flowcode\ShopBundle\Entity\InventoryItem;
use Flowcode\ShopBundle\Model\Product;
use Flowcode\ShopBundle\Repository\InventoryItemRepository;
use Flowcode\ShopBundle\Repository\InventoryRepository;
use Flowcode\ShopBundle\Repository\ProductRepository;


/**
 * Class InventoryService
 * @package Flowcode\ShopBundle\Service
 */
class InventoryService
{

    /**
     * @var InventoryRepository
     */
    private $inventoryRepository;

    /**
     * @var InventoryItemRepository
     */
    private $inventoryItemRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var WarehouseProductService
     */
    private $warehouseProductService;

    /**
     * InventoryService constructor.
     * @param InventoryRepository $inventoryRepository
     * @param InventoryItemRepository $inventoryItemRepository
     * @param ProductRepository $productRepository
     * @param WarehouseProductService $warehouseProductService
     */
    public function __construct(InventoryRepository $inventoryRepository, InventoryItemRepository $inventoryItemRepository, ProductRepository $productRepository, WarehouseProductService $warehouseProductService)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->inventoryItemRepository = $inventoryItemRepository;
        $this->productRepository = $productRepository;
        $this->warehouseProductService = $warehouseProductService;
    }


    public function getNextCode()
    {
        $currentCount = $this->inventoryRepository->getTotalCount();
        return $currentCount + 1;
    }

    public function fillInventory(Inventory $inventory)
    {
        $warehouseService = $this->warehouseProductService;
        $items = $this->productRepository->findAll();
        /* @var InventoryItem $inventoryItem */
        /* @var \Amulen\ShopBundle\Entity\Product $item */
        foreach ($items as $item) {
            if ($item->getWarehouse() != null){
                $warehouseProduct = $warehouseService->getByWarehouseProduct($inventory->getWarehouse(), $item);
                $stock = $warehouseProduct->getStock();
                if ($stock != 0) {
                    $inventoryItem = new InventoryItem();
                    $inventoryItem->setInventory($inventory);
                    $inventoryItem->setProduct($item);
                    $inventoryItem->setStock($stock);
                    $inventory->addItem($inventoryItem);
                }
            }
        }

    }


    public function getItems(array $id)
    {
        $inventoryItems = $this->inventoryItemRepository->findBy(array("product" => $id));
        foreach ($id as $item){
        }
        return $inventoryItems;
    }

    public function itemIsValid(InventoryItem $inventoryItem)
    {
        if ($inventoryItem->getProduct() != null && $inventoryItem->getStock() != null) {
            return true;
        } else {
            return false;
        }

    }


}