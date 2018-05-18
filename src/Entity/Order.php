<?php

namespace App\Entity;

use App\Entity\Car;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="order_date", type="datetime")
     * @Assert\NotBlank()
     */
    private $orderDate;

    /**
     * @ORM\Column(name="order_end_date", type="datetime", nullable=true)
     */
    private $orderEndDate;

    /**
     * @ORM\Column(name="completed", type="boolean")
     * @Assert\Type("bool")
     */
    private $completed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Car", inversedBy="orders")
     * @ORM\JoinColumn(name="car_id", referencedColumnName="id", nullable=false)
     */
    private $car;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedService", mappedBy="order")
     */
    private $services;

    public function __construct()
    {
        $this->completed = false;
        $this->services = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
  
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    }
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function setOrderEndDate($orderEndDate)
    {
        $this->orderEndDate = $orderEndDate;
    }
    public function getOrderEndDate()
    {
        return $this->orderEndDate;
    }

    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }
    public function getCompleted()
    {
        return $this->completed;
    }

    public function setCar(Car $car)
    {
        $this->car = $car;
    }
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @return Collection|OrderedService[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(OrderedService $service){
        $this->services->add($service);
    }
}
