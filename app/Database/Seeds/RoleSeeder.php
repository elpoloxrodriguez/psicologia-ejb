<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'admin'],
            ['name' => 'psychologist'],
            ['name' => 'patient'],
        ];

        // Using Query Builder
        $this->db->table('roles')->insertBatch($data);
    }
}
