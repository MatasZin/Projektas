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

class WorkersController extends Controller {

    /**
     * @Route("/workers", name="workers")
     */
    public function index() {
        $workers = $this->getDoctrine()->getRepository(User::class)->findBy(array('access'=>'1'));

        return $this->render('workers/index.html.twig', array('workers' => $workers));
    }

    /**
     * @Route("workers/add", name="add_worker")
     * @Method({"GET", "POST"})
     */
    public function add(Request $request) {
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
                'label' => 'Add new worker',
                'attr' => array('class' => 'modern')
            ))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $user->setAccess('1');
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('workers');
        }

        return $this->render('workers/add_worker.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/workers/remove{id}")
     * @Method({"GET"})
     */
    public function remove($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->index();
    }
}