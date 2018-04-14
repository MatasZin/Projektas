<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;



class SuppController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $isRoleAdmin = $auth_checker->isGranted('ROLE_ADMIN');
        $isRoleWorker = $auth_checker->isGranted('ROLE_WORKER');
        $isRoleUser = $auth_checker->isGranted('ROLE_USER');
        return $this->render('index.html.twig', [
            'isRoleAdmin' => $isRoleAdmin,
            'isRoleWorker' => $isRoleWorker,
            'isRoleUser' => $isRoleUser,
        ]);
    }

}