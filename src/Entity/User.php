<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"id", "email"}, message="This email is already taken.")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", length=254, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(name="name", type="string", length=25, nullable=false)
     * @Assert\Regex(
     *     "/^[a-Z]+$/",
     *     message="Incorrect format of first name."
     * )
     */
    private $name;

    /**
     * @ORM\Column(name="second_name", type="string", length=25, nullable=true)
     * @Assert\Regex(
     *     "/^[a-Z]+$/",
     *     message="Incorrect format of second name."
     * )
     */
    private $second_name;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Car", mappedBy="user")
     */
    private $cars;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedService", mappedBy="user")
     */
    private $assignedServices;

    public function __construct()
    {
        $this->isActive = true;
        $this->cars = new ArrayCollection();
        $this->assignedServices = new ArrayCollection();
    }
    /**
     * @ORM\Column(name="role", type="string", length=25)
     */
    private $role;

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
        return true;
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
