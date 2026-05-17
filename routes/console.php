<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mantener la BD activa (evita cold-start de ProxySQL en Laravel Cloud)
Schedule::call(function () {
    DB::select('SELECT 1');
})->everyMinute()->name('db-keepalive')->withoutOverlapping();
