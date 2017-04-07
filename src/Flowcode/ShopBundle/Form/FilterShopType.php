<?php

namespace Flowcode\ShopBundle\Form;

use Lexik\Bundle\FormFilterBundle\Filter\Condition\ConditionBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class FilterShopType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', Filters\NumberRangeFilterType::class, array(
                'label' => false,
                'left_number_options' => array(
                    'attr' => array(
                        'placeholder' => 'Precio desde'
                    ),
                    'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN_EQUAL
                ),
                'right_number_options' => array(
                    'attr' => array(
                        'placeholder' => 'Precio hasta'
                    ),
                    'condition_operator' => FilterOperands::OPERATOR_LOWER_THAN_EQUAL
                ),
            ))
            ->add('category', Filters\EntityFilterType::class, array(
                'class' => "Amulen\ClassificationBundle\Entity\Category",
                'choice_label' => function ($category) {
                    if(!is_null($category->getParent())){
                        return $category->getName();
                    }
                    return null;
                },
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if ($values['value']->isEmpty()) {
                        return null;
                    }

                    $paramName = sprintf('p_%s', str_replace('.', '_', $field));

                    $expression = $filterQuery->getExpr()->in($field, ':' . $paramName);
                    $expression .= ' OR ' . $filterQuery->getExpr()->in('cat.parent', ':' . $paramName);

                    $parameters = array($paramName => $values['value']);

                    return $filterQuery->createCondition($expression, $parameters);
                },
                'multiple' => true,
                'empty_data' => null
            ));
    }

    public function getBlockPrefix()
    {
        return 'item_filter';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering'),
            'filter_condition_builder' => function (ConditionBuilderInterface $builder) {
                $builder
                    ->root('and')
                    ->field('price')
                    ->orX()
                    ->field('category')
                    ->end()
                    ->end();
            }
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'flowcode_shopbundle_filter_shop';
    }
}
