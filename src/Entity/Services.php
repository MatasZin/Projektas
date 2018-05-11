<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServicesRepository")
 * @ORM\Table(name="services")
 */
class Services
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="text", length=100)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="price", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedService", mappedBy="service")
     */
    private $assignedOrders;

    public function __construct()
    {
        $this->assignedOrders = new ArrayCollection();
    }

    // Getteres & Setters

    /**
     * @return Collection|OrderedService[]
     */
    public function getAssignedOrders(): Collection
    {
        return $this->assignedOrders;
    }

    public function addAssignedOrder(OrderedService $assignedOrder)
    {
        $this->assignedOrders->add($assignedOrder);
    }

    public function getId(){return $this->id;}

    public function getTitle(){return $this->title;}
    public function setTitle($title){$this->title = $title;}

    public function getPrice(){return $this->price;}
    public function setPrice($price){$this->price = $price;}

    public function getDescription(){return $this->description;}
    public function setDescription($description){$this->description = $description;}

}
