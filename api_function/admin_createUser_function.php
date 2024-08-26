<?php
require_once '../config/database.php';
require_once '../middleware/TokenAuthMiddleware.php';  
require_once '../controllers/AdminController.php';

// Initialize database connection and controllers
function initialize()
{
    $database = new Database();
    $dbConnection = $database->Connect();
    $adminController = new AdminController($dbConnection);
    $authMiddleware = new AuthMiddleware($dbConnection); 
    return [$adminController, $authMiddleware];
}

// Get request data and headers
function getRequestData()
{
    $data = json_decode(file_get_contents("php://input"));
    $headers = apache_request_headers();
    $adminToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    return [$data, $adminToken];
}

// Handle POST request to create a user
function handlePostRequest($adminController, $data)
{
    if (isset($data->username) && isset($data->password)) {
        $response = $adminController->createUser($data->username, $data->password);
        echo json_encode($response);
    } else {
        echo json_encode([
            'message' => 'Username and password are required'
        ]);
    }
}

// Handle DELETE request to delete a user
function handleDeleteRequest($adminController, $data)
{
    if (isset($data->user_id)) {
        $response = $adminController->deleteUser(intval($data->user_id));
        echo json_encode([
            "message" => $response ? "User deleted successfully" : "Failed to delete user"
        ]);
    } else {
        echo json_encode([
            'message' => 'User ID is required'
        ]);
    }
}
?>