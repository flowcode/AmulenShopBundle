<?php

namespace Flowcode\ShopBundle\Service;

use Doctrine\ORM\EntityRepository;
use Amulen\ShopBundle\Entity\Product;
use Flowcode\ShopBundle\Entity\StockChangeLog;
use Flowcode\ShopBundle\Entity\Warehouse;
use Flowcode\ShopBundle\Entity\WarehouseProduct;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class WarehouseProductService
{

    /**
     * @var EntityRepository
     */
    private $warehouseProductRepository;

    /**
     * StockChangeLogService constructor.
     * @param EntityRepository $warehouseProductRepository
     */
    public function __construct(EntityRepository $warehouseProductRepository)
    {
        $this->warehouseProductRepository = $warehouseProductRepository;
    }

    public function save(WarehouseProduct $warehouseProduct)
    {
        return $this->warehouseProductRepository->save($warehouseProduct);
    }

    /**
     * @param Warehouse $warehouse
     * @param Product $product
     * @return WarehouseProduct|null
     */
    public function getByWarehouseProduct(Warehouse $warehouse, Product $product)
    {
        $warehouseProduct = $this->warehouseProductRepository->findOneBy(array(
            'product' => $product,
            'warehouse' => $warehouse,
        ));

        if (!$warehouseProduct) {
            $warehouseProduct = new WarehouseProduct();
            $warehouseProduct->setProduct($product);
            $warehouseProduct->setWarehouse($warehouse);
            $this->warehouseProductRepository->save($warehouseProduct);
        }

        return $warehouseProduct;
    }

}