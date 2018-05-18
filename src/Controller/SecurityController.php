<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils){

        /*if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirect($this->generateUrl('homepage'));
        }*/
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("/logout", name="logout")
     * @throws \RuntimeException
     */
    public function logoutAction()
    {
        throw new \RuntimeException('');
    }

    /**
     * @Route("/reset", name="resetPassword")
     * @Method({"GET", "POST"})
     */
    public function resetPasswordAction(Request $request, \Swift_Mailer $mailer){
        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array(
                'email' => $data['email'],
            ));
            if($user == null)
            {
                $this->addFlash('warning', 'User with this email address not found.');
                return $this->render('login/resetPassword.html.twig', array(
                    'form' => $form->createView(),
                ));
            }else{
                $date = new \DateTime();
                $uniqueString = $user->getEmail(). '' .$date->format('Y-m-d H:i:s');
                $token = str_replace(array('/', '+', '='), '', base64_encode($uniqueString));
                $user->setForgetPasswordToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                if ($this->sendResetingPasswordLink($user->getEmail(), $token, $user->getName(), $mailer)){
                    return $this->render('login/resetPasswordSent.html.twig', array(
                        'email' => $user->getEmail(),
                    ));
                }
            }
        }
        return $this->render('login/resetPassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/reset/{token}", name="createNewPassword")
     * @Method({"GET", "POST"})
     */
    public function createNewPasswordAction(Request $request, UserPasswordEncoderInterface $encoder, $token)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array(
            'forgetPasswordToken' => $token,
        ));
        if($user === null)
        {
            $this->get('session')->getFlashBag()->add(
                'warning',
                'Account with this resetting token is not found!'
            );
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(ChangePasswordType::class);
        $form->remove('password');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $password = $encoder->encodePassword($user, $data['newPassword']);
            $user->setPassword($password);
            $user->setForgetPasswordToken(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'successful',
                'Your user password has been successfully changed. You can login now.'
            );
            return $this->redirectToRoute('login');
        }

        return $this->render('login/resetPassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function sendResetingPasswordLink($email, $token, $name, \Swift_Mailer $mailer){
        $message = (new \Swift_Message('Password recovery'))
            ->setFrom(['paumanma@gmail.com' => 'PauManMa services'])
            ->setTo($email)
            ->setBody(
                $this->renderView('emails/resettingPassword.html.twig', array(
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
}