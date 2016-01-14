<?php

namespace Flowcode\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
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
            ->add('enabled')
            ->add('parent', 'y_tree', array(
                   'class' => "Amulen\ClassificationBundle\Entity\Category",
                   'orderFields' => array('root' => 'asc','lft' => 'asc'),
                   'prefixAttributeName' => 'data-level-prefix',
                   'treeLevelField' => 'lvl',
                   'required' => false,
                   'multiple' => false,
                   'attr' => array("class" => "tall"))
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flowcode_shopbundle_category';
    }
}
