<?php

    Class Database{

        private $hostname; 
        private $username; 
        private $password; 
        private $database; 
        private $conn;

        // DB connection requirments
        public function __construct($hostname, $username, $password, $database){
            $this->hostname = $hostname;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;

            $this->connect();
        }

        // creates a PDO connection to sql DB
        private function connect(){
            try{
                $this->conn = new PDO(
                    "mysql:host={$this->hostname};dbname={$this->database}",
                    $this->username,
                    $this->password
                );

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch(PDOException $e){
                error_log("Connection Failed: " . $e->getMessage());
                die("Connection Failed: Please try again later"); // error not shown to user
            }
        }

        // allows connection to be used
        public function getConn(){
            return $this->conn;
        }

        public function closeConn(){
            $this->conn = null;
        }
    }

?>