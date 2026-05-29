<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'ver_usuarios', 'display_name' => 'Ver usuarios'],
            ['name' => 'crear_usuarios', 'display_name' => 'Crear usuarios'],
            ['name' => 'editar_usuarios', 'display_name' => 'Editar usuarios'],
            ['name' => 'eliminar_usuarios', 'display_name' => 'Eliminar usuarios'],
            ['name' => 'ver_flujos', 'display_name' => 'Ver flujos de trabajo'],
            ['name' => 'crear_flujos', 'display_name' => 'Crear flujos de trabajo'],
            ['name' => 'editar_flujos', 'display_name' => 'Editar flujos de trabajo'],
            ['name' => 'eliminar_flujos', 'display_name' => 'Eliminar flujos de trabajo'],
            ['name' => 'activar_usuarios', 'display_name' => 'Activar/desactivar usuarios'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        $adminRole = Role::where('slug', 'administrador')->first();
        $gerenteRole = Role::where('slug', 'gerente')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(Permission::all());
        }

        if ($gerenteRole) {
            $gerenteRole->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', ['ver_flujos', 'crear_flujos', 'editar_flujos', 'eliminar_flujos'])->get()
            );
        }
    }
}
