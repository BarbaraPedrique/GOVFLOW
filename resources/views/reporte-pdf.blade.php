<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 12px; color: #64748b; margin-bottom: 20px; }
        h2 { font-size: 14px; border-bottom: 2px solid #3b82f6; padding-bottom: 4px; margin-top: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #f1f5f9; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        .metric { flex: 1; min-width: 120px; padding: 8px; border-radius: 8px; display: inline-block; }
        .metric .value { font-size: 20px; font-weight: bold; }
        .metric .label { font-size: 9px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
        .blue { background: #eff6ff; }
        .green { background: #f0fdf4; }
        .amber { background: #fffbeb; }
        .rose { background: #fff1f2; }
        .purple { background: #faf5ff; }
        .cyan { background: #ecfeff; }
        .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-rose { background: #ffe4e6; color: #9f1239; }
        .bar-chart { margin: 4px 0; }
        .bar { height: 12px; border-radius: 4px; text-align: right; padding-right: 4px; font-size: 8px; font-weight: 600; color: #fff; line-height: 12px; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 10px;">
        <h1 style="margin: 0;">GOVFLOW</h1>
        <p class="subtitle" style="margin: 4px 0 0 0;">{{ $titulo }} — Del {{ $desde }} al {{ $hasta }}</p>
    </div>

    <h2>Flujos de Trabajo Realizados</h2>
    @if($detalle_flujos->isEmpty())
        <p style="color: #94a3b8;">No se realizaron flujos en este período.</p>
    @else
        <table>
            <thead>
                <tr><th>Código</th><th>Nombre</th><th>Estado</th><th>Vigente</th><th>Pasos</th><th>A Tiempo</th><th>Fuera</th><th>Duración</th></tr>
            </thead>
            <tbody>
                @foreach($detalle_flujos as $flujo)
                    @php
                        $h = $flujo['duracion_horas'];
                        $duracion = $h < 1 ? round($h * 60) . 'min' : ($h < 24 ? round($h) . 'h' : round($h / 24) . 'd');
                    @endphp
                    <tr>
                        <td>{{ $flujo['codigo'] }}</td>
                        <td>{{ $flujo['nombre'] }}</td>
                        <td>
                            @php
                                $estado = $flujo['estado'] ?? 'en_progreso';
                                $badgeClass = $estado === 'completada' ? 'badge-green' : 'badge-amber';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                        </td>
                        <td><span class="badge {{ $flujo['vigente'] ? 'badge-green' : 'badge-rose' }}">{{ $flujo['vigente'] ? 'Vigente' : 'Eliminado' }}</span></td>
                        <td>{{ $flujo['pasos_completados'] }}/{{ $flujo['total_pasos'] }}</td>
                        <td><span class="badge badge-green">{{ $flujo['a_tiempo'] }}</span></td>
                        <td>@if($flujo['fuera_tiempo'] > 0)<span class="badge badge-rose">{{ $flujo['fuera_tiempo'] }}</span>@else<span style="color: #94a3b8;">—</span>@endif</td>
                        <td>{{ $duracion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Top 3 Descansos</h2>
    @if($top_descansos->isEmpty())
        <p style="color: #94a3b8;">Sin registros de descanso en este período.</p>
    @else
        <table>
            <thead>
                <tr><th>#</th><th>Usuario</th><th>Rol</th><th>Tiempo Total</th></tr>
            </thead>
            <tbody>
                @foreach($top_descansos as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $d->apodo ?? $d->name }}</td>
                        <td>{{ $d->rol ?? 'Sin rol' }}</td>
                        <td><strong>{{ $d->total_formatted }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Resumen General</h2>
    <table>
        <tr>
            <td class="metric blue"><div class="value">{{ $flujos_creados }}</div><div class="label">Flujos Creados</div></td>
            <td class="metric green"><div class="value">{{ $flujos_realizados }}</div><div class="label">Flujos Completados</div></td>
            <td class="metric purple"><div class="value">{{ $flujos_eliminados }}</div><div class="label">Flujos Eliminados</div></td>
            <td class="metric amber"><div class="value">{{ $nuevos_ingresos }}</div><div class="label">Nuevos Ingresos</div></td>
        </tr>
        <tr>
            <td class="metric blue"><div class="value">{{ $participantes }}</div><div class="label">Participantes</div></td>
            <td class="metric cyan"><div class="value">{{ $solicitudes }}</div><div class="label">Solicitudes</div></td>
            <td class="metric amber"><div class="value">{{ $modificaciones }}</div><div class="label">Modificaciones</div></td>
            <td class="metric rose"><div class="value">{{ $empleados_eliminados }}</div><div class="label">Empleados Eliminados</div></td>
            <td class="metric green"><div class="value">{{ $cambios_roles }}</div><div class="label">Cambios de Roles</div></td>
        </tr>
    </table>

    @if(count($semanas) > 0)
        @php $maxEfectividad = max(array_column($semanas, 'efectividad')); @endphp
        <h2>Rendimiento por Semana</h2>
        <table>
            <thead>
                <tr><th>Semana</th><th>Flujos</th><th>Tareas</th><th>Efectividad</th><th style="width: 120px;">Barra</th></tr>
            </thead>
            <tbody>
                @foreach($semanas as $s)
                    @php
                        $pct = $maxEfectividad > 0 ? max(10, round(($s['efectividad'] / $maxEfectividad) * 100)) : 0;
                        $color = $s['efectividad'] >= 70 ? '#166534' : ($s['efectividad'] >= 40 ? '#92400e' : '#9f1239');
                        $bg = $s['efectividad'] >= 70 ? '#22c55e' : ($s['efectividad'] >= 40 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <tr>
                        <td><strong>Semana {{ $s['semana'] }}</strong></td>
                        <td>{{ $s['flujos'] }}</td>
                        <td>{{ $s['tareas_completadas'] }}/{{ $s['tareas_totales'] }}</td>
                        <td><span class="badge" style="background: {{ $bg }}; color: #fff;">{{ $s['efectividad'] }}%</span></td>
                        <td><div class="bar-chart"><div class="bar" style="width: {{ $pct }}%; background: {{ $bg }};">@if($pct > 20){{ $s['efectividad'] }}%@endif</div></div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Rendimiento de Equipos</h2>
    @if($equipos->isEmpty())
        <p style="color: #94a3b8;">No hay equipos registrados.</p>
    @else
        <table>
            <thead>
                <tr><th>Equipo</th><th>Miembros</th><th>Tareas Completadas</th><th>Tareas Pendientes</th><th>Efectividad</th></tr>
            </thead>
            <tbody>
                @foreach($equipos as $equipo)
                    @php $total = $equipo['tareas_completadas'] + $equipo['tareas_pendientes']; @endphp
                    <tr>
                        <td>{{ $equipo['nombre'] }}</td>
                        <td>{{ $equipo['miembros'] }}</td>
                        <td>{{ $equipo['tareas_completadas'] }}</td>
                        <td>{{ $equipo['tareas_pendientes'] }}</td>
                        <td>
                            @if($total > 0)
                                <span class="badge {{ ($equipo['tareas_completadas']/$total) >= 0.7 ? 'badge-green' : (($equipo['tareas_completadas']/$total) >= 0.4 ? 'badge-amber' : 'badge-rose') }}">
                                    {{ round(($equipo['tareas_completadas']/$total)*100, 1) }}%
                                </span>
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Mejor Rendimiento</h2>
    @if($mejor_rendimiento->isEmpty())
        <p style="color: #94a3b8;">Sin datos de rendimiento.</p>
    @else
        <table>
            <thead>
                <tr><th>#</th><th>Usuario</th><th>Rol</th><th>Tareas</th><th>Completadas</th><th>Tasa</th></tr>
            </thead>
            <tbody>
                @foreach($mejor_rendimiento as $i => $u)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $u['apodo'] }}</td>
                        <td>{{ $u['rol'] }}</td>
                        <td>{{ $u['tareas_total'] }}</td>
                        <td>{{ $u['tareas_completadas'] }}</td>
                        <td><span class="badge badge-green">{{ $u['tasa'] }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Peor Rendimiento</h2>
    @if($peor_rendimiento->isEmpty())
        <p style="color: #94a3b8;">Sin datos de rendimiento.</p>
    @else
        <table>
            <thead>
                <tr><th>#</th><th>Usuario</th><th>Rol</th><th>Tareas</th><th>Completadas</th><th>Tasa</th></tr>
            </thead>
            <tbody>
                @foreach($peor_rendimiento as $i => $u)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $u['apodo'] }}</td>
                        <td>{{ $u['rol'] }}</td>
                        <td>{{ $u['tareas_total'] }}</td>
                        <td>{{ $u['tareas_completadas'] }}</td>
                        <td><span class="badge {{ $u['tasa'] < 30 ? 'badge-rose' : 'badge-amber' }}">{{ $u['tasa'] }}%</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php
        $pieColors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
        $pieTotal = $pie_equipos->sum('tareas_completadas');
    @endphp
    <h2>Distribución de Tareas Completadas por Equipo</h2>
    @if($pieTotal > 0)
        @if($pie_chart_base64)
            <table style="width: auto;">
                <tr>
                    <td style="border: none; vertical-align: middle;">
                        <img src="data:image/png;base64,{{ $pie_chart_base64 }}" width="250" height="250"/>
                    </td>
                    <td style="border: none; vertical-align: middle; padding-left: 15px;">
                        <table style="margin: 0; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="background: #f1f5f9; padding: 4px 8px; font-size: 9px; text-transform: uppercase; text-align: left; border-bottom: 2px solid #e2e8f0;">Equipo</th>
                                    <th style="background: #f1f5f9; padding: 4px 8px; font-size: 9px; text-transform: uppercase; text-align: left; border-bottom: 2px solid #e2e8f0;">Completadas</th>
                                    <th style="background: #f1f5f9; padding: 4px 8px; font-size: 9px; text-transform: uppercase; text-align: left; border-bottom: 2px solid #e2e8f0;">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pie_equipos as $i => $eq)
                                    @php $pct = round(($eq['tareas_completadas'] / $pieTotal) * 100, 1); @endphp
                                    <tr>
                                        <td style="border-bottom: 1px solid #e2e8f0; padding: 3px 8px; font-size: 10px;"><span style="display: inline-block; width: 8px; height: 8px; background: {{ $pieColors[$i] }}; margin-right: 4px;"></span>{{ $eq['nombre'] }}</td>
                                        <td style="border-bottom: 1px solid #e2e8f0; padding: 3px 8px; font-size: 10px; font-weight: bold;">{{ $eq['tareas_completadas'] }}</td>
                                        <td style="border-bottom: 1px solid #e2e8f0; padding: 3px 8px; font-size: 10px;">{{ $pct }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        @else
            <table>
                <thead>
                    <tr><th>Equipo</th><th>Completadas</th><th>Porcentaje</th></tr>
                </thead>
                <tbody>
                    @foreach($pie_equipos as $i => $eq)
                        @php $pct = round(($eq['tareas_completadas'] / $pieTotal) * 100, 1); @endphp
                        <tr>
                            <td><span style="display: inline-block; width: 8px; height: 8px; background: {{ $pieColors[$i] }}; margin-right: 4px;"></span>{{ $eq['nombre'] }}</td>
                            <td>{{ $eq['tareas_completadas'] }}</td>
                            <td>{{ $pct }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <p style="color: #94a3b8;">No hay datos de tareas completadas por equipo en este período.</p>
    @endif

    <div class="footer">
        GOVFLOW - Reporte generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
