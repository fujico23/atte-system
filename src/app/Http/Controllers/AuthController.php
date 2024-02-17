<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use DateTime;

class AuthController extends Controller
{
    public function create()
    {
        $name = Auth::user()->name;
        $loginUser_id = Auth::id();
        $date = now()->toDateString();

        //AttendanceモデルとRestモデルのuser_idとログインユーザーのidが一致する最新の最初のレコードを取得
        $attendance = Attendance::where('user_id', $loginUser_id)->whereDate('date', $date)->latest()->first();
        $rest = Rest::where('user_id', $loginUser_id)->latest()->whereDate('date', $date)->first();

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
        $name = Auth::user()->name;

        if ($action === 'work_start') {
            //1日1度しか勤務開始を取得できないようにする処理
            $existWorkStart = Attendance::where('user_id', $user_id)
                ->whereDate('date', now()->toDateString())
                ->whereNotNull('work_start')
                ->first();
            //勤務開始してる場合としていない場合の処理
            if ($existWorkStart) {
                //時刻を更新する場合は$existWorkStart->update(['work_start' => now()]);
            } else { //もしなければwork_startを取得する
                Attendance::create([
                    'user_id' => $user_id,
                    'work_start' => now(),
                    'date' => now()->toDateString()
                ]);
                $message = $name . 'さん、今日も一日頑張りましょう！';
            }
        } elseif ($action === 'work_end') {
            //勤務開始しているか確認する
            $attendance = Attendance::where('user_id', $user_id)
                ->whereNotNull('work_start')
                ->latest()
                ->first();
            //勤務開始している場合としていない場合の処理
            if ($attendance) {
                $attendance->update(['work_end' => now()]);
                $message = $name . 'さん、１日お疲れ様でした！';
            }
        } elseif ($action === 'rest_start') {
            $attendance = Attendance::where('user_id', $user_id)
                ->whereDate('date', now()->toDateString())
                ->latest()
                ->first();
            if ($attendance && $attendance->id) {
                Rest::create([
                    'user_id' => $user_id,
                    'attendance_id' => $attendance->id,
                    'rest_start' => now(),
                    'date' => now()->toDateString(),
                ]);
                $message = '休憩開始！ゆっくり休みましょう！';
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
                $message = '休憩終了！お仕事頑張りましょう！';
            }
        }
        return redirect()->back()->with('message', $message);
    }

    public function index($date = null)
    {
        //日付が指定されていない場合は最新の日付を使用
        if (!$date) {
            $date = Attendance::latest()->value('date');
        }
        //前後の日付を取得
        $previousDate = Attendance::whereDate('date', '<', $date)->orderBy('date', 'desc')->value('date');
        $nextDate = Attendance::whereDate('date', '>', $date)->orderBy('date', 'asc')->value('date');
        // Attendanceモデルのdateとパラメータのdateが一致するdate,user_id,work_start,work_endのレコードを取得する
        $attendances = Attendance::whereDate('date', $date)
            ->select('date', 'user_id', 'work_start', 'work_end')
            ->with('rests')
            ->get();

        foreach ($attendances as $attendance) {
            //パラメータのdateのユーザーid
            $user = User::find($attendance->user_id);
            //パラメータのdateの勤務開始時間と勤務終了時間

            $work_start = new DateTime($attendance->work_start);
            $work_end = new DateTime($attendance->work_end);
            //if ($work_start && $work_end) {
            $work_diff = $work_end->diff($work_start);
            $work_time_interval = $work_diff->s + $work_diff->i * 60 + $work_diff->h * 3600;
            $total_work_time = gmdate("H:i:s", $work_time_interval);
            //}
            //Restモデルからuser_idとattendanceのuser_idが一致するものを探し
            $total_rest_time = Rest::where('user_id', $attendance->user_id)
                ->whereDate('date', $date)
                //->whereDate('date', Carbon::today()->toDateString())
                ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(rest_end, rest_start))'));


            $dates[] = [
                'name' => $user->name,
                'work_start' => Carbon::parse($attendance->work_start)->format('H:i:s'),
                'work_end' => $attendance->work_end ? Carbon::parse($attendance->work_end)->format('H:i:s') : '打刻漏れ',
                'total_rest_time' => $total_rest_time,
                'total_work_time' => $attendance->work_end ? $total_work_time : '未取得'
            ];

            //ページネーション処理
            $collection = collect($dates);
            $query = $attendance->date;
            $path = '/attendance/' . $query;
            $datesPaginate = $this->paginate($collection, 5, null, ['path' => $path]);
        }
        return view('attendance', compact('date', 'previousDate', 'nextDate', 'datesPaginate'));
    }
    //ページネーションメソッド
    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function list()
    {
        $users = User::all();
        return view('list', compact('users'));
    }

    public function show($id, $month = null)
    {
        $month = $month ? Carbon::parse($month) : Carbon::now()->startOfMonth();
        //今月
        $now = Carbon::now()->startOfMonth();
        $Month = Carbon::now()->startOfMonth()->addMonthNoOverflow()->subSecond(1);
        $thisMonth = Attendance::whereBetween('created_at', array($now, $Month));


        $attendances = Attendance::with('rests')->where('user_id', $id)->get();
        $name = User::where('id', $id)->value('name');
        $items = [];

        foreach ($attendances as $attendance) {
            $date = $attendance->date;

            $work_start = Carbon::parse($attendance->work_start);
            $work_end = Carbon::parse($attendance->work_end);
            $work_diff = $work_end->diffInSeconds($work_start);
            $total_work_time = gmdate("H:i:s", $work_diff);

            $total_rest_time = Rest::where('user_id', $attendance->user_id)
            ->whereDate('date', $date)
            ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(rest_end, rest_start))'));

            $items[] = [
                'date' => $date,
                'work_start' => Carbon::parse($attendance->work_start)->format('H:i:s'),
                'work_end' => $attendance->work_end ? Carbon::parse($attendance->work_end)->format('H:i:s') : '打刻漏れ',
                'total_rest_time' => $total_rest_time,
                'total_work_time' => $attendance->work_end ? $total_work_time : '未取得'
            ];
        }



        return view('detail', compact('items','name','month'));
    }
}
