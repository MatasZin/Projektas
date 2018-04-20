<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     */
    private $title;

    /**
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedService", mappedBy="services")
     */
    private $assignedOrders;

    public function __construct()
    {
        $this->assignedOrders = new ArrayCollection();
    }

    // Getteres & Setters

    public function getId(){return $this->id;}

    public function getTitle(){return $this->title;}
    public function setTitle($title){$this->title = $title;}

    public function getPrice(){return $this->price;}
    public function setPrice($price){$this->price = $price;}

    public function getDescription(){return $this->description;}
    public function setDescription($description){$this->description = $description;}

}
