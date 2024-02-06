<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Carbon;
use DateTime;

class AuthController extends Controller
{
    public function create()
    {
        $name = Auth::user()->name;
        $user_id = Auth::id();
        $date = now()->toDateString();

        //AttendanceモデルとRestモデルのuser_idとログインユーザーのidが一致する最新の最初のレコードを取得
        $attendance = Attendance::where('user_id', $user_id)->whereDate('date', $date)->latest()->first();

        $rest = Rest::where('user_id', $user_id)->latest()->first();

        // 勤務開始・休憩開始・休憩終了時間が設定されているかを確認する
        $work_start_defined = $attendance ? $attendance->work_start !== null : false;
        $work_end_defined = $attendance ? $attendance->work_end !== null : false;
        $rest_start_defined = $rest ? $rest->rest_start !== null : false;
        $rest_end_defined = $rest ? $rest->rest_end !== null : false;

        return view('index', compact('name', 'date', 'work_start_defined', 'work_end_defined', 'rest_start_defined', 'rest_end_defined'));
    }

    public function store(Request $request)
    {
        $action = $request->input('action');
        $user_id = Auth::id();

        if ($action === 'work_start') {
            //1日1度しか勤務開始を取得できないようにする処理
            $existWorkStart = Attendance::where('user_id', $user_id)
                ->whereDate('date', now()->toDateString())
                ->whereNotNull('work_start')
                ->first();
            //もしあれば何もしない
            if ($existWorkStart) {
                //時刻を更新する場合は$existWorkStart->update(['work_start' => now()]);
            } else { //もしなければwork_startを取得する
                Attendance::create([
                    'user_id' => $user_id,
                    'work_start' => now(),
                    'date' => now()->toDateString()
                ]);
            }
        } elseif ($action === 'work_end') { //勤務終了処理
            //nullじゃない勤務開始データを取得(勤務開始が押されているか確認)
            $attendance = Attendance::where('user_id', $user_id)
                ->whereNotNull('work_start')
                ->latest()
                ->first();
            if ($attendance) { //勤務開始されているなら勤務終了を更新出来る
                $attendance->update(['work_end' => now()]);
            }
        } elseif ($action === 'rest_start') {
            $attendance = Attendance::where('user_id', $user_id)->whereDate('date', now()->toDateString())->latest()->first();
            if ($attendance && $attendance->id) {
                Rest::create([
                    'user_id' => $user_id,
                    'attendance_id' => $attendance->id,
                    'rest_start' => now(),
                    'date' => now()->toDateString(),
                ]);
            } else {
                //attendance_idがない場合の処理を定義。エラー処理を行うか、何もしないか…
            }
        } elseif ($action === 'rest_end') {
            $rest = Rest::where('user_id', $user_id)
                ->whereNotNull('rest_start')
                ->latest()
                ->first();
            if ($rest) {
                $rest->update(['rest_end' => now()]);
            }
        }
        return redirect()->back();
    }

    public function index($date = null)
    {
        //日付が指定されていない場合は今日の日付を使用
        if (!$date) {
            $date = now()->format('Y-m-d');
        }

        // Attendanceモデルからdateカラムとリクエストのdateが一致する今日の日付が一致するものを探し
        //date,user_id,work_start,work_endのレコードを取得する
        $attendances = Attendance::whereDate('date', $date)
            ->select('date', 'user_id', 'work_start', 'work_end')
            ->groupBy('date', 'user_id', 'work_start', 'work_end')
            ->get();
        $newestDate = Attendance::latest('date')->value('date');
        //前後の日付を取得
        $previousAttendance = Attendance::whereDate('date', '<', $date)->orderBy('date', 'desc')->value('date'); //前日の日付
        $nextAttendance = Attendance::whereDate('date', '>', $date)->orderBy('date', 'asc')->first();


        foreach ($attendances as $attendance) {
            // 今日の日付
            $date = $attendance->date;
            // 今日のユーザーid
            $user = User::find($attendance->user_id);
            // 今日の勤務開始時間と勤務終了時間
            $work_start = new DateTime($attendance->work_start);
            $work_end = new DateTime($attendance->work_end);
            if ($work_start && $work_end) {
                $work_time = $work_end->diff($work_start);
                $work_time_interval = $work_time->s + $work_time->i * 60 + $work_time->h * 3600;
                $total_work_time = gmdate("H:i:s", $work_time_interval);
            }
            //Restモデルからuser_idとattendanceのuser_idが一致するものを探し
            $total_rest_time = Rest::where('user_id', $attendance->user_id)
                ->whereDate('date', Carbon::today()->toDateString())
                ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(rest_end, rest_start))'));



            $dates[] = [
                //'date' => $date,
                'name' => $user->name,
                'work_start' => Carbon::parse($attendance->work_start)->format('H:i:s'),
                'work_end' => Carbon::parse($attendance->work_end)->format('H:i:s'),
                'total_rest_time' => $total_rest_time,
                'total_work_time' => $total_work_time
            ];
        }
        return view('attendance', compact('dates', 'date', 'newestDate', 'previousAttendance', 'nextAttendance'));
    }

    public function previousDate()
    {
        $previousDate = Carbon::now()->subDay()->format('Y-m-d');

        return $this->index($previousDate);
    }
    public function nextDay()
    {
        $nextDate = Carbon::today()->addDay()->format('Y-m-d');
        return redirect()->route('attendance.index', ['date' => $nextDate]);
    }
}
