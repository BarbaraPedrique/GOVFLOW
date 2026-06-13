<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestUsers extends Command
{
    protected $signature = 'app:create-test-users';
    protected $description = 'Create 50 test users with roles from administrador to empleado';

    public function handle()
    {
        $roles = Role::pluck('id', 'slug');

        $plan = [
            'administrador' => 4,
            'gerente'       => 8,
            'lider_equipo'  => 13,
            'empleado'      => 25,
        ];

        $created = 0;
        $nombresH = ['Carlos','Luis','Jorge','Miguel','Andrés','Javier','Fernando','Ricardo','Diego','Santiago','Alejandro','Pablo','Cristian','Manuel','Raúl','Iván','Adrián','Héctor','Oscar','Eduardo','Francisco','David','Víctor','Hugo','Marco'];
        $nombresM = ['Ana','María','Laura','Carmen','Sofía','Valentina','Camila','Isabella','Ximena','Gabriela','Elena','Lucía','Paula','Diana','Rosa','Claudia','Mónica','Patricia','Andrea','Silvia','Marta','Verónica','Liliana','Julia','Teresa'];
        $apellidos = ['García','Rodríguez','Martínez','López','Hernández','González','Pérez','Sánchez','Ramírez','Torres','Flores','Rivera','Gómez','Díaz','Cruz','Castillo','Reyes','Morales','Ortiz','Rubio','Vargas','Mendoza','Ramos','Delgado','Acosta'];

        foreach ($plan as $slug => $cantidad) {
            $roleId = $roles[$slug] ?? null;
            if (!$roleId) {
                $this->error("Role '$slug' not found.");
                continue;
            }

            for ($i = 1; $i <= $cantidad; $i++) {
                $genero = ($i % 2 === 0) ? 'M' : 'H';
                $poolNombres = ($genero === 'H') ? $nombresH : $nombresM;
                $nombre = $poolNombres[array_rand($poolNombres)];
                $apellido1 = $apellidos[array_rand($apellidos)];
                $apellido2 = $apellidos[array_rand($apellidos)];

                $name = "$nombre $apellido1 $apellido2";
                $apodo = $nombre;
                $email = strtolower("$nombre.$apellido1." . uniqid('', true) . "@govflow.test");
                $maxAttempts = 10;
                while (User::query()->where('email', $email)->exists() && --$maxAttempts > 0) {
                    $email = strtolower("$nombre.$apellido1." . uniqid('', true) . "@govflow.test");
                }
                if ($maxAttempts === 0) {
                    $email = strtolower("$nombre.$apellido1." . time() . rand(1000, 9999) . "@govflow.test");
                }

                User::create([
                    'name' => $name,
                    'apodo' => $apodo,
                    'email' => $email,
                    'password' => bcrypt('test123'),
                    'role_id' => $roleId,
                    'status' => 'activo',
                ]);

                $created++;
            }
        }

        $this->info("Created $created test users successfully.");
    }
}
