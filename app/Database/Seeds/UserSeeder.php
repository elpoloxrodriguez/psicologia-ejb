<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'role_id'  => 1, // 1 for admin
            'name'     => 'Admin User',
            'cedula'   => '12345678',
            'email'    => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ];

        // Using Query Builder
        $this->db->table('users')->insert($data);
    }
}
