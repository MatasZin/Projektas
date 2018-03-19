<?php
/**
 * Created by PhpStorm.
 * User: paulius
 * Date: 3/12/18
 * Time: 7:48 PM
 */

namespace Models;

class UsersContainer
{
    private $users;

    /**
     * @param $email User's email
     * @param $password User's password
     */
    public function addUser($email, $password, $name, $secondName)
    {
        $this->users[] = new User($email, password_hash($password, PASSWORD_DEFAULT), $name, $secondName);
    }

    /**
     * @param $email User's email
     * @return null if user is not found / User if user is found
     */
    public function getUser($email)
    {
        foreach ($this->users as $user)
        {
            if($user->getEmail() === $email)
            {
                return $user;
            }
        }
        return null;
    }
}