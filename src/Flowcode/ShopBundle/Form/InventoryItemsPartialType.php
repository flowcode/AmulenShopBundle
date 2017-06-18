<?php

namespace Flowcode\ShopBundle\Form\Type;

use Flowcode\ShopBundle\Entity\Inventory;
use Flowcode\ShopBundle\Model\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class InventoryItemsPartialType extends AbstractType
{
    private $product;

    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * ProductRawMaterialType constructor.
     * @param Inventory $inventory
     */
    public function __construct(Inventory $inventory = null)
    {
        $this->inventory = $inventory;
        $items = [];
        foreach ($inventory->getItems() as $item) {
            if ($item && $item->getProduct()) {
                array_push($items, $item->getProduct());
            }
        };

        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('product', EntityType::class, array(
//                'class' => 'FlowerModelBundle:Stock\Product',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->getNotInInventoryQB($this->inventory->getId());
//                }))
            ->add('product')
            ->add('stock', null, array(
                "required" => true,
            ));
//            ->add('measureUnit', null, array('label' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flowcode\ShopBundle\Entity\InventoryItem',
            'translation_domain' => 'InventoryItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'inventoryitem';
    }
}
