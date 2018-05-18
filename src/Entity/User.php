<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="This email is already taken.")
 * @UniqueEntity(fields={"id"}, message="The generated id are already exist, please report it to admin!")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", length=254, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(name="name", type="string", length=25, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 25,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     "/^[a-zA-Z]+$/",
     *     message="Incorrect format of first name."
     * )
     */
    private $name;

    /**
     * @ORM\Column(name="second_name", type="string", length=25, nullable=true)
     * @Assert\Length(
     *      min = 3,
     *      max = 25,
     *      minMessage = "Your second name must be at least {{ limit }} characters long",
     *      maxMessage = "Your second name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     "/^[a-zA-Z]+$/",
     *     message="Incorrect format of second name."
     * )
     */
    private $second_name;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Assert\Type("bool")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_deleted", type="boolean")
     * @Assert\Type("bool")
     */
    private $isDeleted;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     * @Assert\Type("string")
     */
    private $confirmationToken;

    /**
     * @ORM\Column(name="forget_token", type="string", length=255, nullable=true)
     * @Assert\Type("string")
     */
    private $forgetPasswordToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Car", mappedBy="owner")
     */
    private $cars;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedService", mappedBy="worker")
     */
    private $assignedServices;

    /**
     * @ORM\Column(name="role", type="string", length=25)
     * @Assert\NotBlank()
     */
    private $role;

    public function __construct()
    {
        $this->isActive = false;
        $this->isDeleted = false;
        $this->role = 'ROLE_USER';
        $this->cars = new ArrayCollection();
        $this->assignedServices = new ArrayCollection();
    }

    /**
     * @return Collection|Car[]
     */
    public function getCars(): Collection{
        return $this->cars;
    }

    public function addCar(Car $car){
        $this->cars->add($car);
    }

    /**
     * @return Collection|OrderedService[]
     */
    public function getAssignedServices(): Collection{
        return $this->assignedServices;
    }

    public function addAssignedService(OrderedService $assignedService){
        $this->assignedServices->add($assignedService);
    }

    public function getId(){
        return $this->id;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $this->password = $password;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getSecondName(){
        return $this->second_name;
    }

    public function setSecondName($second_name){
        $this->second_name = $second_name;
    }

    public function setConfirmationToken($confirmationToken){
        $this->confirmationToken = $confirmationToken;
    }

    public function setForgetPasswordToken($forgetPasswordToken){
        $this->forgetPasswordToken = $forgetPasswordToken;
    }

    public function getIsDeleted(){
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted){
        $this->isDeleted = $isDeleted;
    }

    public function setIsActive($isActive){
        $this->isActive = $isActive;
    }

    public function setRole($role){
        $this->role = $role;
    }

    public function getRole(){
        return $this->role;
    }

    public function getRoles()
    {
        return [$this->getRole()];
    }

    public function getSalt()
    {
        return null;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return (!$this->isDeleted);
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->isActive,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->isActive,
            ) = unserialize($serialized);
    }

}
