<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarRepository")
 * @ORM\Table(name="cars")
 */
class Car
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="license_plate",type="string", length=10, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     "/^[A-Z 0-9]+$/",
     *     message="Incorrect format of license plate. For example: CNA 534, JEY210..."
     * )
     */
    private $licensePlate;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cars")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="car")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order)
    {
        $this->orders->add($order);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLicensePlate()
    {
        return $this->licensePlate;
    }

    public function setLicensePlate($licensePlate)
    {
        $this->licensePlate = $licensePlate;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }
    public function getOwner()
    {
        return $this->owner;
    }

}
