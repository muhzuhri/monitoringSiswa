<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReminderKegiatan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-kegiatan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WA untuk siswa yang belum mengisi Logbook/Kegiatan harian';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = \Carbon\Carbon::now()->toDateString();
        
        // Cari siswa aktif
        $siswas = \App\Models\Siswa::where(function($q) {
            $q->whereNull('tgl_selesai_magang')
              ->orWhere('tgl_selesai_magang', '>=', \Carbon\Carbon::now()->toDateString());
        })->get();

        $count = 0;

        foreach ($siswas as $siswa) {
            if (!$siswa->no_hp) continue;

            $logbook = \App\Models\Logbook::where('nisn', $siswa->nisn)
                              ->whereDate('tanggal', $today)
                              ->first();

            if (!$logbook) {
                $pesan = "Halo {$siswa->nama},\n\nMohon perhatian, Anda belum mengisi *Logbook Kegiatan Harian* untuk hari ini (" . \Carbon\Carbon::now()->translatedFormat('d F Y') . ").\nSilakan lengkapi kegiatan Anda di SIM Magang agar pembimbing dapat melakukan penilaian.";
                
                \App\Jobs\SendWhatsAppNotification::dispatch($siswa->no_hp, $pesan);
                $count++;
            }
        }

        $this->info("Reminder Kegiatan dispatched to $count students.");
    }
}
