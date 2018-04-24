<?php

namespace App\Controller;

use App\Criteria\OrderFilter;
use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Services;
use App\Entity\Car;
use App\Form\OrderType;
use App\Form\ServicesType;
use App\Repository\OrderRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
     * @Route("/order/new", name="order_new")
     * @Method({"GET", "POST"})
     */
    public function RegisterNewOrderAction(Request $request)
    {
        $step = 1;
        $user = $this->getUser();
        $cars = $this->getDoctrine()->getRepository(Car::class)
            ->findBy(array(
                'owner' => $user,
            ));

        $services = $this->getDoctrine()->getRepository(Services::class)->findAll();
        $order = new Order();
        $form1 = $this->createForm(OrderType::class, $order, array(
            'cars' => $cars,
        ));
        $selectedServices = null;
        $form2 = $this->createForm(ServicesType::class, $selectedServices, array(
            'services' => $services,
        ));

        $form1->handleRequest($request);
        $form2->handleRequest($request);
        if ('POST' == $request->getMethod()) {
            if ($form1->getClickedButton() && 'next' === $form1->getClickedButton()->getName() && $form1->isValid()) {
                $order = $form1->getData();
                $order->setOrderEndDate(null);
                $entityManager = $this->getDoctrine()->getManager();
                if ($cars == null) {
                    $newCar = $order->getCar();
                    $newCar->setOwner($user);
                    $entityManager->persist($newCar);
                    $entityManager->flush();
                }
                //$form2->get('order')->setData($order->getId());
                $entityManager->persist($order);
                $entityManager->flush();
                $step = 2;
                $form2->setData(array('order'=>$order->getId()));
            }
            if ($form2->getClickedButton() && 'save' === $form2->getClickedButton()->getName()) {
                $selectedServices = $form2->getData();
                $order = $this->getDoctrine()
                    ->getRepository(Order::class)
                    ->find((integer)$selectedServices['order']);
                if ($selectedServices['selectedService'] == null){
                    $this->addFlash("warning", "Please select at least one service!");
                    $step = 2;
                    return $this->render('order/new.html.twig', [
                        'form1' => $form1->createView(),
                        'form2' => $form2->createView(),
                        'step' => $step,
                    ]);
                }
                foreach ($selectedServices['selectedService'] as $selected){
                    $ckeckAlreadyExist = $this->getDoctrine()->getRepository(OrderedService::class)
                        ->findBy(array(
                            'order' => $order,
                            'service' => $selected,
                        ));
                    if ($ckeckAlreadyExist == null)
                    {
                        $temp = new OrderedService();
                        $temp->setLastChangeDate();
                        $temp->setNote('');
                        $temp->setOrder($order);
                        $temp->setService($selected);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($temp);
                        $entityManager->flush();
                    }
                }
                $step = 3;
                return $this->render('order/new.html.twig', [
                    'step' => $step,
                    'services' => $selectedServices,
                    'order' => $order
                ]);
            }
        }
        return $this->render('order/new.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'step' => $step,
        ]);
    }

    protected function addFlash($type, $message){
        $flashbag = $this->get('session')->getFlashBag();

        // Add flash message
        $flashbag->add($type, $message);
    }

    /**
     * @Route("/order/{id}", name="show_order")
     */
    public function show($id)
    {
        $services=$this->getDoctrine()->getRepository(OrderedService::class)->findBy(array('order'=>$id));
        return $this->render('order/show.html.twig', array ('services' => $services));
    }

    /**
     * @Route("/admin/orders", name="admin_orders")
     */
    public function admin_orders(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('completeness', CheckboxType::class, array(
                'label'=>"Show completed orders?", 'required'=>false))
            ->add('orderby', ChoiceType::class, array(
                'label'=>"Sort by: ", 'choices'=>array(
                    'Date ordered'=>'orderDate', 'Date completed'=>'orderEndDate', 'Completeness'=>'completed')))
            ->add('sortorder', ChoiceType::class, array(
                'label'=>"Sorting order: ", 'choices'=>array('Ascending'=>'ASC', 'Descending'=>'DESC')))
            ->add('submit', SubmitType::class, array(
                'label'=>"Go!", 'attr' => array('class' => 'modern')))
            ->getForm();
        $form->handleRequest($request);
        $filter = new OrderFilter($form);
        $orders = $this->getDoctrine()->getRepository(Order::class)->findByOrderFilter($filter);
        return $this->render('admin/orders/index.html.twig', array(
            'form' => $form->createView(), 'message' => "List of orders:", 'orders' => $orders));
    }
}
