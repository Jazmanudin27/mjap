<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index()
    {
        $data['PermissionTambah'] = Permission::getPermission('Tambah Users', Auth::user()->role_id);

        return view('users.index', $data);
    }

    public function detailUsers(Request $request)
    {
        $data['PermissionEdit'] = Permission::getPermission('Edit Users', Auth::user()->role_id);
        $data['PermissionDelete'] = Permission::getPermission('Delete Users', Auth::user()->role_id);

        $query = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('hrd_karyawan', 'users.nik', '=', 'hrd_karyawan.nik')
            ->select('users.*', 'roles.name as role_name', 'users.last_activity','nama_lengkap')
            ->orderBy('users.name', 'ASC');

        // Filter berdasarkan Nama Lengkap
        if ($request->filled('nama_lengkap')) {
            $query->where('users.name', 'LIKE', '%' . $request->nama_lengkap . '%');
        }

        // Filter berdasarkan Email
        if ($request->filled('email')) {
            $query->where('users.email', 'LIKE', '%' . $request->email . '%');
        }

        // Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('users.status', '=', $request->status);
        }

        $data['data'] = $query->get();

        return view('users.detail', $data);
    }


    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'nik' => $request->nik,
            'status' =>'Aktif',
        ]);

        logActivity('Tambah User','User '. $request->name.' di tambahkan');
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

        $data = $request->only('name', 'email', 'role_id', 'nik');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        logActivity('Update User','User '. $request->name.' di update');

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');

    }

    public function destroy(User $user)
    {
        $user->delete();
        logActivity('Hapus User','User '. $user->id.' di non hapus');
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Toggle status
        $user->status = $user->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
        $user->save();
        logActivity('Non Aktif User','User '. $user->id.' di non aktifkan');

        // Jika user yang dinonaktifkan adalah user yang sedang login, maka logout otomatis
        if ($user->status == 'Nonaktif' && Auth::id() == $user->id) {
            Auth::logout();
            return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan');
        }

        return redirect()->back()->with('success', 'Status user berhasil diperbarui');
    }

}
