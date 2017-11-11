<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ShopBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProductRawMaterialType extends AbstractType
{

    private $product;

    /**
     * ProductRawMaterialType constructor.
     * @param Product $product
     */
    public function __construct(Product $product = null)
    {
        $this->product = $product;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('rawMaterial', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'admin_product_find',
                'class' => Product::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 1,
                'language' => 'es',
                'placeholder' => 'Elegir producto',
                'width' => '100%',
                'autostart' => true
            ])
            ->add('quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\ProductRawMaterial',
            'translation_domain' => 'ProductRawMaterial',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'productrawmaterial';
    }
}
