<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            FlujoTrabajoSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'administrador')->first();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@govflow.com',
            'password' => bcrypt('admin123'),
            'role_id' => $adminRole?->id,
            'status' => 'activo',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $adminRole?->id,
            'status' => 'activo',
        ]);
    }
}
