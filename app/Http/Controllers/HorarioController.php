<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Tarea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(): View
    {
        $horarios = Horario::where('user_id', auth()->id())->get();
        $tareas = Tarea::where('user_id', auth()->id())
            ->where('completada', false)
            ->whereNotNull('fecha_vencimiento')
            ->get();
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        $coloresPrioridad = ['alta' => '#EF4444', 'media' => '#F59E0B', 'baja' => '#10B981'];

        $horarioPorDia = [];
        foreach ($dias as $i => $nombre) {
            $bloques = $horarios->where('dia_semana', $i)->values();

            $tareasDelDia = $tareas->filter(function ($t) use ($i) {
                return $t->fecha_vencimiento && (int) $t->fecha_vencimiento->format('N') - 1 === $i;
            })->map(function ($t) use ($coloresPrioridad) {
                $t->hora_inicio = '08:00:00';
                $t->hora_fin = '09:00:00';
                $t->color = $coloresPrioridad[$t->prioridad] ?? '#6B7280';
                $t->es_tarea = true;
                return $t;
            });

            foreach ($tareasDelDia as $t) {
                $bloques->push($t);
            }

            $horarioPorDia[$i] = [
                'nombre' => $nombre,
                'bloques' => $bloques->sortBy('hora_inicio')->values(),
            ];
        }

        return view('horarios', compact('horarioPorDia', 'horarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'dia_semana' => 'required|integer|between:0,6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'titulo' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = auth()->id();
        Horario::create($data);

        return redirect()->route('horarios.index')->with('success', 'Bloque agregado al horario.');
    }

    public function update(Request $request, Horario $horario): JsonResponse
    {
        if ($horario->user_id !== auth()->id()) abort(403);

        $horario->update($request->validate([
            'dia_semana' => 'integer|between:0,6',
            'hora_inicio' => 'date_format:H:i',
            'hora_fin' => 'date_format:H:i|after:hora_inicio',
            'titulo' => 'string|max:255',
            'color' => 'nullable|string|max:7',
            'ubicacion' => 'nullable|string|max:255',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(Horario $horario): JsonResponse
    {
        if ($horario->user_id !== auth()->id()) abort(403);
        $horario->delete();
        return response()->json(['success' => true]);
    }
}
