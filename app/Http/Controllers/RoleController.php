<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\support\Facades\DB;
use Illuminate\support\Facades\Auth;

class RoleController extends Controller
{
    public function listRole()
    {
        $PermissionRole = Permission::getPermission('Role', Auth::user()->role_id);
        if(empty($PermissionRole)){
            abort(404);
        }
        $data['PermissionTambah'] = Permission::getPermission('Tambah Role', Auth::user()->role_id);
        $data['PermissionEdit'] = Permission::getPermission('Edit Role', Auth::user()->role_id);
        $data['PermissionDelete'] = Permission::getPermission('Delete Role', Auth::user()->role_id);

        $data['data'] = DB::table('roles')->orderBy('name','ASC')->get();
        return view('role.listRole',$data);
    }
    public function tambahRole()
    {

        $getPermission = Permission::getRecord();
        return view('role.tambahRole',compact('getPermission'));
    }
    public function storeRole(Request $request)
    {

        $role_id = DB::table('roles')->insertGetId([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        foreach($request->permission_id as $value) {
            DB::table('permissions_roles')->insert([
                'permission_id' => $value,
                'role_id' => $role_id,
            ]);
        }
        return redirect('listRole')->with('success', 'Role berhasil dihapus!');
    }

    public function editRole($id)
    {

        $data = DB::table('roles')->where('id',$id)->first();
        $getPermission = Permission::getRecord();
        return view('role.editRole',compact('getPermission','data'));
    }

    public function updateRole(Request $request, $id)
    {
        DB::table('permissions_roles')->where('role_id',$id)->delete();
        foreach($request->permission_id as $value) {
            DB::table('permissions_roles')->insert([
                'permission_id' => $value,
                'role_id' => $id,
            ]);
        }
        return redirect('listRole')->with('success', 'Role berhasil dihapus!');
    }


    public function deleteRole($id)
    {
        try {
            DB::table('roles')->where('id',$id)->delete();
            return redirect()->back()->with('success', 'Role berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
    }


}
