<?php

namespace App\Form;

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
            ->add('selectedService', ChoiceType::class, array(
                'choices' => $services,
                'choice_label' => function (Services $entity = null) {
                    return $entity ? $entity->getTitle() : '';
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Please select all services:',
            ))
            ->add('order', HiddenType::class)
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
        ]);
    }

    public function getName()
    {
        return 'services';
    }
}
