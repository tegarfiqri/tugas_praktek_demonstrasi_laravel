<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', $this->viewData(null));
    }

    public function edit(User $user)
    {
        return view('users.index', $this->viewData($user));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        if (($data['password'] ?? '') === '' || $data['password'] === null) {
            unset($data['password']); // password tidak diganti
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna dihapus.');
    }

    private function validated(Request $request, ?User $editing): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($editing?->id),
            ],
            'password' => $editing
                ? ['nullable', 'string', 'min:6']
                : ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,dokter,apoteker,kasir'],
            'dokter_id' => ['nullable', 'integer', 'exists:dokter,id'],
        ]);

        // Tautan ke master dokter hanya berlaku untuk role dokter.
        if ($data['role'] !== 'dokter') {
            $data['dokter_id'] = null;
        }

        return $data;
    }

    private function viewData(?User $editing): array
    {
        return [
            'rows' => User::with('dokter')->orderBy('name')->get(),
            'editing' => $editing,
            'daftarDokter' => Dokter::orderBy('nama')->get(),
        ];
    }
}
