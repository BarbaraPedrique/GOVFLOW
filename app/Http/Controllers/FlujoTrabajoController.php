<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
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

        FlujoTrabajo::create([
            'codigo'       => FlujoTrabajo::generarCodigo(),
            'nombre'       => $request->nombre,
            'departamento' => $request->departamento,
            'estado'       => $request->estado,
            'user_id'      => Auth::id(),
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
