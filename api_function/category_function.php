<?php

require_once '../config/database.php';
require_once '../middleware/TokenAuthMiddleware.php';
require_once '../controllers/AdminProductController.php';
require_once '../controllers/UserProductController.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/UserController.php';

// Function to initialize database connection and middleware
function initialize()
{
    $database = new Database();
    $db = $database->Connect();    
    $authMiddleware = new AuthMiddleware($db);
    return [$db, $authMiddleware];
}

// Function to handle authentication and determine role
function handleAuthentication($authMiddleware, $token)
{
    return $authMiddleware->checkToken($token);
}

// Function to get request data
function getRequestData()
{
    return json_decode(file_get_contents("php://input"), true);
}

// Function to get request headers
function getRequestHeaders()
{
    return apache_request_headers();
}

// Function to handle POST requests (create product)
function handlePostRequest($productController, $data)
{
    if (!empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price']) && isset($data['user_id'])) {
        $response = $productController->createProduct($data['product_name'], $data['description'], $data['category'], $data['price'], $data['user_id']);
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Function to handle GET requests
function handleGetRequest($productController, $controller, $data, $role, $token)
{
    if ($role === 'admin') {
        if (isset($data['category'])) {
            $category = $data['category'];
            $products = $productController->getProductsByCategory($category);
            echo json_encode($products);
        } elseif (isset($data['product_id'])) {
            $products = $productController->getProductsById($data['product_id']);
            echo json_encode($products);
        } else {
            $products = $productController->getAllProducts();
            echo json_encode($products);
        }
    } else {
        fetchProducts($productController, $controller, $data, $token);
    }
}

// Function to fetch products for users
function fetchProducts($productController, $controller, $data, $token)
{
    $user = $controller->getUserByToken($token);        
    $category = $data['category'] ?? null;

    if ($category) {
        $products = $productController->getProductsByCategory($category, $user['user_id']);
        echo json_encode($products ?: ["message" => "No products found in this category"]);
    } else {
        $products = $productController->getAllProducts($user['user_id']);
        echo json_encode($products);       
    }
}

// Function to handle PUT requests (update product)
function handlePutRequest($productController, $data, $role)
{
    if ($role === 'admin') {
        if (!empty($data['product_id']) && !empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price']) && isset($data['user_id'])) {
            $response = $productController->updateProduct($data['product_id'], $data['product_name'], $data['description'], $data['category'], $data['price'], $data['user_id']);
            echo json_encode($response);
        } else {
            echo json_encode(["message" => "Incomplete data"]);
        }
    } else {
        echo json_encode(["message" => "Unauthorized access"]);
    }
}

// Function to handle DELETE requests
function handleDeleteRequest($productController, $adminController, $data, $role)
{
    if ($role === 'admin') {
        if (isset($data['category'])) {
            $category = $data['category'];
            $response = $adminController->deleteCategory($category);
            echo json_encode([
                "message" => $response ? "Category deleted successfully" : "Failed to delete category"
            ]);
        } elseif (isset($data['product_id'])) {
            $response = $productController->deleteProduct($data['product_id']);
            echo json_encode($response);
        } else {
            echo json_encode(["message" => "Incomplete data"]);
        }
    } else {
        echo json_encode(["message" => "Unauthorized access"]);
    }
}
?>