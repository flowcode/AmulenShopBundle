<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ClassificationBundle\Entity\Category;
use Amulen\ShopBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Amulen\ClassificationBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
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
            ->add('description')
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'choices' => $this->categoryService->findByRoot("product"),
                'choice_label' => function ($category, $key, $index) {
                    $prefix = "";
                    for ($i = 0; $i < $category->getLvl(); $i++) {
                        $prefix .= "-";
                    }
                    return strtolower($prefix . $category->getName());
                },
                'multiple' => false,
            ))
            ->add('price', 'text', array("label" => "Precio"))
            ->add('enabled')
            ->add('pack')
            ->add('warehouse')
            ->add('featured')
            ->add('manualStock')
            ->add('manualPackPricing')
            ->add('tags', 'collection', array("type" => new Tag(), "label" => "Etiquetas"))
            ->add('mediaGallery', null, array("label" => "Galería de medios"))
            ->add('content', 'ckeditor')
            ->add('brand')
            ->add('capacity')
            ->add('rawMaterials', 'collection', array(
                'type' => new ProductRawMaterialType($builder->getData()),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ))
            ->add('productItemFieldDatas', CollectionType::class, array(
                'entry_type' => ProductItemFieldDataType::class,
                'label' => false,
                'required' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class
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
