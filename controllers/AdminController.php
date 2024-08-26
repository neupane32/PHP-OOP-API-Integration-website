<?php
require_once '../models/Admin.php';
require_once '../models/User.php';
require_once '../models/UserProduct.php'; // Assuming you have a Product model

class AdminController {
    private $adminModel;
    private $userModel;
    private $productModel;

    public function __construct($db) {
        $this->adminModel = new Admin($db); // Admin model object
        $this->userModel = new User($db);
        $this->productModel = new UserProduct($db);
    }

    // Authenticate admin and return token if successful
    public function authenticate($username, $password) {
        $this->adminModel->username = $username;
        $this->adminModel->password = $password;

        // Check credentials against the database
        if ($this->adminModel->checkCredentials()) {
            $this->adminModel->updateToken(); // Update the token in the database
            return [
                "message" => "Correct password",
                "token" => $this->adminModel->token
            ];
        } else {
            return [
                "message" => "Incorrect password"
            ];
        }
    }

    // Create a new user if admin token is valid
    public function createUser($username, $password) {
        $this->userModel->username = $username;
        $this->userModel->password = $password; // No need to encode as base64 for user password
        return $this->userModel->createUser();
    }

    // Delete a user if admin token is valid
    public function deleteUser($userId) {
        return $this->userModel->deleteUser($userId);
    }

    // Get products by ID or all products if ID is not provided
    public function getProducts($productId = null) {
        return $this->productModel->getProducts($productId);
    }

    // Get products by category if admin token is valid
    public function getCategory($category = null) {
        return $this->productModel->getCategory($category);
    }

    // Delete a category if admin token is valid
    public function deleteCategory($category = null) {
        return $this->productModel->deleteCategory($category);
    }
}
?>