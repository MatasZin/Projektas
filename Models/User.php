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
    private $name;
    private $secondName;

    /**
     * User constructor.
     * @param $email User's email
     * @param $password Password that is hashed using default hash method
     */
    public function __construct($email, $password, $name, $secondName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->secondName = $secondName;
        $this->addToBase();
    }

    /**
     * @return string Hashed password
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function addToBase()
    {
        $data = new \db();
        $query = "INSERT INTO users (email, password, first_name, second_name) 
                  VALUES ('$this->email', '$this->password', '$this->name', '$this->secondName')";

        $sock = $data->dbquery($query);
        var_dump($sock);
    }

    /**
     * @return string User's email
     */
    public function getEmail()
    {
        return $this->email;
    }
}