<?php
namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Form\OrderOptionsType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends Controller {

    /**
     * @Route("/Users", name="users")
     */
    public function index() {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_USER'));
        return $this->render('Users/index.html.twig', array('users' => $users));
    }

    /**
     * @Route("/profile", name="myProfile")
     */
    public function myProfile(Request $request){
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, array(
            'button_label' => 'Save',
            'is_edit' => true,
        ));
        $form->remove('password');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash("notice", "Profile is successfully updated!");
            return $this->render('Users/myProfile.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        return $this->render('Users/myProfile.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/profile/changepassword", name="myProfile_changePassword")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder){
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $encoderService = $this->container->get('security.password_encoder');
            if ($encoderService->isPasswordValid($user, $data['password'])){
                $password = $encoder->encodePassword($user, $data['newPassword']);
                $user->setPassword($password);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $this->addFlash("notice", "Password is successfully changed!");
                return $this->render('Users/changePassword.html.twig', array(
                    'form' => $form->createView(),
                ));
            }else{
                $form->get('password')->addError(new FormError("Current password is invalid."));
            }
        }
        return $this->render('Users/changePassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/Users/{id}", name="user_show")
     */
    public function show($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $cars = $user->getCars();
        return $this->render('Users/show.html.twig', array ('user' => $user, 'cars' => $cars));
    }

    /**
     * @Route("/Users/orders/{id}", name="user_order_show")
     */
    public function showOrders($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $cars = $user->getCars();
        $orders = array();
        foreach ($cars as $car){
            $ordersArray = $car->getOrders();
            $orders = array_merge($orders, $ordersArray->toArray());
        }
        $form=$this->createForm(OrderOptionsType::class);
        $form->handleRequest($request);
        return $this->render('admin/orders/index.html.twig', array (
            'form' => $form->createView(), 'message' => "List of orders:",'orders' => $orders));
    }
}
