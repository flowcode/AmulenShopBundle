<?php

namespace Flowcode\ShopBundle\Form;

use Amulen\ShopBundle\Entity\ShopSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShopSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, array(
                'choices' => array(
                    ShopSetting::CHECKOUT_MAIL => ShopSetting::CHECKOUT_MAIL,
                    ShopSetting::SHOP_AVAILABLE => ShopSetting::SHOP_AVAILABLE,
                    ShopSetting::BUTTON_PAYMENT_METHOD => ShopSetting::BUTTON_PAYMENT_METHOD
                ),
            ))
            ->add('value');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amulen\ShopBundle\Entity\ShopSetting',
            'translation_domain' => 'ShopSetting',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shop_setting';
    }
}
