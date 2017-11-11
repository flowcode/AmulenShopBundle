<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\ProductItemField;

/**
 * Class ProductItemFieldService
 * @package Flowcode\ShopBundle\Service
 */
class ProductItemFieldService
{

    private $uploadsBaseDir;
    private $uploadsProductItemFieldDir;

    /**
     * ProductItemFieldService constructor.
     * @param $uploadsBaseDir
     * @param $uploadsProductItemFieldDir
     */
    public function __construct($uploadsBaseDir, $uploadsProductItemFieldDir)
    {
        $this->uploadsBaseDir = $uploadsBaseDir;
        $this->uploadsProductItemFieldDir = $uploadsProductItemFieldDir;
    }


    /**
     * @param ProductItemField $entity
     * @return ProductItemField
     */
    public function uploadImage(ProductItemField $entity)
    {

        /* the file property can be empty if the field is not required */
        if (null === $entity->getFile()) {
            return $entity;
        }

        $uploadBaseDir = $this->uploadsBaseDir;
        $uploadDir = $this->uploadsProductItemFieldDir;

        /* set the path property to the filename where you've saved the file */
        $filename = $entity->getFile()->getClientOriginalName();
        $extension = $entity->getFile()->getClientOriginalExtension();

        $imageName = md5($filename . time()) . '.' . $extension;

        $entity->setImage($uploadDir . $imageName);
        $entity->getFile()->move($uploadBaseDir . $uploadDir, $imageName);

        $entity->setFile(null);

        return $entity;
    }

}