<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbName = "database";

    private $connection;

    public function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbName);

        if ($this->connection->connect_error) {
            throw new Exception("Failed to connect to the database: " . $this->connection->connect_error);
        }

        return $this->connection;
    }
}
?>
