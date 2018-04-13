<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;


class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, array(
                'attr' => array('class' => 'simple-input')
            ))
            ->add('name', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'simple-input')
            ))
            ->add('second_name', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'simple-input')
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('attr' => array('class' => 'simple-input')),
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('register', SubmitType::class, array(
                'label' => 'Register',
                'attr' => array('class' => 'modern')
            ))
            ->getForm();

        $form->handleRequest($request);
        //var_dump($form);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $user->setAccess('0');
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('register');
        }


        return $this->render('register/index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
