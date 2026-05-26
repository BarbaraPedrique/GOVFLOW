<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FlujoTrabajo;

class FlujoTrabajoSeeder extends Seeder
{
    public function run(): void
    {
        $flujos = [
            ['codigo' => 'WF-1024', 'nombre' => 'Aprobación de Presupuesto Q3',  'departamento' => 'Finanzas',          'estado' => 'Activo'],
            ['codigo' => 'WF-1025', 'nombre' => 'Contratación Personal IT',       'departamento' => 'Recursos Humanos',  'estado' => 'Borrador'],
            ['codigo' => 'WF-1026', 'nombre' => 'Revisión Legal Contratos',        'departamento' => 'Legal',             'estado' => 'Completado'],
            ['codigo' => 'WF-1027', 'nombre' => 'Adquisición de Equipos',          'departamento' => 'Operaciones',       'estado' => 'Activo'],
            ['codigo' => 'WF-1028', 'nombre' => 'Auditoría Anual',                 'departamento' => 'Cumplimiento',      'estado' => 'Pausado'],
        ];

        foreach ($flujos as $flujo) {
            FlujoTrabajo::create($flujo);
        }
    }
}
