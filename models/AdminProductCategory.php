<?php
require_once '../config/database.php';
require_once '../models/EnumProductCategory.php';

class Product {
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

    public function createProduct() {
        // Check if category is valid
        if (!ProductCategory::isValidCategory($this->category)) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        $query = "INSERT INTO " . $this->table_name . " (product_name, description, category, price, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdi", $this->product_name, $this->description, $this->category, $this->price, $this->user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product created successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to execute statement'];
        }
    }

    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return ['success' => true, 'data' => $products];
    }

    public function getProductById($product_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        } else {
            return ['success' => false, 'message' => 'Product not found'];
        }
    }

    public function updateProduct() {
        // Check if category is valid
        if (!ProductCategory::isValidCategory($this->category)) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        $query = "UPDATE " . $this->table_name . " SET product_name = ?, description = ?, category = ?, price = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdii", $this->product_name, $this->description, $this->category, $this->price, $this->product_id, $this->user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to execute statement'];
        }
    }

    public function deleteProduct($product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to execute statement'];
        }
    }

    public function getProductsByCategory($category) {
        // Check if category is valid
        if (!ProductCategory::isValidCategory($category)) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            return ['success' => true, 'data' => $products];
        } else {
            return ['success' => false, 'message' => 'No products found in this category'];
        }
    }

    
}
?>