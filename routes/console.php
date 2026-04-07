<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jadwal Reminder
// Reminder Absensi Masuk (Jam 09:00 pagi)
Schedule::command('app:reminder-absensi masuk')->dailyAt('10:08')->timezone('Asia/Jakarta');

// Reminder Absensi Pulang (Jam 16:30 sore)
Schedule::command('app:reminder-absensi pulang')->dailyAt('16:30')->timezone('Asia/Jakarta');

// Reminder Kegiatan (Jam 17:00 sore)
Schedule::command('app:reminder-kegiatan')->dailyAt('17:00')->timezone('Asia/Jakarta');
