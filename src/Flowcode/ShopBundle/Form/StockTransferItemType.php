<?php

namespace Flowcode\ShopBundle\Form\Type;

use Glifery\EntityHiddenTypeBundle\Form\Type\EntityHiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StockTransferItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('units')
            ->add('stockTransfer', EntityHiddenType::class, [
                'class' => 'Flowcode\ShopBundle\Entity\StockTransfer',
            ])
            ->add('product')
            ->add('measureUnit');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flowcode\ShopBundle\Entity\StockTransferItem',
            'translation_domain' => 'StockTransferItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stocktransferitem';
    }
}
