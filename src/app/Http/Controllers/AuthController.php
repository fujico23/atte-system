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
use Illuminate\Support\Facades\Date;
use DateTime;

use Symfony\Component\HttpFoundation\StreamedResponse;

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
                $message = '本日も1日お疲れ様でした!';
                $message2 = '※勤務終了後は背景が黄色になります';
                session()->put('message_type', 'work_end');
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
        return redirect()->back()->with([
            'message' => $message,
            'message2' => $message2
        ]);
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
        $today = Carbon::today();
        $users = User::with(['attendances' => function ($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'work_start' => $user->attendances->pluck('work_start')->first(),
                'work_end' => $user->attendances->pluck('work_end')->first()
            ];
        });
        return view('list', compact('users'));
    }

    public function show($id, $month = null)
    {
        if (!$month) {
            $latestDate = Attendance::latest('created_at')->value('created_at'); // date: 2024-02-29 09:08:44.0 Asia/Tokyo (+09:00)
            $month = Carbon::parse($latestDate)->format('Y-m'); // "2024-02"
        } else {
            $latestDate = Carbon::parse($month)->format('Y-m-d'); //"2024-02-29"
        }
        $previousMonth = Carbon::parse($latestDate)->subMonth()->format('Y-m'); //"2024-01"
        $nextMonth = Carbon::parse($latestDate)->addMonth()->format('Y-m'); //"2024-03"

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date')
            ->get();

        $user = User::findOrFail($id);
        $name = User::where('id', $id)->value('name'); //社員の名前
        $items = [];

        foreach ($attendances as $attendance) {
            $date = $attendance->date; //"2024-02-04 string"
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
                'total_work_time' => $attendance->work_end ? $total_work_time : '未取得',
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at
            ];
        }
        return view('detail', compact('user', 'items', 'name', 'month', 'previousMonth', 'nextMonth'));
    }

    public function export($id, $month)
    {
        $csvData = [];

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date')
            ->get();

        foreach ($attendances as $attendance) {
            $date = $attendance->date; //"2024-02-04 string"
            $work_start = Carbon::parse($attendance->work_start);
            $work_end = Carbon::parse($attendance->work_end);
            $work_diff = $work_end->diffInSeconds($work_start);
            $total_work_time = gmdate("H:i:s", $work_diff);
            $total_rest_time = Rest::where('user_id', $attendance->user_id)
                ->whereDate('date', $date)
                ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(rest_end, rest_start))'));
            $total_rest_time = gmdate("G:i:s", $total_rest_time);
            $csvData[] = [
                'date' => $date,
                'work_start' => Carbon::parse($attendance->work_start)->format('H:i:s'),
                'work_end' => $attendance->work_end ? Carbon::parse($attendance->work_end)->format('H:i:s') : 'null',
                'total_rest_time' => $total_rest_time,
                'total_work_time' => $attendance->work_end ? $total_work_time : 'null',
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->created_at

            ];
        }

        $csvHeader = [
            'date', 'work_start', 'work_end', 'total_rest_time', 'total_work_time', 'created_at', 'updated_at'
        ];
        $filename = "attendance_{$month}_user{$id}.csv";
        // レスポンスとしてCSVファイルを返す
        $response = new StreamedResponse(
            function () use ($csvHeader, $csvData) {
                $createdCsvFile = fopen('php://output', 'w');
                // ヘッダーをCSVファイルに書き込む
                fputcsv($createdCsvFile, $csvHeader);
                // データをCSVファイルに書き込む
                foreach ($csvData as $csvRow) {
                    fputcsv($createdCsvFile, $csvRow);
                }
                fclose($createdCsvFile);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]
        );

        return $response;
    }
}
