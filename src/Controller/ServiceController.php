<?php

namespace App\Controller;

use App\Criteria\JobFilter;
use App\Entity\Order;
use App\Entity\OrderedService;
use App\Entity\Services;
use App\Form\JobOptionsType;
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
     * @Route("/admin/orders/{order_id}", name="admin_jobs")
     */
    public function admin_services(Request $request, $order_id) {
        $orderedServices = $this->getDoctrine()->getRepository(OrderedService::class)->findBy(array('order'=>$order_id));
        $workers = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_WORKER'));

        $this->jobs_editing($request, $orderedServices);

        return $this->render('jobs/show_edit.html.twig', array('workers' => $workers, 'title' => "List of all jobs:", 'services' => $orderedServices, 'form' => null));
    }

    /**
     * @Route("/jobs", name="jobs")
     */
    public function worker_services(Request $request) {

        $form = $this->createForm(JobOptionsType::class);
        $form->handleRequest($request);
        $filter = new JobFilter($form);

        $orderedServices = array();
        if($this->isGranted("ROLE_WORKER"))
            $orderedServices = $this->getDoctrine()->getRepository(OrderedService::class)->findByJobFilter($filter, $this->getUser());

        $workers = null;
        if($this->isGranted("ROLE_ADMIN")) {
            $orderedServices = $this->getDoctrine()->getRepository(OrderedService::class)->findByJobFilter($filter, null);
            $workers = $this->getDoctrine()->getRepository(User::class)->findBy(array('role'=>'ROLE_WORKER'));
        }

        $this->jobs_editing($request, $orderedServices);

        return $this->render('jobs/show_edit.html.twig', array('title' => "List of jobs", 'services' => $orderedServices, 'workers' => $workers, 'form' =>$form->createView()));
    }

    private function jobs_editing(Request $request, $orderedServices) {
        $count = count($orderedServices);

        $isChangedOnce = false;
        for ($i = 0; $i < $count; $i++) {
            $id = $orderedServices[$i]->getId();
            $isChanged = false;
            if($request->get('status_'.$id) !== $orderedServices[$i]->getStatus() && $request->get('status_'.$id) !== null) {
                $this->getDoctrine()->getRepository(OrderedService::class)->find($id)->setStatus($request->get('status_'.$id));
                $isChanged = true;
                $isChangedOnce = true;
                if($request->get('status_'.$id) === 'Done!') {
                    $isOrderCompleted = true;
                    foreach ($orderedServices[$i]->getOrder()->getServices() as $job) {
                        if($job->getStatus() !== 'Done!') {
                            $isOrderCompleted = false;
                            break;
                        }
                    }

                    if($isOrderCompleted) {
                        $order = $this->getDoctrine()->getRepository(Order::class)->find($orderedServices[$i]->getOrder()->getId());
                        $order->setCompleted(1);
                        $order->setOrderEndDate(new \DateTime());
                    }
                }
            }
            if($request->get('note_'.$id) !== $orderedServices[$i]->getNote() && $request->get('note_'.$id) !== null) {
                $this->getDoctrine()->getRepository(OrderedService::class)->find($id)->setNote($request->get('note_'.$id));
                $isChanged = true;
                $isChangedOnce = true;
            }
            if($this->isGranted("ROLE_ADMIN") && $request->get('worker_'.$id) !== null && $request->get('worker_'.$id) != -1){
                if($orderedServices[$i]->getWorker() === null) {
                    $worker = $this->getDoctrine()->getRepository(User::class)->find($request->get('worker_'.$id));
                    $this->getDoctrine()->getRepository(OrderedService::class)->find($id)->setWorker($worker);
                    $isChanged = true;
                    $isChangedOnce = true;
                }
                elseif ($request->get('worker_'.$id) != $orderedServices[$i]->getWorker()->getId()) {
                    $worker = $this->getDoctrine()->getRepository(User::class)->find($request->get('worker_'.$id));
                    $this->getDoctrine()->getRepository(OrderedService::class)->find($id)->setWorker($worker);
                    $isChanged = true;
                    $isChangedOnce = true;
                }
            }
            if($isChanged) {
                $this->getDoctrine()->getRepository(OrderedService::class)->find($id)->setLastChangeDate();
            }
        }
        if($isChangedOnce) {
            $this->getDoctrine()->getManager()->flush();
        }
    }
}