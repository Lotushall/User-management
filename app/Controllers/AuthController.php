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

        $rules = [
            'username' => 'required',
            'password' => 'required',
            'remember_me' => 'permit_empty|in_list[0,1]'
        ];


        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $rememberMe = (bool) $this->request->getPost('remember_me');

        $result = $this->authService->login($username, $password, $rememberMe);

        if (!$result) {
            return $this->fail('Invalid credentials', 401);
        }

        if (isset($result['error'])) {
            return $this->fail($result['error'], 403);
        }

        return $this->respond($result);
    }

    public function refresh()
    {
        $refreshToken = $this->request->getPost('refresh_token');

        if (!$refreshToken) {
            return $this->fail('Refresh token required', 400);
        }

        $result = $this->authService->refreshToken($refreshToken);

        if (!$result) {
            return $this->fail('Invalid or expired refresh token', 401);
        }

        return $this->respond($result);
    }

    public function logout()
    {
        $refreshToken = $this->request->getPost('refresh_token');

        if (!$refreshToken) {
            return $this->fail('Refresh token required', 400);
        }

        if ($refreshToken) {
            $this->authService->logout($refreshToken);
        } else {
            return $this->fail('Refresh token required', 400);
        }

        return $this->respond(['message' => 'Logged out successfully']);
    }
}
