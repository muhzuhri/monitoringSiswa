<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Admin;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminSiswaController extends Controller
{
    protected function authorizeAdmin(): Admin
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);

        return $user;
    }

    public function kelolaSiswa()
    {
        $admin = $this->authorizeAdmin();
        $siswa = Siswa::with(['guru', 'pembimbing'])->orderBy('nama')->paginate(10);
        $gurus = \App\Models\Guru::orderBy('nama')->get();
        $pembimbings = \App\Models\Pembimbing::orderBy('nama')->get();

        return view('admin.kelolaSiswa', [
            'user' => $admin,
            'siswa' => $siswa,
            'gurus' => $gurus,
            'pembimbings' => $pembimbings,
        ]);
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
