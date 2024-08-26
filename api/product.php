<?php
require_once '../api_function/product_function.php';

header("Content-Type: application/json; charset=UTF-8");

// Main execution
list($dbConnection, $adminController, $productController, $userController, $authMiddleware) = initialize();
$headers = getRequestHeaders();
$authResponse = $authMiddleware->checkToken($headers['Authorization']);
$data = getRequestData();
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Check the status from authResponse (middleware)
if ($authResponse['status']) {
    $user = $userController->getUserByToken($headers['Authorization']);

    if ($user) { // If user
        switch ($requestMethod) {
            case 'GET':
                handleGetRequestUser($productController, $user, $data);
                break;

            case 'POST':
                handlePostRequest($productController, $user, $data);
                break;

            case 'PUT':
                handlePutRequest($productController, $user, $data);
                break;

            case 'DELETE':
                handleDeleteRequest($productController, $user, $data);
                break;

            default:
                echo json_encode([
                    'status' => false,
                    'message' => 'Invalid request method'
                ]);
                break;
        }
    } else {
        switch ($requestMethod) {
            case 'GET':
                handleGetRequestAdmin($adminController, $authResponse, $data);
                break;

            default:
                echo json_encode([
                    'status' => false,
                    'message' => 'Invalid request method'
                ]);
                break;
        }
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => $authResponse["message"]
    ]);
}

?>