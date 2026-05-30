<?php
namespace Clases;

class Auth
{
    /**
     * Extracts and validates the token from the Authorization header or cookie.
     * Returns the payload or exits with 401/403.
     */
    public static function requireAuth(?string $requiredRole): array
    {
        $token = null;

        // 1. Intentar obtener del header Authorization
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        }
        
        // 2. Si no está en header, intentar desde cookie
        if (!$token) {
            $token = $_COOKIE['auth_token'] ?? null;
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthenticated. Token required.']);
            exit;
        }

        $payload = JWT::verify($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token.']);
            exit;
        }

        if ($requiredRole && $payload['role'] !== $requiredRole) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Required role: ' . $requiredRole]);
            exit;
        }

        return $payload;
    }
}
