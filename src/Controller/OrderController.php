<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Services;
use App\Entity\Car;
use App\Form\OrderType;
use App\Form\ServicesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class OrderController extends Controller
{
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
                foreach ($selectedServices['selectedService'] as $selected){
                    $temp = new OrderedService();
                    $temp->setLastChangeDate();
                    $temp->setNote('');
                    $temp->setOrder($order);
                    $temp->setService($selected);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($temp);
                    $entityManager->flush();
                }
                $step = 3;
                return $this->render('order/index.html.twig', [
                    'step' => $step,
                    'services' => $selectedServices,
                    'order' => $order
                ]);
            }
        }
        return $this->render('order/index.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'step' => $step,
        ]);
    }
}
