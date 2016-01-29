<?php

namespace Flowcode\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Amulen\ClassificationBundle\Entity\Tag;

class ProductType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', null, array('required' => true))
            ->add('category', 'y_tree', array(
                   'class' => "Amulen\ClassificationBundle\Entity\Category",
                   'orderFields' => array('root' => 'asc','lft' => 'asc'),
                   'prefixAttributeName' => 'data-level-prefix',
                   'treeLevelField' => 'lvl',
                   'required' => false,
                   'multiple' => false,
                   'attr' => array("class" => "tall")))
            ->add('price', 'text', array("label" => "Precio"))
            ->add('enabled')
            ->add('tags', 'collection', array("type" => new Tag(), "label" => "Etiquetas"))
            ->add('mediaGallery', null, array("label" => "Galería de medios"))
            ->add('content', 'ckeditor')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\Product'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flowcode_shopbundle_product';
    }
}
