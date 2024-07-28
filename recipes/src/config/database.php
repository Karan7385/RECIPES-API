<?php

require_once 'config/config.php';

class Connections extends DB_config{
    protected  $host ;
    protected  $dbname ;
    protected  $username ;
    protected  $password ;

    public function __construct(){
        $this -> host =  $this -> database[$this -> active_database]["dbname"];
        $this -> dbname = $this -> database[$this -> active_database]["host"];
        $this -> username = $this -> database[$this -> active_database]["username"];
        $this -> password = $this -> database[$this -> active_database]["password"];
        
        
    }

    private $mysqli = null;

    /**
     * Connect to the MySQL database using MySQLi.
     *
     * @return mysqli|null Returns the MySQLi instance on success or null on failure.
     */

    public function connect(): ?mysqli {
        
        if ($this -> mysqli === null) {
            $this -> mysqli = new mysqli($this -> database[$this -> active_database]["host"], $this -> database[$this -> active_database]["username"], $this -> database[$this -> active_database]["password"], $this -> database[$this -> active_database]["dbname"]);

            if ($this -> mysqli -> connect_error) {
                error_log("Connection failed: " . $this -> mysqli -> connect_error);
                $this -> mysqli = null;
            } else {
                if (!$this -> mysqli -> set_charset("utf8mb4")) {
                    error_log("Error loading character set utf8mb4: " . $this -> mysqli -> error);
                }
            }
        }

        return $this->mysqli;
    }

    public function close(): void {
        if ($this -> mysqli !== null) {
            $this -> mysqli -> close();
            $this -> mysqli = null;
        }
    }
}

class Database {
    public $connection;
    public $con;
    public function __construct(){
        
        $this->connection = new Connections();
        
        $mysqli = $this -> connection -> connect();
        $this -> con = $mysqli;
    }
}


