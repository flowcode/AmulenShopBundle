<?php

namespace Flowcode\ShopBundle\Form\Filter;

use Amulen\ShopBundle\Entity\Product;
use Amulen\UserBundle\Entity\User;
use Flowcode\ShopBundle\Entity\StockChangeLog;
use Flowcode\ShopBundle\Entity\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class StockChangeLogFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Type',
                'choices' => [
                    StockChangeLog::TYPE_ENTRY => 'stock_entry',
                    StockChangeLog::TYPE_EXIT => 'stock_exit',
                ]
            ])
            ->add('product', Select2EntityType::class, [
                'multiple' => false,
                'required' => false,
                'remote_route' => 'amulen_admin_product_find',
                'class' => Product::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 1,
                'language' => 'es',
                'placeholder' => 'Producto...',
            ])
            ->add('warehouse', EntityType::class, [
                'required' => false,
                'placeholder' => 'Warehouse',
                'class' => Warehouse::class,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Filtrar"
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'StockChangeLog',
        ));
    }

}
