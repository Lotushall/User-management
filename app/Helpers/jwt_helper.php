<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($payload, $expiration = 900) // 15 minutes default
    {
        $encryption = config('Encryption');
        $key = $encryption->key;
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiration;

        return JWT::encode($payload, $key, 'HS512');
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($token)
    {
        try {
            $encryption = config('Encryption');
            $key = $encryption->key;
            $decoded = JWT::decode($token, new Key($key, 'HS512'));
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
