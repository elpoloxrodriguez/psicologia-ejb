<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin role exists
        $role = $this->db->table('roles')->where('name', 'admin')->get()->getRow();
        
        if (!$role) {
            echo "Error: 'admin' role not found. Please run RoleSeeder first.\n";
            return;
        }

        $data = [
            'role_id'  => $role->id,
            'name'     => 'Administrator',
            'cedula'   => '12345678',
            'email'    => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if admin user already exists
        $existingUser = $this->db->table('users')
                               ->where('email', 'admin@example.com')
                               ->countAllResults();

        if ($existingUser === 0) {
            $this->db->table('users')->insert($data);
            echo "Default admin user created successfully.\n";
            echo "Email: admin@example.com\n";
            echo "Password: password\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
}
