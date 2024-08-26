<?php
require_once '../config/database.php';
require_once '../models/EnumProductCategory.php';

class UserProduct {
    private $conn;
    private $table_name = "products";

    public $product_id;
    public $product_name;
    public $description;
    public $category;
    public $price;
    public $user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Insert a new product into the database
    public function createProduct() {
        if (!ProductCategory::isValidCategory($this->category)) {
            return false; // Invalid category
        }

        $query = "INSERT INTO " . $this->table_name . " (product_name, description, category, price, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdi", $this->product_name, $this->description, $this->category, $this->price, $this->user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get all products for a specific user
    public function getAllProducts($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get a specific product by its ID and user ID
    public function getProductById($product_id, $user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $product_id, $user_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    // Update an existing product based on product ID and user ID
    public function updateProduct($product_id, $product_name, $description, $category, $price, $user_id) {
        if (!ProductCategory::isValidCategory($category)) {
            return false; // Invalid category
        }

        $query = "UPDATE " . $this->table_name . " SET product_name = ?, description = ?, category = ?, price = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdii", $product_name, $description, $category, $price, $product_id, $user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete a product based on product ID and user ID
    public function deleteProduct($product_id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $product_id, $user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get products with optional specific product ID
    public function getProducts($productId = null) {
        if ($productId) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $productId);
        } else {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategory($category=null) {
        if ($category) {
            // Get a products of specific category
            $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $category);
        } else {
            // Get all products
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    // Get products by category with optional user ID
    public function getProductsByCategory($category, $userId = null) {
        if (!ProductCategory::isValidCategory($category)) {
            return []; // Invalid category
        }

        if ($userId) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE category = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $category, $userId);
        } else {
            $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $category);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Delete products of a specific category
    public function deleteCategory($category = null) {
        $query = "DELETE FROM " . $this->table_name . " WHERE category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category);
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>