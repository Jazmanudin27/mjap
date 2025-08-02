<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class ActivityLogController extends Controller
{
    public function index()
    {
        return view('activity_logs.index');
    }
    public function showLogs(Request $request)
    {
        $user_id = $request->id;
        $data['logs'] = DB::table('activity_logs')
        ->join('users','users.id','activity_logs.user_id')
        ->when($user_id, function ($query) use ($user_id) {
            return $query->where('activity_logs.id',  $user_id);
        })
        ->paginate(10);
        return view('activity_logs.show', $data);
    }
}
