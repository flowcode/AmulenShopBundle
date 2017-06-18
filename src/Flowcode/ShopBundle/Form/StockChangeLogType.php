<?php

namespace Flowcode\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StockChangeLogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('date')
            ->add('type')
            ->add('amount')
            ->add('balance')
            ->add('description')
            ->add('product')
            ->add('user')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flowcode\ShopBundle\Entity\StockChangeLog',
            'translation_domain' => 'StockChangeLog',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stockchangelog';
    }
}
