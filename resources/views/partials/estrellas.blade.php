@props(['puntuacion' => 0, 'tamano' => 'h-4 w-4', 'clase' => ''])

@php
    $puntuacion = max(0, min(5, (float) $puntuacion));
    $full = floor($puntuacion);
    $half = ($puntuacion - $full) >= 0.25;
    $empty = 5 - $full - ($half ? 1 : 0);
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
@endphp

<div class="flex items-center gap-0.5 {{ $clase }}">
    @for ($i = 0; $i < $full; $i++)
        <svg class="{{ $tamano }} text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}" /></svg>
    @endfor
    @if ($half)
        @php $uid = 'half-' . md5(uniqid()); @endphp
        <svg class="{{ $tamano }}" viewBox="0 0 20 20">
            <defs><linearGradient id="{{ $uid }}"><stop offset="50%" stop-color="#fbbf24" /><stop offset="50%" stop-color="#cbd5e1" /></linearGradient></defs>
            <path d="{{ $starPath }}" fill="url(#{{ $uid }})" />
        </svg>
    @endif
    @for ($i = 0; $i < $empty; $i++)
        <svg class="{{ $tamano }} text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}" /></svg>
    @endfor
</div>