<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create(['role_name' => 'admin']);
        \App\Models\Role::create(['role_name' => 'supervisor']);
        \App\Models\Role::create(['role_name' => 'student']);
    }
}
