<?php
class Admin {
    private $conn;
    private $table_name = "admin";

    public $username;
    public $password;
    public $token;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Check if the provided username and password are correct
    public function checkCredentials() {
        // Hash the password before comparison
        $hashed_password =  base64_encode($this->password);

        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("ss", $this->username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        $isValid = $result->num_rows > 0; // Returns true if data found
        $stmt->close();
        return $isValid;
    }

    // Generate and update the token for the admin
    public function updateToken() {
        $this->token = bin2hex(random_bytes(16)); // Generate a random token
        $query = "UPDATE " . $this->table_name . " SET token = ? WHERE username = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false; // Prepare failed
        }

        $stmt->bind_param("ss", $this->token, $this->username);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Check if the provided token is valid
    public function isTokenValid($token) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE token = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false; // Prepare failed
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        $isValid = $result->num_rows > 0;
        $stmt->close();
        return $isValid;
    }
}
?>