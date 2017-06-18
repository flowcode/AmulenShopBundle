<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Amulen\ShopBundle\Entity\ProductOrder;
use Flowcode\ShopBundle\Entity\StockChangeLog;
use Flowcode\ShopBundle\Entity\StockLevel;
use Flowcode\ShopBundle\Entity\Warehouse;
use Flowcode\ShopBundle\Entity\WarehouseProduct;
use Flowcode\ShopBundle\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class StockService implements ContainerAwareInterface
{

    /**
     * @var WarehouseProductService
     */
    private $warehouseProductService;
    /**
     * @var StockChangeLogService
     */
    private $stockChangeLogService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = NULL)
    {
        $this->container = $container;
    }

    /**
     * StockService constructor.
     * @param WarehouseProductService $warehouseProductService
     * @param StockChangeLogService $stockChangeLogService
     * @param ProductRepository $productRepository
     * @param ContainerInterface $container
     */
    public function __construct(WarehouseProductService $warehouseProductService, StockChangeLogService $stockChangeLogService, ProductRepository $productRepository)
    {
        $this->warehouseProductService = $warehouseProductService;
        $this->stockChangeLogService = $stockChangeLogService;
        $this->productRepository = $productRepository;

    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param bool $affectRawMaterialStock
     */
    public function entryStock(Warehouse $warehouse, Product $product, $quantity = 1, $affectRawMaterialStock = false, $comments = null, \DateTime $date = null)
    {
        if (count($product->getRawMaterials()) > 0 && $affectRawMaterialStock) {
            $this->decreaseRawMaterial($warehouse, $product, $quantity);
        }
        $this->increaseProduct($warehouse, $product, $quantity, $comments);

    }

    public function exitStock(Warehouse $warehouse, Product $product, $quantity = 1, $sale = null, $affectRawMaterialStock = false, $comments = null, \DateTime $date = null)
    {
        $warehouseProduct = $this->warehouseProductService->getByWarehouseProduct($warehouse, $product);

        if ($product->isPack() && $warehouseProduct->getStock() <= 0) {
            if ($affectRawMaterialStock) {
                $this->decreaseRawMaterial($warehouse, $product, $quantity, $sale);
            }
            $this->increaseProduct($warehouse, $product, $quantity, $comments);
        }
        $this->decreaseProduct($warehouse, $product, $quantity, $sale, $comments);

    }


    public function increaseProduct(Warehouse $warehouse, Product $product, $quantity = 1, $comments = null, \DateTime $date = null)
    {
        $date = !$date ? new \DateTime() : $date;

        /* @var WarehouseProduct $warehouseProduct */
        $warehouseProduct = $this->warehouseProductService->getByWarehouseProduct($warehouse, $product);

        $previousStock = $warehouseProduct->getStock();
        $newStock = $previousStock + $quantity;

        $warehouseProduct->setStock($newStock);

        /* Set product stock if it's the default warehouse */
        if ($product->getWarehouse() == $warehouseProduct->getWarehouse()) {
            $product->setStock($newStock);
        }

        $this->warehouseProductService->save($warehouseProduct);

        /* log change log */
        /* @var StockChangeLog $stockChangeLog */
        $stockChangeLog = $this->stockChangeLogService->getEntryChangeLog($product, $quantity, $previousStock, $date, $comments);
        $stockChangeLog->setWarehouse($warehouseProduct->getWarehouse());

        $this->stockChangeLogService->save($stockChangeLog);
    }

    /**
     * @param Warehouse $warehouse
     * @param Product $product
     * @param int $quantity
     * @param Sale|null $sale
     * @param null $comments
     * @param \DateTime|null $date
     */
    public function decreaseProduct(Warehouse $warehouse, Product $product, $quantity = 1, ProductOrder $sale = null, $comments = null, \DateTime $date = null)
    {
        $date = !$date ? new \DateTime() : $date;

        /* @var WarehouseProduct $warehouseProduct */
        $warehouseProduct = $this->warehouseProductService->getByWarehouseProduct($warehouse, $product);

        /* stock calculation */
        $previousStock = $warehouseProduct->getStock();
        $newStock = $previousStock - $quantity;

        $warehouseProduct->setStock($newStock);

        /* Set product stock if it's the default warehouse */
        if ($product->getWarehouse() == $warehouseProduct->getWarehouse()) {
            $product->setStock($newStock);
        }
        $this->warehouseProductService->save($warehouseProduct);

        /* log change log */
        /* @var StockChangeLog $stockChangeLog */
        $stockChangeLog = $this->stockChangeLogService->getExitChangeLog($product, $quantity, $previousStock, $date, $comments);
        $stockChangeLog->setWarehouse($warehouseProduct->getWarehouse());
        $stockChangeLog->setProductOrder($sale);

        $this->stockChangeLogService->save($stockChangeLog);

        /* check notification level */
        $this->checkLevel($product);
    }


    public function decreaseRawMaterial(Warehouse $warehouse, Product $product, $quantity, $sale = null)
    {
        if ($this->isValid($product)) { // Se hace la validación para evitar loops infinítos en los productos, para asegurar que el nivel de stock no va a fallar.
            $comments = "Creación para " . $product->getName() . " (PACK)";
            foreach ($product->getRawMaterials() as $productRawMaterial) {
                /* @var Product $rawMaterial */
                $rawMaterial = $productRawMaterial->getRawMaterial();
                if (count($rawMaterial->getRawMaterials()) > 0 && $rawMaterial->getStock() == 0) {
                    $this->decreaseRawMaterial($rawMaterial->getWarehouse(), $rawMaterial, $quantity);
                }
                $this->decreaseProduct($rawMaterial->getWarehouse(), $rawMaterial, $quantity * $productRawMaterial->getQuantity(), $sale, $comments);
            }
            return true;
        } else {
            $this->logger = $this->container->get("logger");
            $this->logger->error("El producto no puede estar compuesto por si mismo, produce loop infinito");
            return false;

        }
    }

    public function saleStockLog(array $saleItemsRaw, Sale $sale)
    {
        foreach ($saleItemsRaw as $saleItemRaw) {
            if (isset($saleItemRaw['product'])) {
                $product = $this->productRepository->find($saleItemRaw['product']['id']);
                if (!$product) {
                    break;
                } else {
                    if ($product->isCompositionOnDemand()) {
                        foreach ($saleItemRaw['product']['raw_materials'] as $rawMaterial) {
                            $productRaw = $this->productRepository->find($rawMaterial['raw_material']['id']);
                            $this->exitStock($sale->getSalepoint()->getWarehouse(), $productRaw, $saleItemRaw['units'] * $rawMaterial['quantity'], $sale, true, "Composición para " . $product->getName());
                        }
                    } else {
                        /* descrease sale product */
                        $this->exitStock($sale->getSalepoint()->getWarehouse(), $product, $saleItemRaw['units'], $sale, true, "Venta realizada");
                    }
                }
            }
        }
    }

    public function checkLevel(Product $product)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /*$stockLevels = $em->getRepository(StockLevel\)->findBy(array(
            'product' => $product,
        ));
        /* @var StockLevel $stockLevel */
        /*foreach ($stockLevels as $stockLevel) {
            if ($product->getStock() <= $stockLevel->getStock() && $product->getStock() >= 0) {
                $notificationChannel = $stockLevel->getNotificationChannel();
                $handlerId = "flower.core.service.notification_handler." . $notificationChannel->getType();
                $notificationChannelHandler = $this->container->get($handlerId);
                $notificationChannelHandler->handle($notificationChannel, array("product" => $product, "level" => $stockLevel->getStock()));
            }*/
        //}*/
    }

    public function isValid(Product $product)
    {
        /* @var ProductRawMaterial $raw1 */
        $raws1 = $this->getRawMaterial($product);
        foreach ($raws1 as $raw1) {
            if ($raw1->getRawMaterial() == $product) {
                return false;
            }
            $raws2 = $this->getRawMaterial($raw1->getRawMaterial());
            /* @var ProductRawMaterial $raw2 */
            foreach ($raws2 as $raw2) {
                if ($raw2->getRawMaterial() == $product) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getRawMaterial(Product $product)
    {
        return $rawMaterial = $product->getRawMaterials();
    }


}