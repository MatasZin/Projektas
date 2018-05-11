<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'attr' => array('class' => 'simple-input'),
                'label' => 'Email *',
            ))
            ->add('name', TextType::class, array(
                'attr' => array('class' => 'simple-input'),
                'label' => 'First name *',
            ))
            ->add('second_name', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'simple-input'),
                'label' => 'Second name',
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('attr' => array('class' => 'simple-input')),
                'first_options'  => array('label' => 'Password *'),
                'second_options' => array('label' => 'Repeat Password *'),
            ))
            ->add('register', SubmitType::class, array(
                'label' => 'Register',
                'attr' => array('class' => 'modern')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
