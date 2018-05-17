<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Order;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cars = $options['cars'];
        $builder->add('orderDate', HiddenType::class);
        $builder->get('orderDate')->addModelTransformer(new CallbackTransformer(
            function ($orderDateAsDateTime) {
                if($orderDateAsDateTime != null) return $orderDateAsDateTime->format("Y-m-d H:i");
                else return null;
            },
            function ($orderDateAsString) {
                if($orderDateAsString !== null) return \DateTime::createFromFormat("Y-m-d H:i", $orderDateAsString);
                else return null;
            }
        ));
        if ($cars == null){
            $builder
                ->add('car', CarType::class, array(
                    'data_class' => Car::class,
                    'attr' => array(
                        'style' => 'margin: 5px 0px 20px 0px;'
                    )
                ));
        }else{
            $builder
                ->add('car', ChoiceType::class, array(
                    'choices' => $cars,
                    'choice_label' => function (Car $entity = null) {
                        return $entity ? $entity->getLicensePlate() : '';
                    },
                    'attr' => array(
                        'class' => 'custom-select',
                        'style' => 'margin: 5px 0px 20px 0px;'
                    ),
                    'label' => 'Please select the car:'
                ));
        }
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
