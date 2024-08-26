<?php
require_once '../models/UserProduct.php';

class UserProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new UserProduct($db); // Initialize UserProduct model
    }

    // Create a new product
    public function createProduct($product_name, $description, $category, $price, $user_id) {
        $this->productModel->product_name = $product_name;
        $this->productModel->description = $description;
        $this->productModel->category = $category;
        $this->productModel->price = $price;
        $this->productModel->user_id = $user_id;

        if ($this->productModel->createProduct()) {
            return ["message" => "Product created successfully"];
        } else {
            return ["message" => "Failed to create product"];
        }
    }

    // Get all products for a user
    public function getAllProducts($user_id) {
        return $this->productModel->getAllProducts($user_id);
    }

    // Get a product by its ID for a user
    public function getProductById($product_id, $user_id) {
        return $this->productModel->getProductById($product_id, $user_id);
    }

    // Update a product by its ID
    public function updateProduct($product_id, $product_name, $description, $category, $price, $user_id) {
        if ($this->productModel->updateProduct($product_id, $product_name, $description, $category, $price, $user_id)) {
            return ["message" => "Product updated successfully"];
        } else {
            return ["message" => "Failed to update product"];
        }
    }

    // Delete a product by its ID
    public function deleteProduct($product_id, $user_id) {
        if ($this->productModel->deleteProduct($product_id, $user_id)) {
            return ["message" => "Product deleted successfully"];
        } else {
            return ["message" => "Failed to delete product"];
        }
    }

    // Get products by category for a user
    public function getProductsByCategory($category, $user_id = null) {
        return $this->productModel->getProductsByCategory($category, $user_id);
    }
}
?>