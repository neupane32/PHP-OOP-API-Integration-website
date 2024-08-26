<?php
require_once '../config/database.php';
require_once '../middleware/TokenAuthMiddleware1.php'; // Adjust the name if needed
require_once '../controllers/AdminController.php';
require_once '../controllers/UserProductController.php';
require_once '../controllers/UserController.php';

// Initialize database connection and controllers
function initialize() {
    $database = new Database();
    $dbConnection = $database->Connect();
    $adminController = new AdminController($dbConnection);
    $productController = new UserProductController($dbConnection);
    $userController = new UserController($dbConnection);
    $authMiddleware = new AuthMiddleware($dbConnection);
    return [$dbConnection, $adminController, $productController, $userController, $authMiddleware];
}

// Get request headers
function getRequestHeaders() {
    return apache_request_headers();
}

// Get request data from input
function getRequestData() {
    return json_decode(file_get_contents("php://input"), true);
}

// Handle GET request for products by admin
function handleGetRequestAdmin($adminController, $authResponse, $data) {
    if ($authResponse['role'] === 'admin') {
        $productId = isset($data['product_id']) ? intval($data['product_id']) : null;
        $response = $adminController->getProducts($productId);
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Unauthorized access"]);
    }
}

// Handle GET request for products by user
function handleGetRequestUser($productController, $user, $data) {
    if (isset($data['product_id'])) {
        $productId = intval($data['product_id']);
        $response = $productController->getProductById($productId, $user['user_id']);
    } else {
        $response = $productController->getAllProducts($user['user_id']);
    }
    if($response===null){
        echo json_encode(["message" => "Unauthorized access"]);        
    }else{
        echo json_encode($response);
    }
}

// Handle POST request to create a product
function handlePostRequest($productController, $user, $data) {
    if (!empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price'])) {
        $response = $productController->createProduct(
            $data['product_name'],
            $data['description'],
            $data['category'],
            floatval($data['price']), // Convert value to float
            $user['user_id']
        );
        echo json_encode(["message" => $response]);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Handle PUT request to update a product
function handlePutRequest($productController, $user, $data) {
    if (!empty($data['product_id']) && !empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price'])) {
        $response = $productController->updateProduct(
            intval($data['product_id']),
            $data['product_name'],
            $data['description'],
            $data['category'],
            floatval($data['price']), // Convert value to float
            $user['user_id']
        );
        echo json_encode(["message" => $response ? "Product updated successfully" : "Failed to update product"]);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Handle DELETE request to delete a product
function handleDeleteRequest($productController, $user, $data) {
    if (!empty($data['product_id'])) {
        $response = $productController->deleteProduct(intval($data['product_id']), $user['user_id']);
        echo json_encode(["message" => $response ? "Product deleted successfully" : "Failed to delete product"]);
    } else {
        echo json_encode(["message" => "Product ID required"]);
    }
}
?>