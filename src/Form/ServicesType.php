<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Services;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $services = $options['services'];
        $builder
            ->add('order', OrderType::class, array(
                'cars' => $options['cars'],
                'data_class' => Order::class,
                'label' => ' ',
                'attr' => array(
                    'style' => 'margin: 5px 0px 20px 0px;'
                ),
            ))
            ->add('selectedService', ChoiceType::class, array(
                'attr' => array(
                    'style' => 'margin: 5px 0px 20px 0px;'
                ),
                'choices' => $services,
                'choice_label' => function (Services $entity = null) {
                    return $entity ? $entity->getTitle() : '';
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Please select all services:',
            ))
            ->add('save', SubmitType::class, array(
                'attr' => array(
                    'class' => 'modern',
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'services' => Services::class,
            'cars' => Car::class,
        ]);
    }

    public function getName()
    {
        return 'services';
    }
}
