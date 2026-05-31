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
            ['name' => 'ver_usuarios',      'display_name' => 'Ver usuarios'],
            ['name' => 'crear_usuarios',    'display_name' => 'Crear usuarios'],
            ['name' => 'editar_usuarios',   'display_name' => 'Editar usuarios'],
            ['name' => 'eliminar_usuarios', 'display_name' => 'Eliminar usuarios'],
            ['name' => 'activar_usuarios',  'display_name' => 'Activar/desactivar usuarios'],
            ['name' => 'gestionar_roles',   'display_name' => 'Gestionar roles de usuario'],
            ['name' => 'gestionar_equipo',  'display_name' => 'Gestionar empleados a cargo'],
            ['name' => 'ver_flujos',        'display_name' => 'Ver flujos de trabajo'],
            ['name' => 'crear_flujos',      'display_name' => 'Crear flujos de trabajo'],
            ['name' => 'editar_flujos',     'display_name' => 'Editar flujos de trabajo'],
            ['name' => 'eliminar_flujos',   'display_name' => 'Eliminar flujos de trabajo'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        $all = Permission::all();

        $superAdmin = Role::where('slug', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching($all);
        }

        $admin = Role::where('slug', 'administrador')->first();
        if ($admin) {
            $admin->permissions()->syncWithoutDetaching(
                $all->whereNotIn('name', ['gestionar_roles'])
            );
        }

        $gerente = Role::where('slug', 'gerente')->first();
        if ($gerente) {
            $gerente->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', ['ver_flujos', 'crear_flujos', 'editar_flujos', 'eliminar_flujos'])->get()
            );
        }

        $liderEquipo = Role::where('slug', 'lider_equipo')->first();
        if ($liderEquipo) {
            $liderEquipo->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', ['ver_flujos', 'ver_usuarios', 'gestionar_equipo'])->get()
            );
        }
    }
}
