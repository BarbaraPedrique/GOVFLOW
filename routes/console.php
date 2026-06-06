<?php

use App\Models\Notificacion;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Notificacion::viejas()->delete();
})->daily()->description('Eliminar notificaciones mayores a 7 días');
