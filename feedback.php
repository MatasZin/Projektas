<form method="post">
    <label for="comment">Enter your feedback:</label>
    <input id="comment" class="simple-input" type="text" name="comment" />
    <input class="modern" type="submit" value="Send!" />
</form>

<?php
/**
 * Created by PhpStorm.
 * User: paulius
 * Date: 3/19/18
 * Time: 2:48 PM
 */

//require "db.php"; //Uncomment this if want to use this file as a standalone php script

$db = new \db();
$n = $db->get_result("SELECT COUNT(id) FROM comments")["COUNT(id)"];
if($n > 0) {
    echo "Current feedback:";
    echo '<table>';
    echo '<tr><th>First name</th><th>Last name</th><th>Feedback</th></tr>';
    for ($i = 0; $i < $n; $i++) {
        $temp = $db->get_result("SELECT users.first_name AS name, users.second_name AS surname, comments.comment 
                                    FROM comments
                                    INNER JOIN users ON comments.fk_userid = users.id
                                    WHERE comments.id = '$i'");
        if ($temp === null) {
            $n++;
        } else {
            //$feedback[] = $temp;
            echo '<tr>';
            echo '<td>' . $temp["name"] . '</td>';
            echo '<td>' . $temp["surname"] . '</td>';
            echo '<td>' . $temp["comment"] . '</td>';
            echo '</tr>';
        }
    }

    if ($_POST['comment']) {
        $db->dbquery("INSERT INTO comments (fk_userid, comment) VALUES ('$_SESSION[id]', '$_POST[comment]')");
        $temp = $db->get_result("SELECT first_name AS name, second_name AS surname FROM users WHERE users.id = '$_SESSION[id]'");
        echo '<td>' . $temp["name"] . '</td>';
        echo '<td>' . $temp["surname"] . '</td>';
        echo '<td>' . $_POST['comment'] . '</td>';
        echo '</table><p></p>';
        echo "Thank you for your feedback!";
    }
}
else{
    echo "There is no feedback yet... Be first!!";
    if ($_POST['comment']) {
        echo '<table>';
        echo '<tr><th>First name</th><th>Last name</th><th>Feedback</th></tr>';
        $db->dbquery("INSERT INTO comments (fk_userid, comment) VALUES ('$_SESSION[id]', '$_POST[comment]')");
        $temp = $db->get_result("SELECT first_name AS name, second_name AS surname FROM users WHERE users.id = '$_SESSION[id]'");
        echo '<td>' . $temp["name"] . '</td>';
        echo '<td>' . $temp["surname"] . '</td>';
        echo '<td>' . $_POST['comment'] . '</td>';
        echo '</table><p></p>';
        echo "Thank you for your feedback (and being first <^.^> )!";
    }
}
?>