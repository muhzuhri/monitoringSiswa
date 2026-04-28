<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use App\Models\Admin;
use App\Models\TahunAjaran;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminPembimbingController extends Controller
{
    protected function authorizeAdmin(): Admin
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);

        return $user;
    }

    public function kelolaPembimbing()
    {
        $admin = $this->authorizeAdmin();
        $pembimbing = Pembimbing::with('siswas.tahunAjaran')->orderBy('nama')->paginate(10);
        $periods = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.kelolaPembimbing', [
            'user' => $admin,
            'pembimbing' => $pembimbing,
            'periods' => $periods,
        ]);
    }

    public function create()
    {
        $admin = $this->authorizeAdmin();

        return view('admin.pembimbing.create', [
            'user' => $admin,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'id_pembimbing' => ['required', 'string', 'max:50', Rule::unique('pembimbing', 'id_pembimbing')],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pembimbing', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'jabatan' => ['required', 'string', 'max:50'],
            'instansi' => ['required', 'string', 'max:100'],
            'no_telp' => ['required', 'string', 'max:150'],
        ]);

        Pembimbing::create($validated);

        return redirect()
            ->route('admin.kelolaPembimbing')
            ->with('success', 'Akun pembimbing lapangan berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $pembimbing = Pembimbing::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pembimbing', 'email')->ignore($pembimbing->id_pembimbing, 'id_pembimbing')],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'jabatan' => ['required', 'string', 'max:50'],
            'instansi' => ['required', 'string', 'max:100'],
            'no_telp' => ['required', 'string', 'max:150'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $pembimbing->update($validated);

        return redirect()
            ->route('admin.kelolaPembimbing')
            ->with('success', 'Data pembimbing lapangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $pembimbing = Pembimbing::findOrFail($id);

        // Hapus referensi pembimbing lapangan dari siswa terkait
        \App\Models\Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->update([
                'id_pembimbing' => null,
            ]);

        $pembimbing->delete();

        return redirect()
            ->route('admin.kelolaPembimbing')
            ->with('success', 'Akun pembimbing lapangan berhasil dihapus.');
    }
}

