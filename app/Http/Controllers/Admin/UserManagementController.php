<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * UserManagementController — Fase 2.2: Manajemen User (Admin Only)
 * CRUD: daftar, tambah, edit, hapus (soft delete), restore, toggle status
 */
class UserManagementController extends Controller
{
    /**
     * Daftar semua user (termasuk yang di-soft delete).
     */
    public function index(Request $request)
    {
        $query = User::withTrashed()
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderByRaw('deleted_at IS NOT NULL, created_at DESC')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('query'));
    }

    /**
     * Form tambah user baru.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email'    => 'required|email|max:100|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,kasir',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'username.unique'    => 'Username sudah dipakai, pilih yang lain.',
            'username.alpha_dash'=> 'Username hanya boleh huruf, angka, dan tanda (-_).',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            ...collect($validated)->except('password', 'password_confirmation')->toArray(),
            'password'  => $validated['password'],
            'is_active' => true,
        ]);

        ActivityLog::log('Tambah User', "Admin membuat akun user baru: [{$user->username}] (Role: {$user->role})", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "User <strong>{$user->name}</strong> berhasil ditambahkan!");
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,kasir',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $updateData = collect($validated)
            ->except(['password', 'password_confirmation'])
            ->toArray();

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        ActivityLog::log('Edit User', "Admin mengubah data user: [{$user->username}]", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "Data user <strong>{$user->name}</strong> berhasil diperbarui!");
    }

    /**
     * Soft delete user (tidak dihapus permanen dari DB).
     * Admin tidak bisa menghapus dirinya sendiri.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        ActivityLog::log('Hapus User', "Admin melakukan soft-delete user: [{$user->username}]", $user);
        $user->delete();

        return back()->with('success', "User <strong>{$user->name}</strong> berhasil dihapus (dapat dipulihkan).");
    }

    /**
     * Restore user yang sudah di-soft delete.
     */
    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::log('Restore User', "Admin memulihkan user: [{$user->username}]", $user);

        return back()->with('success', "User <strong>{$user->name}</strong> berhasil dipulihkan!");
    }

    /**
     * Aktifkan/nonaktifkan akun user.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri!');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('Toggle Status User', "Admin {$status} akun user: [{$user->username}]", $user);

        return back()->with('success', "User <strong>{$user->name}</strong> berhasil {$status}.");
    }
}
