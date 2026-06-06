<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlujoTrabajoController extends Controller
{
    // Listar todos los flujos con búsqueda opcional
    public function index(Request $request)
    {
        $query = FlujoTrabajo::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('codigo', 'like', "%$buscar%")
                  ->orWhere('departamento', 'like', "%$buscar%")
                  ->orWhere('estado', 'like', "%$buscar%");
            });
        }

        $flujos = $query->orderByDesc('id')->get();

        return view('flujos.index', compact('flujos'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('flujos.create');
    }

    // Guardar nuevo flujo
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'estado'       => 'required|in:Activo,Borrador,Completado,Pausado',
        ], [
            'nombre.required'       => 'El nombre del flujo es obligatorio.',
            'departamento.required' => 'El departamento es obligatorio.',
            'estado.required'       => 'El estado es obligatorio.',
        ]);

        $flujo = FlujoTrabajo::create([
            'codigo'       => FlujoTrabajo::generarCodigo(),
            'nombre'       => $request->nombre,
            'departamento' => $request->departamento,
            'estado'       => $request->estado,
            'user_id'      => Auth::id(),
        ]);

        Notificacion::create([
            'user_id' => Auth::id(),
            'tipo' => 'flujo_creado',
            'titulo' => 'Nuevo flujo de trabajo',
            'mensaje' => "Flujo '{$flujo->nombre}' creado con código {$flujo->codigo}",
            'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
            'color' => 'text-amber-500',
            'url' => route('flujos.index'),
        ]);

        return redirect()->route('flujos.index')
            ->with('success', 'Flujo de trabajo creado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(FlujoTrabajo $flujo)
    {
        return view('flujos.edit', compact('flujo'));
    }

    // Actualizar flujo
    public function update(Request $request, FlujoTrabajo $flujo)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'estado'       => 'required|in:Activo,Borrador,Completado,Pausado',
        ]);

        $flujo->update($request->only('nombre', 'departamento', 'estado'));

        return redirect()->route('flujos.index')
            ->with('success', 'Flujo actualizado correctamente.');
    }

    // Eliminar flujo
    public function destroy(FlujoTrabajo $flujo)
    {
        $flujo->delete();

        return redirect()->route('flujos.index')
            ->with('success', 'Flujo eliminado correctamente.');
    }

    // Mostrar vista de línea de tiempo con estados
    public function showTimeline()
    {
        $flujos = FlujoTrabajo::with('estados')->orderByDesc('id')->get();
        return view('flujos', compact('flujos'));
    }
}
