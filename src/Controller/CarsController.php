<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
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
        $car = new Car();
        $form = $this->createFormBuilder($car)
            ->add('licensePlate', TextType::class, array(
                'attr' => array('class' => 'simple-input')
            ))
            ->add('AddCar', SubmitType::class, array(
                'label' => 'Add car',
                'attr' => array('class' => 'modern')
            ))
            ->getForm();
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
        ]);
    }
}
