<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReminderAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-absensi {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WA untuk siswa yang belum absensi masuk/pulang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type'); // 'masuk' or 'pulang'
        $today = \Carbon\Carbon::now()->toDateString();
        
        // Cari siswa aktif
        $siswas = \App\Models\Siswa::where(function($q) {
            $q->whereNull('tgl_selesai_magang')
              ->orWhere('tgl_selesai_magang', '>=', \Carbon\Carbon::now()->toDateString());
        })->get();

        $count = 0;

        foreach ($siswas as $siswa) {
            if (!$siswa->no_hp) continue; // Skip jika tidak ada nomor HP

            $absensi = \App\Models\Absensi::where('nisn', $siswa->nisn)
                              ->whereDate('tanggal', $today)
                              ->first();

            $shouldSend = false;
            $pesan = "";

            if ($type === 'masuk') {
                if (!$absensi) {
                    $shouldSend = true;
                    $pesan = "Halo {$siswa->nama},\n\nAnda belum melakukan *Absensi Masuk* hari ini (" . \Carbon\Carbon::now()->translatedFormat('d F Y') . ") di SIM Magang.\nSilahkan segera check-in agar kehadiran Anda terekap.\n\nJika lupa, silahkan gunakan fitur *Pengajuan Lupa Absensi* di sistem.";
                }
            } elseif ($type === 'pulang') {
                if ($absensi && !$absensi->jam_pulang) {
                    $shouldSend = true;
                    $pesan = "Halo {$siswa->nama},\n\nAnda sudah melakukan absensi masuk, namun belum melakukan *Absensi Pulang* hari ini (" . \Carbon\Carbon::now()->translatedFormat('d F Y') . ").\nSilakan segera check-out melalui SIM Magang.";
                } elseif (!$absensi) {
                    $shouldSend = true;
                    $pesan = "Halo {$siswa->nama},\n\nKami mendeteksi Anda belum melakukan absensi masuk dan pulang hari ini (" . \Carbon\Carbon::now()->translatedFormat('d F Y') . ").\nMohon segera melengkapi absensi Anda atau buat pengajuan lupa absensi.";
                }
            } elseif ($type === 'logbook') { // Added new type for logbook reminder
                $logbook = \App\Models\Logbook::where('nisn', $siswa->nisn)
                                              ->whereDate('tanggal', $today)
                                              ->first();
                if (!$logbook) {
                    $shouldSend = true;
                    $pesan = "Halo {$siswa->nama},\n\nMohon perhatian, Anda belum mengisi *Logbook Kegiatan Harian* untuk hari ini (" . \Carbon\Carbon::now()->translatedFormat('d F Y') . ").\nSilakan lengkapi kegiatan Anda di SIM Magang agar pembimbing dapat melakukan penilaian.";
                }
            }

            if ($shouldSend) {
                \App\Jobs\SendWhatsAppNotification::dispatch($siswa->no_hp, $pesan);
                $count++;
            }
        }

        $this->info("Reminder $type dispatched to $count students.");
    }

    // sendWhatsApp method removed as it is now in the Job
}
