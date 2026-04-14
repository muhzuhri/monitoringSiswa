<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Admin;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Logbook;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminSiswaController extends Controller
{
    protected function authorizeAdmin(): Admin
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);

        return $user;
    }

    public function kelolaSiswa(Request $request)
    {
        $admin = $this->authorizeAdmin();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = Carbon::now()->toDateString();

        // 1. Siswa Aktif
        $query = Siswa::with(['guru', 'pembimbing', 'absensis' => function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        // Filter Hanya Siswa Aktif
        $query->where(function($q) {
            $q->where(function($sq) {
                $sq->where('status', '!=', 'selesai')
                   ->orWhereNull('status');
            })->where(function($subQ) {
                $subQ->where('tgl_selesai_magang', '>=', now())
                     ->orWhereNull('tgl_selesai_magang');
            });
        });

        $siswa = $query->orderBy('nama')->paginate(100);

        // Grouping logic for Siswa Aktif
        $groupedSiswas = $siswa->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok'
            ];
        });

        // Hitung status hari ini
        foreach ($siswa as $s) {
            $s->absen_hari_ini = $s->absensis->first();
        }

        // 2. Riwayat Siswa (Selesai)
        $riwayatQuery = Siswa::with(['guru', 'pembimbing'])
            ->where(function($q) {
                $q->where('status', 'selesai')
                  ->orWhere('tgl_selesai_magang', '<', now());
            });

        if ($periodeId) {
            $riwayatQuery->where('id_tahun_ajaran', $periodeId);
        }

        if ($search) {
            $riwayatQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        $riwayatSiswas = $riwayatQuery->orderBy('tgl_selesai_magang', 'desc')->get();

        $groupedRiwayat = $riwayatSiswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok'
            ];
        });

        $gurus = \App\Models\Guru::orderBy('nama')->get();
        $pembimbings = \App\Models\Pembimbing::orderBy('nama')->get();
        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();

        return view('admin.kelolaSiswa', [
            'user' => $admin,
            'siswa' => $siswa,
            'gurus' => $gurus,
            'pembimbings' => $pembimbings,
            'search' => $search,
            'groupedSiswas' => $groupedSiswas,
            'riwayatSiswas' => $riwayatSiswas,
            'groupedRiwayat' => $groupedRiwayat,
            'periodeId' => $periodeId,
            'periodeOptions' => $periodeOptions,
        ]);
    }

    public function absensiSiswa($nisn)
    {
        $admin = $this->authorizeAdmin();
        $siswa = Siswa::where('nisn', $nisn)->firstOrFail();
        $absensis = Absensi::where('nisn', $nisn)->orderBy('tanggal', 'desc')->paginate(15);
        
        $rekap = [
            'total' => Absensi::where('nisn', $nisn)->count(),
            'hadir' => Absensi::where('nisn', $nisn)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => Absensi::where('nisn', $nisn)->where('status', 'izin')->count(),
            'sakit' => Absensi::where('nisn', $nisn)->where('status', 'sakit')->count(),
            'alpa' => Absensi::where('nisn', $nisn)->where('status', 'alpa')->count(),
        ];

        return view('admin.absensiSiswa', compact('admin', 'siswa', 'absensis', 'rekap'));
    }

    public function logbookSiswa(Request $request, $nisn)
    {
        $admin = $this->authorizeAdmin();
        $siswa = Siswa::where('nisn', $nisn)->firstOrFail();
        $status = $request->input('status');
        
        $query = Logbook::where('nisn', $nisn);
        if ($status) {
            $query->where('status', $status);
        }
        $logbooks = $query->orderBy('tanggal', 'desc')->get();

        return view('admin.logbookSiswa', compact('admin', 'siswa', 'logbooks', 'status'));
    }

    /**
     * Download Jurnal Kegiatan Mingguan Siswa.
     */
    public function downloadJurnalMingguan(Request $request, $nisn)
    {
        $this->authorizeAdmin();
        $siswa = Siswa::where('nisn', $nisn)->firstOrFail();

        $logbooks = $siswa->logbooks()->orderBy('tanggal', 'asc')->get();
        $siswa->load('pembimbing');

        $fileName = "Jurnal_Mingguan_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.printJurnal', [
            'user' => $siswa,
            'logbooks' => $logbooks
        ]);

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Individu) Siswa.
     */
    public function downloadRekapAbsensiIndividu(Request $request, $nisn)
    {
        $this->authorizeAdmin();
        $siswa = Siswa::where('nisn', $nisn)->firstOrFail();

        $absensis = $siswa->absensis()->orderBy('tanggal', 'asc')->get();

        $fileName = "Rekap_Absensi_Individu_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', [
            'user' => $siswa,
            'absensis' => $absensis
        ]);

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Kelompok) Siswa.
     */
    public function downloadRekapAbsensiKelompok(Request $request, $nisnKetua)
    {
        $this->authorizeAdmin();
        
        // Find leader
        $leader = Siswa::where('nisn', $nisnKetua)->firstOrFail();

        // Start Magang from Tahun Ajaran
        $startMagang = $leader->tahunAjaran ? \Carbon\Carbon::parse($leader->tahunAjaran->tgl_mulai) : \Carbon\Carbon::now()->startOfMonth();
        
        // Find range end based on latest activity in the group
        $latestAbsen = Absensi::whereIn('nisn', function($query) use ($nisnKetua) {
            $query->select('nisn')->from('siswa')
                  ->where('nisn_ketua', $nisnKetua)
                  ->orWhere('nisn', $nisnKetua);
        })->max('tanggal');

        $endRange = $latestAbsen ? \Carbon\Carbon::parse($latestAbsen) : \Carbon\Carbon::now();
        if ($endRange->isPast() && $leader->tgl_selesai_magang) {
             $endRange = \Carbon\Carbon::parse($leader->tgl_selesai_magang);
        } else if ($endRange->isPast()) {
            $endRange = \Carbon\Carbon::now();
        }
        
        $months = [];
        $current = $startMagang->copy()->startOfMonth();
        
        while ($current <= $endRange) {
            $month = $current->month;
            $year = $current->year;
            
            // Fetch all group members
            $anggota = Siswa::where(function($q) use ($nisnKetua) {
                $q->where('nisn_ketua', $nisnKetua)->orWhere('nisn', $nisnKetua);
            })->with(['absensis' => function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            }])->get();

            $months[] = [
                'month' => $month,
                'year' => $year,
                'anggota' => $anggota,
                'daysInMonth' => $current->daysInMonth,
                'monthName' => $current->translatedFormat('F')
            ];
            
            $current->addMonth();
        }

        $fileName = "Rekap_Absensi_Kelompok_{$leader->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiKelompok', [
            'user' => $leader,
            'months' => $months
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('siswa', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', 'string', 'max:100'],
            'sekolah' => ['required', 'string', 'max:150'],
            'perusahaan' => ['nullable', 'string', 'max:150'],
            'id_guru' => ['nullable', 'string', 'exists:guru,id_guru'],
            'id_pembimbing' => ['nullable', 'exists:pembimbing,id_pembimbing'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        Siswa::create($validated);

        return redirect()
            ->route('admin.kelolaSiswa')
            ->with('success', 'Akun siswa berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('siswa', 'email')->ignore($siswa->nisn, 'nisn')],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($siswa->nisn, 'nisn')],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', 'string', 'max:100'],
            'sekolah' => ['required', 'string', 'max:150'],
            'perusahaan' => ['nullable', 'string', 'max:150'],
            'id_guru' => ['nullable', 'string', 'exists:guru,id_guru'],
            'id_pembimbing' => ['nullable', 'exists:pembimbing,id_pembimbing'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $siswa->update($validated);

        return redirect()
            ->route('admin.kelolaSiswa')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()
            ->route('admin.kelolaSiswa')
            ->with('success', 'Akun siswa berhasil dihapus.');
    }
}
