<?php
// Include necessary functions
include_once '../api_function/category_function.php';

// Set response header to JSON with UTF-8 charset
header("Content-Type: application/json; charset=UTF-8");

// Main setup
list($db, $authMiddleware) = initialize();
$headers = getRequestHeaders();
$authToken = $headers['Authorization'] ?? '';
$authResponse = handleAuthentication($authMiddleware, $authToken);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$data = getRequestData();

// Instantiate appropriate controller based on user role
$productController = ($authResponse['role'] === 'admin') ? new AdminProductController($db) : new UserProductController($db);
$adminOrUserController = ($authResponse['role'] === 'admin') ? new AdminController($db) : new UserController($db);

// Perform the operation if authenticated
if ($authResponse['status']) {
    if ($requestMethod === 'POST') {
        handlePostRequest($productController, $data);
    } elseif ($requestMethod === 'GET') {
        handleGetRequest($productController, $adminOrUserController, $data, $authResponse['role'], $authToken);
    } elseif ($requestMethod === 'PUT') {
        handlePutRequest($productController, $data, $authResponse['role']);
    } elseif ($requestMethod === 'DELETE') {
        handleDeleteRequest($productController, $adminOrUserController, $data, $authResponse['role']);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid request method'
        ]);
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => $authResponse["message"]
    ]);
}
?>
