<?php

namespace App\Controller;

use App\Criteria\OrderFilter;
use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Services;
use App\Form\ServicesType;
use App\Form\OrderOptionsType;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
        $cars = $user->getCars();

        $orders = array();
        foreach ($cars as $car){
            $ordersTmp = $car->getOrders();
            $orders = array_merge($orders, $ordersTmp->toArray());
        }
        return $this->render('order/index.html.twig', array ('orders' => $orders));
    }



    /**
     * @Route("/order/new", name="order_new")
     * @Method({"GET", "POST"})
     */
    public function RegisterNewOrderAction(Request $request)
    {
        $step = 1;
        $user = $this->getUser();
        $cars = $user->getCars();

        $services = $this->getDoctrine()->getRepository(Services::class)->findAll();
        $allFormData = null;
        $form1 = $this->createForm(ServicesType::class, $allFormData, array(
            'services' => $services,
            'cars' => $cars,
        ));

        $form1->handleRequest($request);
        if ('POST' == $request->getMethod()) {
            if ($form1->getClickedButton() && 'save' === $form1->getClickedButton()->getName()) {
                $allFormData = $form1->getData();
                if ($allFormData['selectedService'] == null){
                    if($allFormData['order']->getOrderDate() === null) $this->addFlash("warning", "Please select date and time!");
                    $this->addFlash("warning", "Please select at least one service!");
                    $step = 1;
                    $form1 = $this->createForm(ServicesType::class, $allFormData, array(
                        'services' => $services,
                        'cars' => $cars,
                    ));
                    return $this->render('order/new.html.twig', [
                        'form1' => $form1->createView(),
                        'step' => $step,
                    ]);
                }
                if($allFormData['order']->getOrderDate() === null) {
                    if ($allFormData['selectedService'] == null) $this->addFlash("warning", "Please select at least one service!");
                    $this->addFlash("warning", "Please select date and time!");
                    $step = 1;
                    $form1 = $this->createForm(ServicesType::class, $allFormData, array(
                        'services' => $services,
                        'cars' => $cars,
                    ));
                    return $this->render('order/new.html.twig', [
                        'form1' => $form1->createView(),
                        'step' => $step,
                    ]);
                }
                $order = $allFormData['order'];
                $order->setOrderEndDate(null);
                $entityManager = $this->getDoctrine()->getManager();
                if ($cars == null) {
                    $newCar = $order->getCar();
                    $newCar->setOwner($user);
                    $entityManager->persist($newCar);
                    $entityManager->flush();
                }
                $entityManager->persist($order);
                $entityManager->flush();

                foreach ($allFormData['selectedService'] as $selected){
                    $checkAlreadyExist = $this->getDoctrine()->getRepository(OrderedService::class)
                        ->findBy(array(
                            'order' => $order,
                            'service' => $selected,
                        ));
                    if ($checkAlreadyExist == null)
                    {
                        $temp = new OrderedService();
                        $temp->setLastChangeDate();
                        $temp->setNote('');
                        $temp->setOrder($order);
                        $temp->setService($selected);
                        $entityManager->persist($temp);
                        $entityManager->flush();
                    }
                }
                $step = 2;
                return $this->render('order/new.html.twig', [
                    'step' => $step,
                    'services' => $allFormData,
                    'order' => $order
                ]);
            }
        }
        return $this->render('order/new.html.twig', [
            'form1' => $form1->createView(),
            'step' => $step,
        ]);
    }

    protected function addFlash($type, $message){
        $flashbag = $this->get('session')->getFlashBag();

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
        $form=$this->createForm(OrderOptionsType::class);
        $form->handleRequest($request);
        $filter = new OrderFilter($form);
        $orders = $this->getDoctrine()->getRepository(Order::class)->findByOrderFilter($filter);
        return $this->render('admin/orders/index.html.twig', array(
            'form' => $form->createView(), 'message' => "List of orders:", 'orders' => $orders));
    }
}
