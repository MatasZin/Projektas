<style>
    <?php include 'style.css';?>
</style>
<form action="register.php" method="post">
    <p> <label for="name">Name:</label>
        <input id="name" class="simple-input" type="text" name="name" />
    <p> <label for="surname">Surname:</label>
        <input id="surname" class="simple-input" type="text" name="surname" />
    <p> <label for="email">E-mail:</label>
        <input id="email" class="simple-input" type="text" name="email" />
    <p> <label for="password">Password:</label>
        <input id="password" class="simple-input" type="password" name="password" />
    <p> <label for="password2">Repeat password:</label>
        <input id="password2" class="simple-input" type="password" name="password2" />
    <p><input type="submit" class="modern" value="Sign up"  name="signup"/>
        <input type="button" class="modern" value="Log in" onclick="location.href='index.php';" /></p>
</form>
<?php
require 'db.php';
$data = new \db();
if (isset($_POST['signup'])){
    if($_POST["name"] != "" && $_POST["surname"] != "" && $_POST["email"] != "" && $_POST["password"] != "" ) {
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        if($_POST["password"] === $_POST["password2"]){
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $query = "SELECT email FROM users WHERE email = '$email'";
            $result = $data->get_result($query);
            if($result["email"] == null){
                $sql = "INSERT INTO users(email, password, first_name, second_name) VALUES ('$email', '$password', '$name','$surname')";
                $data->dbquery($sql);
                header('Location: index.php');
            }
            else{
                echo " user with your email already exists";
            }
        }
        else {
            echo "<br>passwords don't match";
        }
    }
    else{
        echo "Fill up all tables";
    }
}

?>