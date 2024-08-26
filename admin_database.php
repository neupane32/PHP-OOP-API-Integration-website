<?php
include_once '../project_php/config/database.php';

class Admin {
    private $dbConnection;

    public function __construct($connection) {
        $this->dbConnection = $connection;
    }

    public function createAdmin($username, $password, $role, $token = "") {
        $sql = "INSERT INTO admin (username, password, role, token) VALUES (?, ?, ?, ?)";
        $statement = $this->dbConnection->prepare($sql);
        
        if (!$statement) {
            throw new Exception("Statement preparation failed: " . $this->dbConnection->error);
        }
        
        $encodedPassword = base64_encode($password);
    
        $statement->bind_param("ssss", $username, $encodedPassword, $role, $token);
    
        try {
            if (!$statement->execute()) {
                throw new Exception("Statement execution failed: " . $statement->error);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                throw new Exception("Username already exists.");
            } else {
                throw $e;
            }
        }
        
        $statement->close();
    }
}

// Example usage
$databaseInstance = new Database();
$adminInstance = new Admin($databaseInstance->Connect());

// Insert a new admin with username "admin", password "admin", role "admin", and no token
$adminInstance->createAdmin("admin", "admin", "admin");
?>
