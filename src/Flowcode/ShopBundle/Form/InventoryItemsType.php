<?php

namespace Flowcode\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Flowcode\ShopBundle\Form\Type\InventoryItemsPartialType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InventoryItemsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, array(
                'type' => new InventoryItemsPartialType($builder->getData()),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flowcode\ShopBundle\Entity\Inventory',
            'translation_domain' => 'Inventory',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'inventory';
    }
}
