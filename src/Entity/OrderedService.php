<?php

namespace App\Entity;

use App\Form\OrderType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderedServiceRepository")
 * @ORM\Table(name="ordered_services")
 */
class OrderedService
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status = "Waiting";

    /**
     * @ORM\Column(name="last_change_date", type="datetime")
     */
    private $lastChangeDate;

    /**
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="ordered_services")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ordered_services")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="id", nullable=true)
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Services", inversedBy="ordered_services")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=false)
     */
    private $service;

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getLastChangeDate()
    {
        return $this->lastChangeDate;
    }
    public function setLastChangeDate()
    {
        $this->lastChangeDate = new \DateTime();
    }

    public function getNote()
    {
        return $this->note;
    }
    public function setNote($note)
    {
        $this->note = $note;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
    }
    public function getOrder()
    {
        return $this->order;
    }

    public function setWorker(User $worker = null)
    {
        $this->worker = $worker;
    }
    public function getWorker()
    {
        return $this->worker;
    }

    public function setService(Services $services)
    {
        $this->service = $services;
    }
    public function getService()
    {
        return $this->service;
    }

}
