<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"id", "email"})
 */
class User
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
     * @ORM\Column(name="name", type="string", length=25)
     */
    private $name;

    /**
     * @ORM\Column(name="second_name", type="string", length=25)
     */
    private $second_name;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     * @Assert\Type(type = "numeric")
     *
     */
    private $access;

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
    public function getAccess(){
        return $this->access;
    }

    public function setAccess($access){
        $this->access = $access;
    }
}
