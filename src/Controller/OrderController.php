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
                    $this->addFlash("warning", "Please select at least one service!");
                    $step = 1;
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
}
