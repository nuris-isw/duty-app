<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\LeaveType;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // CEGAH Unit Admin & User Biasa masuk sini
        if (!Gate::allows('manage-master-data')) {
            abort(403, 'Anda tidak memiliki akses ke menu pengaturan.');
        }

        $currentUser = Auth::user();
        $currentYear = now()->year;
        $annualLeaveType = LeaveType::where('nama_cuti', 'Cuti Tahunan')->first();
        $annualLeaveQuota = $annualLeaveType->kuota ?? 0;

        // Query Dasar
        $usersQuery = User::with(['jabatan', 'unitKerja', 'userLeaveQuotas' => function ($query) use ($annualLeaveType, $currentYear) {
            if ($annualLeaveType) {
                $query->where('leave_type_id', $annualLeaveType->id)->where('tahun', $currentYear);
            }
        }]);

        // PROTEKSI SUPERADMIN:
        // Jika yang login adalah SysAdmin, sembunyikan Superadmin dari list
        if ($currentUser->role === 'sys_admin') {
            $usersQuery->where('role', '!=', 'superadmin');
        }

        $users = $usersQuery->latest()->get();

        return view('admin.users.index', [
            'users' => $users,
            'annualLeaveQuota' => $annualLeaveQuota,
        ]);
    }

    public function create()
    {
        if (!Gate::allows('manage-master-data')) abort(403);

        // Filter Atasan: SysAdmin tidak boleh melihat Superadmin untuk dijadikan atasan
        $superiorsQuery = User::whereIn('role', ['superadmin', 'sys_admin', 'unit_admin']);
        
        if (Auth::user()->role === 'sys_admin') {
            $superiorsQuery->where('role', '!=', 'superadmin');
        }

        return view('admin.users.create', [
            'superiors' => $superiorsQuery->get(),
            'unitKerjas' => UnitKerja::all(),
            'jabatans' => Jabatan::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('manage-master-data')) abort(403);

        // Validasi Role yang boleh diinput
        // SysAdmin tidak boleh membuat user dengan role 'superadmin'
        $allowedRoles = 'sys_admin,unit_admin,user';
        if (Auth::user()->role === 'superadmin') {
            $allowedRoles .= ',superadmin';
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . $allowedRoles, // Validasi dinamis
            'atasan_id' => 'nullable|exists:users,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'jabatan_id' => 'required|exists:jabatans,id', 
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'atasan_id' => $validatedData['atasan_id'],
            'unit_kerja_id' => $validatedData['unit_kerja_id'],
            'jabatan_id' => $validatedData['jabatan_id'], 
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        if (!Gate::allows('manage-master-data')) abort(403);

        // PROTEKSI: SysAdmin tidak boleh edit Superadmin
        if ($user->role === 'superadmin' && Auth::user()->role !== 'superadmin') {
            abort(403, 'Anda tidak berhak mengedit data Superadmin.');
        }

        $superiors = User::whereIn('role', ['superadmin', 'sys_admin', 'unit_admin'])
                        ->where('id', '!=', $user->id);

        if (Auth::user()->role === 'sys_admin') {
            $superiors->where('role', '!=', 'superadmin');
        }

        return view('admin.users.edit', [
            'user' => $user,
            'superiors' => $superiors->get(),
            'unitKerjas' => UnitKerja::all(),
            'jabatans' => Jabatan::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (!Gate::allows('manage-master-data')) abort(403);
        
        // PROTEKSI: SysAdmin tidak boleh update Superadmin
        if ($user->role === 'superadmin' && Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $allowedRoles = 'sys_admin,unit_admin,user';
        if (Auth::user()->role === 'superadmin') {
            $allowedRoles .= ',superadmin';
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:' . $allowedRoles,
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'atasan_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user->update($validatedData);
        } else {
            $user->update($request->except('password'));
        }

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (!Gate::allows('manage-master-data')) abort(403);

        // 1. Tidak boleh hapus diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 2. SysAdmin tidak boleh hapus Superadmin
        if ($user->role === 'superadmin' && Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak berhak menghapus Superadmin.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}