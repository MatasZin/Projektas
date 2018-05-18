<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('licensePlate', TextType::class, array(
                'attr' => array(
                    'class' => 'simple-input',
                    'style' => 'width: auto;',
                ),
                'label' => 'Your car license plate number:',
            ))
        ;

        if ($options['need_button']){
            $builder
                ->add('AddCar', SubmitType::class, array(
                    'label' => 'Add car',
                    'attr' => array('class' => 'modern')
                ))
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
            'need_button' => true,
        ]);
    }
}
