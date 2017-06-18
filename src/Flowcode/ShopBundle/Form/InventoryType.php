<?php

namespace Flowcode\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InventoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('warehouse', null, ['required' => true])
            ->add('date')
            ->add('user');
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
