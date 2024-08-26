<?php
require_once '../config/database.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/UserController.php';
require_once '../middleware/AdminUserAuthMiddleware.php';

// Initialize controller based on the role
function initialize($role)
{
    $database = new Database();
    $db = $database->Connect();

    if ($role === 'admin') {
        return new AdminController($db);
    } else {
        return new UserController($db);
    }
}

// Get request data from the input stream
function getRequestData()
{
    return json_decode(file_get_contents("php://input"), true);
}

// Handle authentication and generate token response
function handleAuthentication($controller, $data)
{
    $role = $data['role'];

    try {
        $response = $controller->authenticate($data['username'], $data['password']);

        if ($response) {
            echo json_encode([
                'message1' => ucfirst($role) . " login successful",
                'message2' => $response
            ]);
        } else {
            throw new Exception(ucfirst($role) . " login failed");
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>