<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, array(
                'attr' => array('class' => 'simple-input'),
            ))
            ->add('newPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match.',
                'options' => array('attr' => array('class' => 'simple-input')),
                'first_options'  => array('label' => 'Password *'),
                'second_options' => array('label' => 'Repeat Password *'),
            ))
            ->add('change', SubmitType::class, array(
                'label' => 'Change password',
                'attr' => array('class' => 'modern')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
