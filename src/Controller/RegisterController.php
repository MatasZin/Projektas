<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, array(
            'button_label' => 'Register',
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $date = new \DateTime();
            $uniqueString = $user->getEmail(). '' .$date->format('Y-m-d H:i:s');
            $token = str_replace(array('/', '+', '='), '', base64_encode($uniqueString));
            $user->setConfirmationToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            if ($this->sendConfirmationEmail($user->getEmail(), $token, $user->getName(), $mailer)){
                return $this->render('register/confirmationSent.html.twig', array(
                    'email' => $user->getEmail(),
                ));
            }
        }


        return $this->render('register/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sendConfirmationEmail($email, $token, $name, \Swift_Mailer $mailer){
        $message = (new \Swift_Message('Registration confirmation'))
            ->setFrom(['paumanma@gmail.com' => 'TeamPMM services'])
            ->setTo($email)
            ->setBody(
                $this->renderView('emails/registration.html.twig', array(
                    'name' => $name,
                    'token' => $token,
                )),
                'text/html'
            );
        if (!$mailer->send($message, $failures)) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @Route("/confirm/{token}", name="confirmAccount")
     */
    public function Confirm($token)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array(
            'confirmationToken' => $token,
        ));
        if($user !== null)
        {
            $user->setIsActive(true);
            $user->setConfirmationToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'successful',
                'Your account is confirmed successfully. You can login now.'
            );
        }
        return $this->redirectToRoute('login');
    }
}
