<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ClassificationBundle\Entity\Category;
use Amulen\ShopBundle\Entity\Product;
use Amulen\ShopBundle\Entity\Strategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Amulen\ClassificationBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StrategyType extends AbstractType
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
            ->add('product', EntityType::class, array(
                'class' => Product::class,
                'choice_label' => function ($product) {
                    return $product->getName();
                },
                'multiple' => false,
            ))
            ->add('categories', EntityType::class, array(
                'class' => Category::class,
                'choices' => $this->categoryService->findByRoot("product"),
                'choice_label' => function ($category, $key, $index) {
                    $prefix = "";
                    for ($i = 0; $i < $category->getLvl(); $i++) {
                        $prefix .= "-";
                    }
                    return strtolower($prefix . $category->getName());
                },
                'multiple' => true,
            ))
            ->add('factor', PercentType::class, array("label" => "Factor descuento"));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Strategy::class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flowcode_shopbundle_strategy';
    }
}
