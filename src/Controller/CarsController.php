<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\CarType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarsController extends Controller
{
    /**
     * @Route("/cars", name="cars")
     * @Method({"GET", "POST"})
     */
    public function RegisterCarAction(Request $request)
    {
        $user = $this->getUser();
        $cars = $this->getDoctrine()->getRepository(Car::class)
            ->findBy(array(
                'owner' => $user,
            ));
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form
            ->add('AddCar', SubmitType::class, array(
                'label' => 'Add car',
                'attr' => array('class' => 'modern')
            ));
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()){
            $car = $form->getData();
            $car->setOwner($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($car);
            $entityManager->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('cars/index.html.twig', [
            'form' => $form->createView(),
            'cars' => $cars,
        ]);
    }
}
