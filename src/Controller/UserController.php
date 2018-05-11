<?php
namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController extends Controller {

    /**
     * @Route("/Users", name="users")
     */
    public function index() {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_USER'));
        return $this->render('Users/index.html.twig', array('users' => $users));
    }


    /**
     * @Route("/Users/{id}", name="user_show")
     */
    public function show($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $cars = $this->getDoctrine()->getRepository(Car::class)->findBy(array('owner' => $user));
        return $this->render('Users/show.html.twig', array ('user' => $user, 'cars' => $cars));
    }

}
