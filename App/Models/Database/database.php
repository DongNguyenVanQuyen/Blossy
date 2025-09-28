<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "0586964157";
    private $databasename = "web_ban_hoa";
    protected $conn = null;

    function connection_database(): PDO {
        try {
            $conn = new PDO(
                "mysql:host=$this->servername;dbname=$this->databasename",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw $e;
        }

        return $conn;
    }
}

?>