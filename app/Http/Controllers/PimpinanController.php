<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Pembimbing;
use App\Models\Pimpinan;
use App\Models\TahunAjaran;
use App\Models\InformasiDashboard;
use App\Models\ProgramStudi;
use App\Models\Admin;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PimpinanController extends Controller
{
    protected function authorizePimpinan(): Pimpinan
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'pimpinan', 403);

        return $user;
    }

    public function index()
    {
        $user = $this->authorizePimpinan();
        
        $stats = [
            'total_siswa' => Siswa::count(),
            'siswa_aktif' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => Guru::count(),
            'total_pembimbing' => Pembimbing::count(),
        ];

        $informasi = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan', 'asc')->get();

        return view('pimpinan.home', compact('user', 'stats', 'informasi', 'programStudis'));
    }

    public function kelolaAdmin(Request $request)
    {
        $user = $this->authorizePimpinan();
        $search = $request->input('search');
        
        $query = Admin::query();
        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        $admins = $query->orderBy('nama', 'asc')->paginate(10);
        
        return view('pimpinan.admin', compact('user', 'admins', 'search'));
    }

    public function storeAdmin(Request $request)
    {
        $this->authorizePimpinan();
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function updateAdmin(Request $request, $id)
    {
        $this->authorizePimpinan();
        $admin = Admin::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $id . ',id_admin',
            'password' => 'nullable|min:6',
        ]);

        $admin->nama = $request->nama;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroyAdmin($id)
    {
        $this->authorizePimpinan();
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }

    public function siswa(Request $request)
    {
        $user = $this->authorizePimpinan();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = \Carbon\Carbon::now()->toDateString();

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

        $siswa = $query->orderBy('nama', 'asc')->paginate(100);

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

        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
        $lokasis = \App\Models\LokasiAbsensi::orderBy('nama_lokasi', 'asc')->get();

        return view('pimpinan.siswa', compact(
            'user',
            'siswa',
            'groupedSiswas',
            'riwayatSiswas',
            'groupedRiwayat',
            'search',
            'periodeId',
            'periodeOptions',
            'lokasis'
        ));
    }

    public function guru()
    {
        $user = $this->authorizePimpinan();
        $guru = Guru::with('siswas')->orderBy('nama', 'asc')->paginate(10);
        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();

        return view('pimpinan.guru', compact('user', 'guru', 'periodeOptions'));
    }

    public function pembimbing()
    {
        $user = $this->authorizePimpinan();
        $pembimbing = Pembimbing::with('siswas')->orderBy('nama', 'asc')->paginate(10);
        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();

        return view('pimpinan.pembimbing', compact('user', 'pembimbing', 'periodeOptions'));
    }

    public function rekap()
    {
        $user = $this->authorizePimpinan();
        
        $stats = [
            'siswa_aktif' => Siswa::where(function($q) {
                $q->where('status', '!=', 'selesai')
                  ->orWhereNull('status');
            })->where(function($q) {
                $q->where('tgl_selesai_magang', '>=', now())
                  ->orWhereNull('tgl_selesai_magang');
            })->count(),
            'siswa_selesai' => Siswa::where('status', 'selesai')
                ->orWhere('tgl_selesai_magang', '<', now())
                ->count(),
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
        ];

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('pimpinan.rekap', compact('user', 'stats', 'tahunAjarans'));
    }

    public function rekapStats(Request $request)
    {
        $this->authorizePimpinan();
        $periode = $request->input('periode');

        $queryAktif = Siswa::where(function($q) {
            $q->where('status', '!=', 'selesai')
              ->orWhereNull('status');
        })->where(function($q) {
            $q->where('tgl_selesai_magang', '>=', now())
              ->orWhereNull('tgl_selesai_magang');
        });

        $querySelesai = Siswa::where(function($q) {
            $q->where('status', 'selesai')
              ->orWhere('tgl_selesai_magang', '<', now());
        });

        $queryTotal = Siswa::query();
        $queryGuru  = Guru::query();

        if ($periode) {
            $queryAktif->where('id_tahun_ajaran', $periode);
            $querySelesai->where('id_tahun_ajaran', $periode);
            $queryTotal->where('id_tahun_ajaran', $periode);
            $queryGuru->where('id_tahun_ajaran', $periode);
        }

        return response()->json([
            'siswa_aktif'   => $queryAktif->count(),
            'siswa_selesai' => $querySelesai->count(),
            'total_siswa'   => $queryTotal->count(),
            'total_guru'    => $queryGuru->count(),
        ]);
    }

    public function profil()
    {
        $user = $this->authorizePimpinan();
        return view('pimpinan.profil', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = $this->authorizePimpinan();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pimpinan,email,' . $user->id_pimpinan . ',id_pimpinan',
            'no_hp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:100',
        ]);

        \App\Models\Pimpinan::query()->where('id_pimpinan', $user->id_pimpinan)->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jabatan' => $request->jabatan,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $this->authorizePimpinan();
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        \App\Models\Pimpinan::query()->where('id_pimpinan', $user->id_pimpinan)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
