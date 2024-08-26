<?php

require_once '../models/Admin.php';
require_once '../models/User.php';

class AuthMiddleware
{
    private $adminModel;
    private $userModel;

    public function __construct($db)
    {
        $this->adminModel = new Admin($db);
        $this->userModel = new User($db);
    }

    public function checkToken(string $token): array
    {
        // Ensure the token is provided
        if (empty($token)) {
            return [
                "status" => false,
                "message" => "Token not provided"
            ];
        }

        // Check if the token is valid for admin
        if ($this->adminModel->isTokenValid($token)) {
            return [
                "status" => true,
                "role" => "admin"
            ];
        }

        // Check if the token is valid for user
        if ($this->userModel->isTokenValid($token)) {
            return [
                "status" => true,
                "role" => "user"
            ];
        }

        // Token is invalid
        return [
            "status" => false,
            "message" => "Invalid Token"
        ];
    }
}

?>
