<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\RefreshTokenModel;

class AuthService
{
    protected $userModel;
    protected $refreshTokenModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->refreshTokenModel = new RefreshTokenModel();
    }

    public function login($username, $password, $rememberMe = false): array|bool
    {
        // Find user by username
        $user = $this->userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // Generate access token (15 minutes)
        $accessToken = generateJWT([
            'user_id' => $user['id'],
            'username' => $user['username']
        ], 900);

        // Generate refresh token
        $refreshTokenExpiry = $rememberMe ? 30 * 24 * 60 * 60 : 7 * 24 * 60 * 60; // 30 days if remember me, 7 days otherwise
        $refreshToken = generateJWT([
            'user_id' => $user['id'],
            'type' => 'refresh'
        ], $refreshTokenExpiry);

        // Delete old refresh tokens for this user (optional - for security)
        $this->refreshTokenModel->where('user_id', $user['id'])->delete();

        $this->refreshTokenModel->insert([
            'user_id' => $user['id'],
            'token' => $refreshToken,
            'expires_at' => date('Y-m-d H:i:s', time() + $refreshTokenExpiry),
            'remember_me' => $rememberMe ? 1 : 0
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 900,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
            ]
        ];
    }

    public function ValidateToken($token)
    {
        $decodedToken = validateJWT($token);

        if (!$decodedToken) {
            return false;
        }

        $user = $this->userModel->find($decodedToken['user_id']);

        if (!$user) {
            return false;
        }

        return [
            'valid' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'expires_in' => $decodedToken['exp']
            ]
        ];
    }

    public function refreshToken($refreshToken)
    {

        $decodedToken = validateJWT($refreshToken);

        if (!$decodedToken || !isset($decodedToken['type']) || $decodedToken['type'] !== 'refresh') {
            return false;
        }

        $tokenData = $this->refreshTokenModel
            ->where('token', $refreshToken)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (!$tokenData) {
            return false;
        }

        $user = $this->userModel->find($tokenData['user_id']);

        $accessToken = generateJWT([
            'user_id' => $user['id'],
            'username' => $user['username']
        ], 900);

        return [
            'access_token' => $accessToken,
            'expires_in' => 900,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ];
    }

    public function logout($refreshToken)
    {
        if (!$refreshToken) {
            return false;
        }
        
        $result = $this->refreshTokenModel->where('token', $refreshToken)->delete();

        if (!$result) {
            log_message('error', 'Failed to delete refresh token: ' . $refreshToken);
            return false;
        }

        return true;
    }
}
