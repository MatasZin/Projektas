<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class WorkersController extends Controller {

    /**
     * @Route("/workers", name="workers")
     */
    public function index() {
        $auth_checker = $this->get('security.authorization_checker');
        if($auth_checker->isGranted('ROLE_ADMIN')) {
            $workers = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_WORKER'));
            return $this->render('workers/index.html.twig', array('workers' => $workers, 'admin' => $auth_checker->isGranted('ROLE_ADMIN')));
        }
        else {
            return $this->render('workers/index.html.twig', array('admin' => $auth_checker->isGranted('ROLE_ADMIN')));
        }
    }

    /**
     * @Route("workers/add", name="add_worker")
     * @Method({"GET", "POST"})
     */
    public function add(Request $request) {
        $auth_checker = $this->get('security.authorization_checker');
        if($auth_checker->isGranted('ROLE_ADMIN')) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();
                $user->setRole('ROLE_WORKER');
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('workers');
            }

            return $this->render('workers/add_worker.html.twig', array(
                'form' => $form->createView(), 'admin' => $auth_checker->isGranted('ROLE_ADMIN')));
        }
        else {
            return $this->render('workers/index.html.twig', array('admin' => $auth_checker->isGranted('ROLE_ADMIN')));
        }
    }

    /**
     * @Route("/workers/remove{id}")
     * @Method({"GET"})
     */
    public function remove($id) {
        $auth_checker = $this->get('security.authorization_checker');
        if($auth_checker->isGranted('ROLE_ADMIN')) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
            return $this->index();
    }
}