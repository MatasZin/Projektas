<?php
/**
 * Created by PhpStorm.
 * User: matas
 * Date: 3/12/18
 * Time: 10:58 PM
 */

namespace Models;
use Models\User;
use Models\UsersContainer;

class Authentication
{
    private $data;
    public function __construct()
    {
        $this->data = new \db();
    }

    public function check($email, $password)
    {
        if (!empty($_SESSION["email"])) {
            return true;
        }
        $query = "SELECT email, password FROM users WHERE email = '$email'";
        $result = $this->data->get_result($query);
        if ($result != null) {
            if(password_verify($password, $result['password'])) {
                $this->login($result['email']);
                return true;
            }
        }
        return false;
    }
    public function logout()
    {
        $_SESSION["email"] = null;
    }
    private function login($email)
    {
        $_SESSION["email"] = $email;
    }
}