<style>
    <?php include 'style.css';?>
</style>
<?php
/**
 * Created by PhpStorm.
 * User: mantas
 * Date: 3/13/18
 * Time: 12:30 AM
 */
session_start();

require 'db.php';
include "Models/Authentication.php";
//include "Models/User.php";
//include "Models/UsersContainer.php";

use Models\Authentication;

$auth = new Authentication();

if(isset($_POST['logout'])){
    $auth->logout();
}

if (isset($_POST['login'])){
    $email = $_POST["email"];
    $password = $_POST["pass"];
    if (!$auth->check($email, $password)){
        echo "Incorrect email address or password.";
    }
}

if($_SESSION["email"] != null){
    echo "Success! You are at the university area as {$_SESSION["email"]}";
    include("logout.php");
    include('feedback.php');
}else{
    include("login.php");
}
