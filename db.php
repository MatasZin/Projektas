<?php
/**
 * Created by PhpStorm.
 * User: mantas
 * Date: 3/17/18
 * Time: 11:19 PM
 */

require_once('configs/config.php');
class db extends mysqli
{
    private static $instance = null;


    private $host = DB_HOST;
    private $user = DB_USERNAME;
    private $pass = DB_PASSWORD;
    private $dbname = DB_DATABASE;

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    public function __construct()
    {
        parent::__construct($this->host, $this->user, $this->pass, $this->dbname);

        if (mysqli_connect_error()){
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
        parent::set_charset('utf-8');
    }

    public function dbquery($query)
    {
        if($this->query($query))
        {
            return true;
        }else{
            echo "ERROR: Could not able to execute  " . mysqli_error($this);
            return false;
        }

    }
    public function get_result($query)
    {
        $result = $this->query($query);
        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row;
        } else
            return null;
    }
}
?>