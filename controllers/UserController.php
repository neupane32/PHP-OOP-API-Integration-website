<?php
require_once '../models/User.php';
require_once '../models/Admin.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db); // Initialize User model
    }

    // Authenticate user and return token if successful
    public function authenticate($username, $password) {
        $this->userModel->username = $username;
        $this->userModel->password = $password;

        if ($this->userModel->checkCredentials()) {
            $token = $this->userModel->generateToken();
            if ($token) {
                return [
                    "message" => "Correct password",
                    "token" => $token
                ];
            }
        }

        return [
            "message" => "Incorrect password"
        ];
    }

    // Retrieve user details by token
    public function getUserByToken($token) {
        $this->userModel->token = $token;
        return $this->userModel->getUserByToken();
    }
}
?>