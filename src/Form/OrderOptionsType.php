<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('completeness', CheckboxType::class, array(
                'label'=>"Show completed orders?", 'required'=>false))
            ->add('orderby', ChoiceType::class, array(
                'label'=>"Sort by: ", 'choices'=>array(
                    'Date ordered'=>'orderDate', 'Date completed'=>'orderEndDate', 'Completeness'=>'completed')))
            ->add('sortorder', ChoiceType::class, array(
                'label'=>"Sorting order: ", 'choices'=>array('Ascending'=>'ASC', 'Descending'=>'DESC')))
            ->add('submit', SubmitType::class, array(
                'label'=>"Go!", 'attr' => array('class' => 'modern')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
