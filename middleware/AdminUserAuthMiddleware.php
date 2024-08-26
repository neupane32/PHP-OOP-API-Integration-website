<?php

class Middleware
{
    public static function validateRequest(array $data): array
    {
        try {
            // Check for required fields
            if (empty($data['username']) || empty($data['password'])) {
                throw new Exception("Username and password are required");
            }

            // Return validated data with role
            return [
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => ($data['username'] === 'admin' && $data['password'] === 'admin') ? 'admin' : 'user',
            ];
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}

?>
