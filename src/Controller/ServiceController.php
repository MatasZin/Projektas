<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ServiceController extends Controller {
    /**
     * @Route("/Services", name="Services")
     * @Method({"GET", "POST"})
     */
    public function index()
    {
        $services = $this->getDoctrine()->getRepository(Services::class)->findAll();
        return $this->render('Services/index.html.twig', array ('services' =>$services));
    }

    /**
     * @Route("/Services/new", name="new_service")
     * Method({"GET", "POST"})
     */
    public function new(Request $request){
        $service = new Services();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $service = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('Services');
        }

        return $this->render('Services/new.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Route("/Services/edit/{id}", name="edit_service")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id){
        $service=$this->getDoctrine()->getRepository(Services::class)->find($id);
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('Services');
        }

        return $this->render('Services/edit.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Route("/Services/remove{id}", name="remove_service")
     * @Method({"GET"})
     */
    public function remove($id) {
        $service = $this->getDoctrine()->getRepository(Services::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $this->index();
    }

    /**
     * @Route("/Services/{id}", name="service_show")
     */
    public function show($id)
    {
        $service=$this->getDoctrine()->getRepository(Services::class)->find($id);
        return $this->render('Services/show.html.twig', array ('service' => $service));
    }
}