<?php

namespace App\Models;

use CodeIgniter\Model;

class RefreshTokenModel extends Model
{
    protected $table = 'refresh_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'token', 'expires_at', 'remember_me'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Clean expired tokens
    public function cleanExpiredTokens()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
