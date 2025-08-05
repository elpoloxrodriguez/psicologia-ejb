<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('jwt.secret');
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        // check if token is null or empty
        if (is_null($token) || empty($token)) {
            return Services::response()->setJSON(['error' => 'Access denied. Token not provided.'])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $userRole = $decoded->role;

            if (!in_array($userRole, $arguments)) {
                return Services::response()->setJSON(['error' => 'Forbidden. You do not have the required role.'])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            return Services::response()->setJSON(['error' => 'Invalid token. ' . $e->getMessage()])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
