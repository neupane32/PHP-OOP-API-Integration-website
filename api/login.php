<?php
include_once '../api_function/login_function.php';

header("Content-Type: application/json");

$requestMethod = $_SERVER['REQUEST_METHOD'];
$data = getRequestData();

if ($requestMethod === 'POST') {
    // Validate incoming request data
    $validatedData = Middleware::validateRequest($data);

    // Select the controller according to the user's role
    $controller = initialize($validatedData['role']);

    // Process authentication
    handleAuthentication($controller, $validatedData);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Invalid request method"
    ]);
}
?>
