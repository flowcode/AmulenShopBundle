<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ClassificationBundle\Entity\Category;
use Amulen\ShopBundle\Entity\ProductItemField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yavin\Symfony\Form\Type\TreeType;

class ProductItemFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('fieldLabel')
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    ProductItemField::TYPE_STRING => 'String',
                    ProductItemField::TYPE_INTEGER => 'Integer',
                ),
            ))
            ->add('category', TreeType::class, array(
                'class' => Category::class,
                'orderFields' => array('root' => 'asc', 'lft' => 'asc'),
                'prefixAttributeName' => 'data-level-prefix',
                'treeLevelField' => 'lvl',
                'required' => false,
                'multiple' => false,
            ))
            ->add('requiredField')
            ->add('defaultValue')
            ->add('file', null, array("label" => "image"));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\ProductItemField',
            'translation_domain' => 'ProductItemField',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flowcode_shopbundle_productitemfield';
    }
}
