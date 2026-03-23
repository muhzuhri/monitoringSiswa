<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Admin;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminGuruController extends Controller
{
    protected function authorizeAdmin(): Admin
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);

        return $user;
    }

    public function kelolaGuru()
    {
        $admin = $this->authorizeAdmin();
        $guru = Guru::with('siswas')->orderBy('nama')->paginate(10);

        return view('admin.kelolaGuru', [
            'user' => $admin,
            'guru' => $guru,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('guru', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'id_guru' => ['required', 'string', 'max:50', Rule::unique('guru', 'id_guru')],
            'jabatan' => ['required', 'string', 'max:100'],
            'sekolah' => ['required', 'string', 'max:150'],
        ]);

        Guru::create($validated);

        return redirect()
            ->route('admin.kelolaGuru')
            ->with('success', 'Akun guru berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('guru', 'email')->ignore($guru->id_guru, 'id_guru')],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'jabatan' => ['required', 'string', 'max:100'],
            'sekolah' => ['required', 'string', 'max:150'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $guru->update($validated);

        return redirect()
            ->route('admin.kelolaGuru')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return redirect()
            ->route('admin.kelolaGuru')
            ->with('success', 'Akun guru berhasil dihapus.');
    }
}
