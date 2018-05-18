<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Order;
use App\Entity\User;
use App\Form\CarType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CarsController extends Controller
{
    /**
     * @Route("/cars", name="cars")
     * @Method({"GET", "POST"})
     */
    public function RegisterCarAction(Request $request)
    {
        $user = $this->getUser();
        $cars = $user->getCars()->filter( function($entry) {
            return $entry->getIsActive() === TRUE;
        });
        $orderCount = array();

        foreach ($cars as $car) {

            $orderCount[$car->getId()] = sizeof($car->getOrders());
        }
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $car = $form->getData();
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
                $entityManager = $this->getDoctrine()->getManager();
                if ($getCarIfUserIsOwner === null){
                    $car->setOwner($user);
                    $entityManager->persist($car);
                }else{
                    $getCarIfUserIsOwner->setIsActive(true);
                }
                $entityManager->flush();
                $this->get('session')->getFlashBag()->add(
                    'successful',
                    'Your car has been successfully registered!'
                );
                return $this->redirectToRoute('cars');
            }
            $this->addFlash("warning", "This car is already registered on our system!");
            return $this->render('cars/index.html.twig', [
                'form' => $form->createView(),
                'cars' => $cars,
                'orderCount' => $orderCount,
            ]);
        }

        return $this->render('cars/index.html.twig', [
            'form' => $form->createView(),
            'cars' => $cars,
            'orderCount' => $orderCount,
        ]);
    }

    /**
     * @Route("/cars/remove/{car_id}", name="cars_remove")
     * @Method({"GET", "POST"})
     * @ParamConverter("car", class="App\Entity\Car", options={"id" = "car_id"})
     */
    public function RemoveCarAction(Request $request, Car $car = null)
    {
        $user = $this->getUser();
        if ($car != null && $user === $car->getOwner()){
            $car->setIsActive(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($car);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add(
                'successful',
                'Your car was removed successfully!'
            );
            return $this->redirectToRoute('cars');
        }else{
            return $this->redirectToRoute('homepage');
        }
    }
}
