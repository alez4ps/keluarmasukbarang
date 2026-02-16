<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insertBatch([
            [
                'username' => 'admin',
                'password' => '$2y$12$tIys0fbB/0u2I/d1SeRyMu0o5U7dslsqTtbLp0AnhE69Sg3KkMJD2', // 123456
                'role'     => 'admin',
            ],
            [
                'username' => 'petugas',
                'password' => '$2y$12$tIys0fbB/0u2I/d1SeRyMu0o5U7dslsqTtbLp0AnhE69Sg3KkMJD2', // 123456
                'role'     => 'petugas',
            ],
            [
                'username' => 'user',
                'password' => '$2y$12$tIys0fbB/0u2I/d1SeRyMu0o5U7dslsqTtbLp0AnhE69Sg3KkMJD2', // 123456
                'role'     => 'user',
            ]
        ]);
    }
}
