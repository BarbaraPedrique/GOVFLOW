<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TareaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tareas = Tarea::where('user_id', $user->id)
            ->porPrioridad()
            ->get()
            ->groupBy('prioridad');

        return view('tareas', compact('tareas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'prioridad' => 'required|in:alta,media,baja',
            'categoria' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $maxOrden = Tarea::where('user_id', auth()->id())->max('orden') ?? 0;
        $data['user_id'] = auth()->id();
        $data['orden'] = $maxOrden + 1;

        $tarea = Tarea::create($data);

        LogAuditoria::registrar(
            'crear_tarea',
            'Tarea',
            $tarea->id,
            "Tarea '{$tarea->titulo}' creada con prioridad {$tarea->prioridad}",
        );

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
    }

    public function update(Request $request, Tarea $tarea): JsonResponse
    {
        if ($tarea->user_id !== auth()->id()) abort(403);

        if ($request->has('completada')) {
            $tarea->update(['completada' => $request->boolean('completada')]);

            LogAuditoria::registrar(
                $request->boolean('completada') ? 'completar_tarea' : 'reabrir_tarea',
                'Tarea',
                $tarea->id,
                "Tarea '{$tarea->titulo}' " . ($request->boolean('completada') ? 'completada' : 'reabierta'),
            );

            return response()->json(['success' => true]);
        }

        if ($request->has('orden')) {
            $tarea->update(['orden' => $request->integer('orden')]);
            return response()->json(['success' => true]);
        }

        $tarea->update($request->validate([
            'titulo' => 'string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'prioridad' => 'in:alta,media,baja',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(Tarea $tarea): JsonResponse
    {
        if ($tarea->user_id !== auth()->id()) abort(403);
        $tarea->delete();
        return response()->json(['success' => true]);
    }

    public function reordenar(Request $request): JsonResponse
    {
        $ordenes = $request->validate([
            'ordenes' => 'required|array',
            'ordenes.*.id' => 'required|exists:tareas,id',
            'ordenes.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($ordenes['ordenes'] as $item) {
            Tarea::where('id', $item['id'])->where('user_id', auth()->id())
                ->update(['orden' => $item['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
