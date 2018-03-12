<?php
/**
 * Created by PhpStorm.
 * User: paulius
 * Date: 3/12/18
 * Time: 7:39 PM
 */

namespace Models;

class User
{
    private $email;
    private $password;

    /**
     * User constructor.
     * @param $email User's email
     * @param $password Password that is hashed using default hash method
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string Hashed password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string User's email
     */
    public function getEmail()
    {
        return $this->email;
    }
}