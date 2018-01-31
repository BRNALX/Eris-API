<?php
class Database{
 
    // specify your own database credentials
    private $host = "200.98.112.72";
    private $db_name = "ERIS";
    private $username = "root";
    private $password = "Eris@2018#";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>