<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Order;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cars = $options['cars'];
        $builder
            ->add('orderDate', DateTimeType::class, array(
                'data' => new \DateTime(),
                'label' => 'Select the time you want for the service:',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd  HH:mm',
                'attr' => array(
                    'class' => 'simple-input',
                    'style' => 'width: auto;'
                )
            ));
        if ($cars == null){
            $builder
                ->add('car', CarType::class);
        }else{
            $builder
                ->add('car', ChoiceType::class, array(
                    'choices' => $cars,
                    'choice_label' => function (Car $entity = null) {
                        return $entity ? $entity->getLicensePlate() : '';
                    },
                    'attr' => array(
                        'class' => 'custom-select',
                    ),
                    'label' => 'Please select the car:'
                ));
        }
        $builder
            ->add('next', SubmitType::class, array(
                'attr' => array(
                    'class' => 'modern',
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'cars' => Car::class,
        ]);
    }

    public function getName()
    {
        return 'order';
    }
}
