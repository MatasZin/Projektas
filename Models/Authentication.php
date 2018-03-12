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
    private $users;
    public function __construct(UsersContainer $usersContainer)
    {
        $this->users = $usersContainer;
    }

    public function check($email, $password)
    {
        if (!empty($_SESSION["email"])) {
            return true;
        }
        $user = $this->users->getUser($email);
        if ($user != null) {
            if(password_verify($password, $user->getPassword())) {
                $this->login($user);
                return $user->getPassword() === (string)$password;
            }
        }
        return false;
    }
    public function logout()
    {
        $_SESSION["email"] = null;
    }
    private function login(User $member)
    {
        $_SESSION["email"] = $member->getEmail();
    }
}