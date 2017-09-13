<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ClassificationBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{

    private $categoryService;

    public function __construct($categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('detail')
            ->add('price')
            ->add('description')
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'choices' => $this->categoryService->findByRoot("service"),
                'choice_label' => function ($category) {
                    return strtolower($category->getName());
                },
                'multiple' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\Service'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flowcode_shopbundle_service';
    }
}
