<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ShopBundle\Entity\ProductOrderStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProductOrderStatusType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('invoiceable')
            ->add('orderModificable')
            ->add('stockModifier')
            ->add('orderDeleted')
            ->add('orderCanceled')
            ->add('previousSteps', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'admin_orderstatus_find_all',
                'class' => ProductOrderStatus::class,
                'text_property' => 'name',
                'minimum_input_length' => 1,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => false,
                'language' => 'es',
                'placeholder' => 'Selecionar',
            ])
            ->add('followingSteps', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'admin_orderstatus_find_all',
                'class' => ProductOrderStatus::class,
                'text_property' => 'name',
                'minimum_input_length' => 1,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => false,
                'language' => 'es',
                'placeholder' => 'Selecionar',
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\ProductOrderStatus'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'amulen_shopbundle_productorderstatus';
    }
}
