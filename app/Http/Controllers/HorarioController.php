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
            ->whereNull('completed_at')
            ->whereNotNull('fecha_vencimiento')
            ->where(function ($q) {
                $q->whereNull('categoria')
                  ->orWhereNotIn('categoria', ['Horario', 'Flujo', 'Solicitud']);
            })
            ->get();
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        $coloresPrioridad = ['alta' => '#EF4444', 'media' => '#F59E0B', 'baja' => '#10B981'];

        $horarioPorDia = [];
        foreach ($dias as $i => $nombre) {
            $bloques = $horarios->where('dia_semana', $i)->values();

            $tareasDelDia = $tareas->filter(function ($t) use ($i) {
                return $t->fecha_vencimiento && (int) $t->fecha_vencimiento->format('N') - 1 === $i;
            })->map(function ($t) use ($coloresPrioridad) {
                if (!$t->hora_inicio) $t->hora_inicio = '08:00:00';
                if (!$t->hora_fin) $t->hora_fin = '09:00:00';
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

        $userId = auth()->id();
        $data['user_id'] = $userId;
        $horario = Horario::create($data);

        $monday = now()->startOfWeek();
        $fechaVencimiento = $monday->copy()->addDays((int) $data['dia_semana']);

        $exists = Tarea::where('user_id', $userId)
            ->where('titulo', $data['titulo'])
            ->whereDate('fecha_vencimiento', $fechaVencimiento)
            ->exists();

        if (!$exists) {
            Tarea::create([
                'user_id' => $userId,
                'titulo' => $data['titulo'],
                'prioridad' => 'media',
                'categoria' => 'Horario',
                'fecha_vencimiento' => $fechaVencimiento,
                'orden' => (Tarea::where('user_id', $userId)->max('orden') ?? 0) + 1,
            ]);
        }

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

        $userId = auth()->id();
        $monday = now()->startOfWeek();
        $fechaVencimiento = $monday->copy()->addDays((int) $horario->dia_semana);

        Tarea::where('user_id', $userId)
            ->where('titulo', $horario->titulo)
            ->whereDate('fecha_vencimiento', $fechaVencimiento)
            ->where('categoria', 'Horario')
            ->delete();

        Horario::destroy($horario->id);
        return response()->json(['success' => true]);
    }
}
