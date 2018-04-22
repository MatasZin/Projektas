<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Car;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrderController extends Controller
{
    /**
     * @Route("/order", name="order")
     * @Method({"GET", "POST"})
     */
    public function index()
    {
        $user = $this->getUser();
        $cars = $this->getDoctrine()->getRepository(Car::class)->findBy(array('owner'=>$user));
        $orders = $this->getDoctrine()->getRepository(Order::class)->findBy(array('car'=>$cars));
        return $this->render('order/index.html.twig', array ('orders' =>$orders));
    }

    /**
     * @Route("/order/{id}", name="show_order")
     */
    public function show($id)
    {
        $services=$this->getDoctrine()->getRepository(OrderedService::class)->findBy(array('order'=>$id));
        return $this->render('order/show.html.twig', array ('services' => $services));
    }
}
