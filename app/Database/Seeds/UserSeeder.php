<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'employeeID' => 'EMP01',
                'username'   => 'admin',
                'password'   => password_hash('admin', PASSWORD_DEFAULT),
                'title'      => 'นาย',
                'firstname'  => 'admin',
                'lastname'   => 'นะจ๊า',
                'tel'        => '0123456789',
                'position'   => 'Administrator',
                'role'       => 'admin',
                'startdate'  => date('Y-m-d'),
            ],
        ];

        // Insert batch of users
        $this->db->table('users')->insertBatch($data);
    }
}
