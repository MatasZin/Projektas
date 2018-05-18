<?php

namespace App\Controller;

use App\Entity\OrderedService;
use App\Entity\Services;
use App\Form\ServiceType;
use App\Entity\User;
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
        $services = $this->getDoctrine()->getRepository(Services::class)->findBy(array(
            'isActive' => true,
        ));
        return $this->render('Services/index.html.twig', array ('services' => $services));
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
        $service=$this->getDoctrine()->getRepository(Services::class)->findOneBy(array(
            'id' => $id,
            'isActive' => true,
        ));
        if ($service !== null){
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
        return $this->redirectToRoute('Services');

    }

    /**
     * @Route("/Services/remove{id}", name="remove_service")
     * @Method({"GET"})
     */
    public function remove($id) {
        $service = $this->getDoctrine()->getRepository(Services::class)->findOneBy(array(
            'id' => $id,
            'isActive' => true,
        ));
        if ($service !== null){
            $entityManager = $this->getDoctrine()->getManager();
            $service->setIsActive(false);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add(
                'successful',
                'Service was removed successfully!'
            );
            return $this->redirectToRoute('Services');
        }
        return $this->redirectToRoute('Services');
    }

    /**
     * @Route("/Services/{id}", name="service_show")
     */
    public function show($id)
    {
        $service = $this->getDoctrine()->getRepository(Services::class)->findOneBy(array(
            'id' => $id,
            'isActive' => true,
        ));
        if ($service !== null){
            return $this->render('Services/show.html.twig', array ('service' => $service));
        }
        return $this->redirectToRoute('Services');
    }

    /**
     * @Route("/admin/orders/{id}", name="admin_services")
     */
    public function admin_services(Request $request, $id) {

        $count = $this->getDoctrine()->getRepository(OrderedService::class)->getServicesCount($id);

        for ($i = 1; $i <= $count; $i++) {
            if($request->get('status_'.$i)) {
                $this->getDoctrine()->getRepository(OrderedService::class)->find($i)->setStatus($request->get('status_'.$i));
            }
            if($request->get('worker_'.$i)){
                $worker = $this->getDoctrine()->getRepository(User::class)->find($request->get('worker_'.$i));
                $this->getDoctrine()->getRepository(OrderedService::class)->find($i)->setWorker($worker);
            }
        }
        $this->getDoctrine()->getManager()->flush();

        $services = $this->getDoctrine()->getRepository(OrderedService::class)->findBy(array('order'=>$id));
        $workers = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_WORKER'));
        return $this->render('admin/services/index.html.twig', array('workers' => $workers, 'message' => "List of services:", 'services' => $services));
    }
}