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
     *
     * @Assert\Length(
     *      min = 4,
     *      max = 6,
     *      minMessage = "License plate number must be at least {{ limit }} characters long",
     *      maxMessage = "License plate number cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     "/^[A-Z0-9]+$/",
     *     message="Incorrect format of license plate. For example: CNA534, JEY210..."
     * )
     */
    private $licensePlate;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     * @Assert\Type("bool")
     */
    private $isActive;

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
        $this->isActive = true;
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
    public function setIsActive($isActive){
        $this->isActive = $isActive;
    }
    public function getIsActive()
    {
        return $this->isActive;
    }

}
