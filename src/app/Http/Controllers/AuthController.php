<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;

class AuthController extends Controller
{
    public function create ()
    {
        $name = Auth::user()->name;
        $date = now()->toDateString();
        /*$workStarted = Auth::user()->attendances()->whereDate('date', $date)->exists();*/
        return view('index', compact('name','date',));
    }
    public function store (Request $request)
    {
        $user_id = Auth::user()->id;
        $action = $request->input('action');
        $date = $request->input('date');

        if ($action === 'work_start') {
            Attendance::updateOrCreate(
                ['user_id' => $user_id, 'date' => $date],
                ['work_start' => now(),]
            );
        }elseif ($action === 'work_end') {
            Attendance::updateOrCreate(
                ['user_id' => $user_id, 'date' => $date],
                ['work_end' => now(),]
            );
        }elseif ($action === 'break_start') {
            Attendance::updateOrCreate([
                'user_id' => $user_id,
                'break_start' => now(),
            ]);
        }elseif ($action === 'break_end') {
            Attendance::updateOrCreate([
                'user_id' => $user_id,
                'break_end' => now(),
            ]);
        }

        return redirect()->back();
    }

    public function index()
    {
        return view('attendance');
    }
}
