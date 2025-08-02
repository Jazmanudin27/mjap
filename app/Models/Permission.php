<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    static public function getSigle($id){

        return Permission::find($id);
    }

    static public function getRecord(){

        $getPermission = Permission::groupBy('groupby')->get();
        $result = array();
        foreach ($getPermission as $value){

            $getPermissionGroup = Permission::getPermissionGroup($value->groupby);
            $data = array();
            $data['id'] = $value->id;
            $data['name'] = $value->name;
            $group = array();
            foreach ($getPermissionGroup as $valueG) {
                $dataG = array();
                $dataG['id'] = $valueG->id;
                $dataG['name'] = $valueG->name;
                $group[] = $dataG;
            }
            $data['group'] = $group;
            $result[] = $data;
        }

        return $result;
    }

    static public function getPermissionGroup($groupby){

        return Permission::where('groupby',$groupby)->get();
    }

    static public function getPermission($slug, $role_id)
    {
        return DB::table('permissions_roles')
        ->select('permissions_roles.id')
        ->join('permissions','permissions.id','permissions_roles.permission_id')
        ->where('permissions_roles.role_id',$role_id)
        ->where('permissions.slug',$slug)
        ->count();

    }
}
