<?php

namespace App\Controllers;

use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {

        $data = $this->request->getJSON(true);

        $rules = [
            'username' => 'required',
            'password' => 'required',
            'remember_me' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules, $data)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $username = $data['username'];
        $password = $data['password'];
        $rememberMe = isset($data['remember_me']) ? (bool) $data['remember_me'] : false;

        $result = $this->authService->login($username, $password, $rememberMe);

        if (!$result) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (isset($result['error'])) {
            return $this->respond([
                'status' => 'error',
                'message' => $result['error']
            ], 403);
        }

        return $this->respond([
            'status' => 'success',
            'data' => $result
        ], 200);
    }

    public function validateToken()
    {
        try {
            $authHeader = $this->request->getHeaderLine('Authorization');

            if (!$authHeader) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Authorization header is required'
                ], 401);
            }

            if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid authorization format. Use: Bearer <token>'
                ], 401);
            }

            $token = $matches[1];
            $result = $this->authService->validateToken($token);

            if (!$result) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ], 401);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Token is valid',
                'data' => $result
            ], 200);
            
        } catch (\Exception $e) {
            log_message('error', 'Token validation error: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'An error occurred during token validation'
            ], 500);
        }
    }

    public function refresh()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['refresh_token'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Refresh token required'
            ])->setStatusCode(400);
        }

        $result = $this->authService->refreshToken($data['refresh_token']);

        if (!$result) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid or expired refresh token'
            ])->setStatusCode(401);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $result
        ])->setStatusCode(200);
    }

    public function logout()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['refresh_token'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Refresh token required'
            ])->setStatusCode(400);
        }

        $result = $this->authService->logout($data['refresh_token']);

        if (!$result) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to logout'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ])->setStatusCode(200);
    }
}
