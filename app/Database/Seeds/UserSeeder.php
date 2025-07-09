<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username' => 'admin',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
        ];

        // Using Query Builder
        $this->db->table('users')->insert($data);
    }
}
