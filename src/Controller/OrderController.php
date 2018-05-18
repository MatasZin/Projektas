<?php

namespace App\Controller;

use App\Criteria\OrderFilter;
use App\Entity\Car;
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
        $cars = $user->getCars()->filter( function($entry) {
            return $entry->getIsActive() === TRUE;
        });

        $orders = array();
        foreach ($cars as $car){
            $ordersTmp = $car->getOrders();
            $orders = array_merge($orders, $ordersTmp->toArray());
        }
        return $this->render('order/index.html.twig', array ('orders' => $orders));
    }

    private function getDatesAndCounts($allDates) {
        $uniqueDates = array();
        for ($i = 0; $i < count($allDates); $i++) {
            if($allDates[$i] !== null) {
                $currentDate = $allDates[$i]->format('Y-m-d');
                $allDates[$i] = null;
                $uniqueDates[] = array('date' => \DateTime::createFromFormat("Y-m-d", $currentDate), 'count' => 1);
                for ($j = 0; $j < count($allDates); $j++) {
                    if ($allDates[$j] !== null && $allDates[$j]->format('Y-m-d') === $currentDate) {
                        $uniqueDates[count($uniqueDates) - 1]['count'] += 1;
                        $allDates[$j] = null;
                    }
                }
            }
        }
        return $uniqueDates;
    }

    private function getForbiddenDays($datesAndCounts, $limit) {
        $forbiddenDates = array();
        foreach ($datesAndCounts as $date) {
            if($date['count'] >= $limit) {
                $forbiddenDates[] = $date['date'];
            }
        }
        return $forbiddenDates;
    }

    /**
     * @Route("/order/new", name="order_new")
     * @Method({"GET", "POST"})
     */
    public function RegisterNewOrderAction(Request $request)
    {
        $step = 1;
        $user = $this->getUser();
        $cars = $user->getCars()->filter( function($entry) {
            return $entry->getIsActive() === TRUE;
        });
        $allDates = $this->getDoctrine()->getRepository(Order::class)->getAllOrderDates();
        $limitDates = $this->getForbiddenDays($this->getDatesAndCounts($allDates), 10);

        $services = $this->getDoctrine()->getRepository(Services::class)->findBy(array(
            'isActive' => true,
        ));
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
                        'allDates' => $allDates,
                        'limitDates' => $limitDates
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
                        'allDates' => $allDates,
                        'limitDates' => $limitDates
                    ]);
                }
                $order = $allFormData['order'];
                $order->setOrderEndDate(null);
                $entityManager = $this->getDoctrine()->getManager();
                $car = $order->getCar();
                /********************   CAR REGISTRATION    ******************/
                if ($cars->isEmpty()) {
                    $carTaken = $this->getDoctrine()->getRepository(Car::class)->findOneBy(
                        array(
                            'licensePlate' => $car->getLicensePlate(),
                            'isActive' => true,
                        )
                    );

                    if ($carTaken === null){
                        $getCarIfUserIsOwner = $this->getDoctrine()->getRepository(Car::class)->findOneBy(
                            array(
                                'owner' => $user,
                                'licensePlate' => $car->getLicensePlate(),
                                'isActive' => False,
                            )
                        );
                        if ($getCarIfUserIsOwner === null){
                            $car->setOwner($user);
                            $entityManager->persist($car);
                        }else{
                            $getCarIfUserIsOwner->setIsActive(true);
                        }
                    }else{
                        $this->addFlash("warning", "This car is already registered on our system!");
                        $step = 1;
                        return $this->render('order/new.html.twig', [
                            'form1' => $form1->createView(),
                            'step' => $step,
                        ]);
                    }
                /********************   CAR REGISTRATION DONE   **********************/
                }else{
                /********************   CAR SELECTED THINGS   **********************/
                    $carOrders = $car->getOrders()->filter( function($entry) {
                        return $entry->getCompleted() === FALSE;
                    });
                    if (!$carOrders->isEmpty()){
                        $this->addFlash("warning", "You can't choose this car, because it has unfinished order!");
                        $step = 1;
                        return $this->render('order/new.html.twig', [
                            'form1' => $form1->createView(),
                            'step' => $step,
                        ]);
                    }
                /********************   CAR SELECTED DONE   **********************/
                }

                $entityManager->persist($order);

                /********************  SELECTED SERVICE THINGS  *********************/
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
                    }
                }
                $entityManager->flush();
                /********************  SELECTED SERVICE DONE   *********************/
                $step = 2;
                return $this->render('order/new.html.twig', [
                    'step' => $step,
                    'services' => $allFormData,
                    'order' => $order,
                    'allDates' => $allDates,
                    'limitDates' => $limitDates
                ]);
            }
        }
        return $this->render('order/new.html.twig', [
            'form1' => $form1->createView(),
            'step' => $step,
            'allDates' => $allDates,
            'limitDates' => $limitDates
        ]);
    }

    /**
     * @Route("/order/{id}", name="show_order")
     */
    public function show($id)
    {
        $services = $this->getDoctrine()->getRepository(OrderedService::class)->findBy(array('order'=>$id));

        if (!empty($services)){
            return $this->render('order/show.html.twig', array ('services' => $services));
        }
        return $this->redirectToRoute('order');
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
