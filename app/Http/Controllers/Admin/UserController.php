<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user dengan filter & pagination
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email_or_phone', 'like', "%$search%");
            });
        }

        // 📌 Filter role
        if ($request->filled('filterRole') && $request->filterRole !== 'Semua') {
            $query->where('role', $request->filterRole);
        }

        // 📌 Sort
        if ($request->filled('sortBy')) {
            switch ($request->sortBy) {
            case 'name':
                    $query->orderBy('name', 'asc'); // A-Z
                    break;

            case 'role':
                    $query->orderBy('role', 'asc');
                    break;
            }
        } else {
            $query->latest();
        }


        $users = $query->paginate(10);

        return view('admin.data-user', compact('users'));
    }

    /**
     * Form tambah user (Admin)
     */
    public function create()
    {
        return view('admin.create-user');
    }

    /**
     * Simpan user baru (Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email_or_phone' => 'required|string|max:255|unique:users,email_or_phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email_or_phone' => $validated['email_or_phone'],
            'password' => Hash::make($validated['password']),
            'role'     => 'admin', // 🔑 otomatis Admin jika dibuat lewat admin
        ]);

        return redirect()->route('admin.user.store')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Edit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email_or_phone' => 'required|string|max:255|unique:users,email_or_phone,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'name'     => $validated['name'],
            'email_or_phone' => $validated['email_or_phone'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return redirect()->route('admin.user.edit', $user->id)->with('success', 'User berhasil diupdate.');
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }
}
