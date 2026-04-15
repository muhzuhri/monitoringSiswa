<?php

namespace App\Http\Controllers;

use App\Contracts\HasRole;
use App\Mail\AccountCreatedMail;
use App\Models\Admin;
use App\Models\Pembimbing;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $tahunAjarans = \App\Models\TahunAjaran::where('status', 'aktif')->get();
        $lokasis = \App\Models\LokasiAbsensi::where('is_active', true)->get();
        return view('auth.register', compact('tahunAjarans', 'lokasis'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attr, $value, $fail) {
                    if (
                        Siswa::where('email', $value)->exists()
                        || Guru::where('email', $value)->exists()
                        || Pembimbing::where('email', $value)->exists()
                        || Admin::where('email', $value)->exists()
                    ) {
                        $fail('Email sudah terdaftar.');
                    }
                },
            ],
            'no_hp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            // Registrasi hanya diperbolehkan untuk siswa dan guru
            'role' => ['required', 'in:siswa,guru'],
            'id_tahun_ajaran' => ['required', 'exists:tahun_ajaran,id_tahun_ajaran'],

            // Fields for Siswa
            'nisn' => ['nullable', 'string', 'max:20', 'unique:siswa,nisn'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'sekolah_siswa' => ['required_if:role,siswa', 'nullable', 'string', 'max:150'],
            'npsn_siswa' => ['required_if:role,siswa', 'nullable', 'string', 'exists:sekolah,npsn'],
            'surat_balasan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'perusahaan' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tipe_magang' => ['nullable', 'in:individu,kelompok'],
            'nisn_ketua' => ['nullable', 'string', 'max:20'],
            'tgl_mulai_magang' => ['nullable', 'date'],
            'tgl_selesai_magang' => ['nullable', 'date', 'after_or_equal:tgl_mulai_magang'],

            // Fields for Guru
            'id_guru' => ['nullable', 'string', 'max:50', 'unique:guru,id_guru'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'sekolah_guru' => ['required_if:role,guru', 'nullable', 'string', 'max:150'],
            'npsn_guru' => ['required_if:role,guru', 'nullable', 'string', 'exists:sekolah,npsn'],
        ]);

        $role = $validated['role'];

        if ($role === 'siswa') {
            $request->validate([
                'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn'],
                'kelas' => ['required', 'string', 'max:50'],
                'jurusan' => ['required', 'string', 'max:100'],
                'sekolah_siswa' => ['required', 'string', 'max:150'],
                'npsn_siswa' => ['required', 'string', 'exists:sekolah,npsn'],
                'surat_balasan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                'perusahaan' => ['required', 'string', 'max:150'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'tipe_magang' => ['required', 'in:individu,kelompok'],
                'nisn_ketua' => ['required_if:tipe_magang,kelompok', 'nullable', 'string', 'max:20'],
                'tgl_mulai_magang' => ['required', 'date'],
                'tgl_selesai_magang' => ['required', 'date', 'after_or_equal:tgl_mulai_magang'],
            ]);
        } elseif ($role === 'guru') {
            $request->validate([
                'id_guru' => ['required', 'string', 'max:50', 'unique:guru,id_guru'],
                'jabatan' => ['required', 'string', 'max:100'],
                'sekolah_guru' => ['required', 'string', 'max:150'],
                'npsn_guru' => ['required', 'string', 'exists:sekolah,npsn'],
            ]);
        }

        $common = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => $validated['password'],
        ];

        $roleLabels = [
            'siswa' => 'Siswa Magang',
            'guru' => 'Guru Pembimbing',
        ];

        $user = match ($role) {
            'siswa' => Siswa::create(array_merge($common, [
                'nisn' => $request->input('nisn'),
                'kelas' => $request->input('kelas'),
                'jurusan' => $request->input('jurusan'),
                'sekolah' => \App\Models\Sekolah::where('npsn', $request->input('npsn_siswa'))->first()->nama_sekolah,
                'npsn' => $request->input('npsn_siswa'),
                'surat_balasan' => $request->hasFile('surat_balasan')
                    ? $request->file('surat_balasan')->store('surat_balasan', 'public')
                    : null,
                'perusahaan' => $request->input('perusahaan'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'id_tahun_ajaran' => $request->input('id_tahun_ajaran'),
                'tipe_magang' => $request->input('tipe_magang'),
                'nisn_ketua' => $request->input('tipe_magang') === 'individu'
                    ? $request->input('nisn')
                    : $request->input('nisn_ketua'),
                'tgl_mulai_magang' => $request->input('tgl_mulai_magang'),
                'tgl_selesai_magang' => $request->input('tgl_selesai_magang'),
                'status' => 'aktif',
            ])),
            'guru' => Guru::create(array_merge($common, [
                'id_guru' => $request->input('id_guru'),
                'jabatan' => $request->input('jabatan'),
                'sekolah' => \App\Models\Sekolah::where('npsn', $request->input('npsn_guru'))->first()->nama_sekolah,
                'npsn' => $request->input('npsn_guru'),
                'id_tahun_ajaran' => $request->input('id_tahun_ajaran'),
            ])),
        };

        try {
            Mail::to($user->email)->send(new AccountCreatedMail(
                $user->nama,
                $user->email,
                $roleLabels[$role]
            ));
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi registrasi: ' . $e->getMessage(), [
                'email' => $user->email,
            ]);
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login. Cek email untuk konfirmasi.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $role = $user instanceof HasRole ? $user->getRole() : null;

            return match ($role) {
                'siswa' => redirect()->route('siswa.siswa'),
                'guru' => redirect()->route('guru.guru'),
                'pembimbing' => redirect()->route('pembimbing.pembimbing'),
                'admin' => redirect()->route('admin.admin'),
                'pimpinan' => redirect()->route('pimpinan.home'),
                default => redirect()->route('login'),
            };
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Redirect ke halaman sesuai peran setelah login.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $role = $user instanceof HasRole ? $user->getRole() : null;
        return match ($role) {
            'siswa' => redirect()->route('siswa.siswa'),
            'guru' => redirect()->route('guru.guru'),
            'pembimbing' => redirect()->route('pembimbing.pembimbing'),
            'admin' => redirect()->route('admin.admin'),
            'pimpinan' => redirect()->route('pimpinan.home'),
            default => redirect()->route('login'),
        };
    }

    public function siswa()
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'siswa', 403);
        return view('siswa.siswa', ['user' => $user]);
    }



    public function pembimbing()
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'pembimbing', 403);
        return view('pembimbing.pembimbing', ['user' => $user]);
    }

    public function admin()
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);
        return view('admin.admin', ['user' => $user]);
    }
}
