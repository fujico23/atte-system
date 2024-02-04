<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
                if ($request->input('action') === 'work_end') {
                    $date = $request->input('date');
                    $user_id = auth()->user()->id;

                    $attendance = Attendance::where('user_id', $user_id)
                        ->where('date', $date)
                        ->first();

                    if (!$attendance || !$attendance->work_start) {
                        return redirect()->back()->with('error', '勤務開始が記録されていません。先に勤務開始ボタンを押してください。');
                    }
                }

        return $next($request);
    }
}
