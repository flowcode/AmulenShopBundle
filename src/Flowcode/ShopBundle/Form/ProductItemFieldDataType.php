<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ShopBundle\Entity\ProductItemField;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductItemFieldDataType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('productItem');
        $builder->add('productItemField');*/


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {

            /** @var ProductItemField $entity */
            $entity = $event->getData();
            $settingField = $entity->getProductItemField();

            $form = $event->getForm();

            switch ($settingField->getType()) {
                case ProductItemField::TYPE_STRING:
                    $form->add('data', TextType::class, array(
                        'required' => $settingField->getRequiredField() ? true : false,
                        'label' => $settingField->getFieldLabel(),
                    ));
                    break;

                case ProductItemField::TYPE_DATETIME:
                    $form->add('data', DateTimeType::class, array(
                        'date_widget' => 'single_text',
                        'required' => $settingField->getRequiredField() ? true : false,
                    ));
                    break;

                case ProductItemField::TYPE_INTEGER:
                    $form->add('data', IntegerType::class, array(
                        'required' => $settingField->getRequiredField() ? true : false,
                    ));
                    break;

                default:
                    $form->add('data', TextType::class, array(
                        'required' => $settingField->getRequiredField() ? true : false,
                    ));
                    break;

            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\ProductItemFieldData',
            'translation_domain' => 'ProductItemFieldData',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flowcode_shopbundle_productitemfielddata';
    }
}
