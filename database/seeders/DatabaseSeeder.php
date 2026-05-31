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
        $this->call([RoleSeeder::class]);

        User::firstOrCreate(
            ['email' => 'superadmin@govflow.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('admin123'), 'role_id' => Role::where('slug', 'super_admin')->first()?->id, 'status' => 'activo'],
        );

        User::firstOrCreate(
            ['email' => 'admin@govflow.com'],
            ['name' => 'Admin', 'password' => bcrypt('admin123'), 'role_id' => Role::where('slug', 'administrador')->first()?->id, 'status' => 'activo'],
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password'), 'role_id' => Role::where('slug', 'empleado')->first()?->id, 'status' => 'activo'],
        );

        User::firstOrCreate(
            ['email' => 'gerente@govflow.com'],
            ['name' => 'Gerente Test', 'password' => bcrypt('admin123'), 'role_id' => Role::where('slug', 'gerente')->first()?->id, 'status' => 'activo'],
        );

        $this->call([
            FlujoTrabajoSeeder::class,
            FlujoEstadoSeeder::class,
            LogAuditoriaSeeder::class,
        ]);
    }
}
