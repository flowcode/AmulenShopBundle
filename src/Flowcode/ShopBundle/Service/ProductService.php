<?php

namespace Flowcode\ShopBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Description of ProductService
 *
 * @author Pedro Barri <pbarri@flowcode.com.ar>
 */
class ProductService
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
    }

    /**
     * Fin all products but current.
     *
     * @param bool $enabledOnly
     * @return mixed
     */
    public function getSQLQuery($products = null, $categories = null, $brands = null, $industries = null, $countries = null, $filterON)
    {
        $sql = "";
        $sql .= 'SELECT *, c.name as category, b.name as brand ';
        $sql .= 'FROM  ';
        $sql .= 'shop_product p,  ';
        $sql .= 'classification_category c, ';
        $sql .= 'shop_brand b ';
        $sql .= 'WHERE 1=1 ';
        $sql .= 'AND p.category_id = c.id ';
        $sql .= 'AND p.brand_id = b.id ';
        if($products){
            $sql .= 'AND p.id IN ( ' . implode(',',$products) . ' ) ';
            $filterON = true;
        }
        if($categories){
            $sql .= 'AND p.category_id IN ( ' . implode(',',$categories) . ' ) ';
            $filterON = true;
        }
        if($brands){
            $sql .= 'AND p.brand_id in ( ' . implode(',',$brands) . ' ) ';
            $filterON = true;
        }
        if($industries){
            $sql .= 'AND p.industry_id IN ( ' . implode(',',$industries) . ' ) ';
            $filterON = true;
        }
        if($countries){
            $sql .= 'AND p.country_id IN ( ' . implode(',',$countries) . ' ) ';
            $filterON = true;
        }
        $result = array("sql" => $sql, "filterON" => $filterON );
        return $result;
    }
}