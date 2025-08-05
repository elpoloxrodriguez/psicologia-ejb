<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

function generate_jwt($email, $userId, $role)
{
    $key = getenv('jwt.secret');
    if (empty($key)) {
        log_message('error', 'JWT secret key is not configured');
        throw new Exception('JWT secret key is not configured');
    }
    
    $iat = time(); // current timestamp value
    $exp = $iat + 3600; // expire time is 1 hour from current time

    $payload = [
        'iss' => 'CodeIgniter4',
        'aud' => 'WebApp',
        'sub' => 'Authentication',
        'iat' => $iat,
        'exp' => $exp,
        'email' => $email,
        'userId' => $userId,
        'role' => $role
    ];

    try {
        return JWT::encode($payload, $key, 'HS256');
    } catch (Exception $e) {
        log_message('error', 'JWT encode error: ' . $e->getMessage());
        throw new Exception('Failed to generate token');
    }
}

function validate_jwt($token)
{
    $key = getenv('jwt.secret');
    if (empty($key)) {
        log_message('error', 'JWT secret key is not configured');
        return ['status' => false, 'message' => 'Authentication configuration error', 'code' => 'config_error'];
    }
    
    if (empty($token)) {
        log_message('error', 'Empty JWT token provided');
        return ['status' => false, 'message' => 'No authentication token provided', 'code' => 'no_token'];
    }
    
    // Clean the token (remove 'Bearer ' if present)
    $token = str_replace('Bearer ', '', trim($token));
    
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        
        // Convert to array for consistent response format
        $decodedArray = json_decode(json_encode($decoded), true);
        
        // Log successful validation
        log_message('debug', 'JWT token validated for user: ' . ($decodedArray['email'] ?? 'unknown'));
        
        return [
            'status' => true, 
            'data' => [
                'userId' => $decodedArray['userId'] ?? null,
                'email' => $decodedArray['email'] ?? null,
                'role' => $decodedArray['role'] ?? null
            ]
        ];
        
    } catch (ExpiredException $e) {
        log_message('error', 'Expired JWT token: ' . $e->getMessage());
        return ['status' => false, 'message' => 'Token has expired', 'code' => 'token_expired'];
    } catch (SignatureInvalidException $e) {
        log_message('error', 'Invalid JWT signature: ' . $e->getMessage());
        return ['status' => false, 'message' => 'Invalid token signature', 'code' => 'invalid_signature'];
    } catch (BeforeValidException $e) {
        log_message('error', 'JWT not yet valid: ' . $e->getMessage());
        return ['status' => false, 'message' => 'Token not yet valid', 'code' => 'token_not_yet_valid'];
    } catch (DomainException | InvalidArgumentException | UnexpectedValueException $e) {
        log_message('error', 'JWT validation error: ' . $e->getMessage());
        return ['status' => false, 'message' => 'Invalid token format', 'code' => 'invalid_token'];
    } catch (Exception $e) {
        log_message('error', 'Unexpected JWT error: ' . $e->getMessage());
        return ['status' => false, 'message' => 'Authentication failed', 'code' => 'authentication_failed'];
    }
}

// Helper function to extract token from request
function get_jwt_from_request($request) {
    $header = $request->getHeaderLine('Authorization');
    
    // Try to get token from Authorization header
    if (!empty($header)) {
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
    }
    
    // Try to get token from cookie
    $token = $request->getCookie('jwt_token');
    if (!empty($token)) {
        return $token;
    }
    
    // Try to get token from POST data
    $token = $request->getPost('token') ?? $request->getGet('token');
    if (!empty($token)) {
        return $token;
    }
    
    return null;
}
