<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFlujos = FlujoTrabajo::count();
        $flujosActivos = FlujoTrabajo::where('estado', 'Activo')->count();

        return view('inicio', compact('totalFlujos', 'flujosActivos'));
    }
}
