<?php
namespace App\Controller;

use App\Entity\Car;
use App\Entity\Order;
use App\Entity\User;
use App\Criteria\OrderFilter;
use App\Form\OrderOptionsType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


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
        //$filter = new OrderFilter($form);
        //$orders = $this->getDoctrine()->getRepository($this->getUser()->getCars()->getOrders())->findByOrderFilter($filter);
        //$orders = $this->getDoctrine()->getRepository(Order::class)->findByOrderFilterAndUser($filter,$id, $cars);
        return $this->render('admin/orders/index.html.twig', array (
            'form' => $form->createView(), 'message' => "List of orders:",'orders' => $orders));
    }

}
