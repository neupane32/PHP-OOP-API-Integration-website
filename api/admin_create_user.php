<?php
require_once '../api_function/admin_createUser_function.php';

header("Content-Type: application/json");

// Main execution
list($adminController, $authMiddleware) = initialize();
$requestMethod = $_SERVER["REQUEST_METHOD"];
list($data, $adminToken) = getRequestData();

// Middleware - Check token
$authResponse = $authMiddleware->checkToken($adminToken);

if ($authResponse['status']) {
    switch ($requestMethod) {
        case 'POST':
            handlePostRequest($adminController, $data);
            break;

        case 'DELETE':
            handleDeleteRequest($adminController, $data);
            break;

        default:
            echo json_encode([
                'status' => false,
                'message' => 'Invalid request method'
            ]);
            break;
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => $authResponse['message']
    ]);
}

?>