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
    protected $id;
    protected $name;
    protected $date;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //ログイン中のユーザーのid・name,今日の日付を取得
            $this->id = Auth::id();
            $this->name = Auth::user()->name;
            $this->date = now()->toDateString();

            return $next($request);
        });
    }
    public function create()
    {
        //ログインユーザーが本日の勤務・休憩レコードを取得している場合、最初のデータを取得する
        $attendance = Attendance::where('user_id', $this->id)->whereDate('date', $this->date)->latest()->first();
        $rest = Rest::where('user_id', $this->id)->latest()->whereDate('date', $this->date)->first();

        //勤務開始・勤務終了・休憩開始・休憩終了時間が設定されているかを確認する
        $workStart = $attendance ? $attendance->work_start !== null : false;
        $workEnd = $attendance ? $attendance->work_end !== null : false;
        $restStart = $rest ? $rest->rest_start !== null : false;
        $restEnd = $rest ? $rest->rest_end !== null : false;

        return view('index', [
            'name' => $this->name,
            'date' => $this->date,
            'workStart' => $workStart,
            'workEnd' => $workEnd,
            'restStart' => $restStart,
            'restEnd' => $restEnd
        ]);

    }

    public function store(Request $request)
    {
        $action = $request->input('action');

        //各action毎の処理を記述
        // --work_start--
        if ($action === 'work_start') {
            //work_startを1日1度しか取得できないようにする
            $workStart = Attendance::where('user_id', $this->id)
                ->whereDate('date', $this->date)
                ->whereNotNull('work_start')
                ->first();
            //work_startのデータを取得している場合は何もせず、していない場合はwork_startデータを取得する
            if ($workStart) {
            } else {
                Attendance::create([
                    'user_id' => $this->id,
                    'work_start' => now(),
                    'date' => $this->date
                ]);
                $message = $this->name . 'さん、今日も一日頑張りましょう！';
                $message2 = '';
            }
            // --work_end--
        } elseif ($action === 'work_end') {
            //work_endからデータを取得すると現実的ではないので、work_startのレコードを取得しているか確認しておく
            $attendance = Attendance::where('user_id', $this->id)
                ->whereNotNull('work_start')
                ->latest()
                ->first();
            //勤務開始している場合はwork_endデータを取得する
            if ($attendance) {
                $attendance->update(['work_end' => now()]);
                $message = '本日も1日お疲れ様でした!';
                $message2 = '※勤務終了後は背景が黄色になります';
                session()->put('message_type', 'work_end');
            }
            // --rest_start--
        } elseif ($action === 'rest_start') {
            $attendance = Attendance::where('user_id', $this->id)
                ->whereDate('date', $this->date)
                ->latest()
                ->first();
            //ログインユーザーが今日の日付でwork_startのデータを取得している場合、rest_startのデータを取得する
            if ($attendance && $attendance->id) {
                Rest::create([
                    'user_id' => $this->id,
                    'attendance_id' => $attendance->id,
                    'rest_start' => now(),
                    'date' => $this->date,
                ]);
                $message = '休憩開始！ゆっくり休みましょう！';
                $message2 = '';
            } else {
            }
            // --rest_end--
        } elseif ($action === 'rest_end') {
            $rest = Rest::where('user_id', $this->id)
                ->whereNotNull('rest_start')
                ->latest()
                ->first();
            //最新のrest_startレコードを更新してrest_endデータを取得する
            if ($rest) {
                $rest->update(['rest_end' => now()]);
                $message = '休憩終了！お仕事頑張りましょう！';
                $message2 = '';
            }
        }

        if (!isset($message2)) {
            $message2 = '※勤務終了後は背景が黄色になります';
        }
        return redirect()->back()->with([
            'message' => $message,
            'message2' => $message2
        ]);
    }

    //attendanceから取得するレコードを関数化して再利用する
    private function calculateWorkAndRestTime($attendance, $date)
    {
        $date = $attendance->date;
        $workStart = new DateTime($attendance->work_start);
        $workEnd = new DateTime($attendance->work_end);

        // 勤務時間の計算
        $workDiff = $workEnd->diff($workStart);
        $workTimeInterval = $workDiff->s + $workDiff->i * 60 + $workDiff->h * 3600;
        $workTime = gmdate("H:i:s", $workTimeInterval);

        // 休憩時間の計算
        $restTimeSeconds = Rest::where('user_id', $attendance->user_id)
            ->whereDate('date', $date)
            ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(rest_end, rest_start))'));
        $restTime = gmdate("H:i:s", $restTimeSeconds);

        return [
            'date' => $date,
            'workStart' => $workStart->format('H:i:s'),
            'workEnd' => $workEnd->format('H:i:s'),
            'restTime' => $restTime,
            'workTime' => $workTime,
        ];
    }

    public function index($date = null)
    {
        //日付が指定されていない場合は最新の日付を取得する
        if (!$date) {
            $date = Attendance::latest()->value('date');
        }

        //最新の日付の前後の日付を取得
        $previous = Attendance::whereDate('date', '<', $date)->orderBy('date', 'desc')->value('date');
        $next = Attendance::whereDate('date', '>', $date)->orderBy('date', 'asc')->value('date');

        // Attendanceモデルのdateとパラメータのdateが一致するデータを取得する
        $attendances = Attendance::whereDate('date', $date)
            ->select('date', 'user_id', 'work_start', 'work_end')
            ->with('rests')
            ->get();

        foreach ($attendances as $attendance) {
            //パラメータのdateのユーザーid
            $user = User::find($attendance->user_id);
            $calcResults = $this->calculateWorkAndRestTime($attendance, $date);

            $dates[] = [
                'name' => $user->name,
                'workStart' => $calcResults['workStart'],
                'workEnd' => $attendance->work_end ? $calcResults['workEnd'] : '打刻漏れ',
                'restTime' => $calcResults['restTime'],
                'workTime' => $attendance->work_end ? $calcResults['workTime'] : '未取得'
            ];

            //ページネーション処理
            $collection = collect($dates);
            $query = $attendance->date;
            $path = '/attendance/' . $query;
            $datesPaginate = $this->paginate($collection, 5, null, ['path' => $path]);
        }
        return view('attendance', compact('date', 'previous', 'next', 'datesPaginate'));
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

    //attendancesから得たデータを配列化
    private function items($attendances, $month)
    {
        $items = [];

        foreach ($attendances as $attendance) {
            $calcResults = $this->calculateWorkAndRestTime($attendance, $month);

            $items[] = [
                'date' => $calcResults['date'],
                'workStart' => $calcResults['workStart'],
                'workEnd' => $calcResults['workEnd'],
                'restTime' => $calcResults['restTime'],
                'workTime' => $calcResults['workTime'],
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at
            ];
        }
        return $items;
    }

    public function show($id, $month = null)
    {
        if (!$month) {
            $latestDate = Attendance::latest('created_at')->value('created_at');
            $month = Carbon::parse($latestDate)->format('Y-m');
        } else {
            $latestDate = Carbon::parse($month)->format('Y-m-d');
        }
        $previous = Carbon::parse($latestDate)->subMonth()->format('Y-m');
        $next = Carbon::parse($latestDate)->addMonth()->format('Y-m');

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date')
            ->get();

        $user = User::findOrFail($id);
        $name = User::where('id', $id)->value('name');

        $items = $this->items($attendances, $month);

        return view('detail', compact('user', 'items', 'name', 'month', 'previous', 'next'));
    }

    public function export($id, $month)
    {
        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date')
            ->get();

            $items = $this->items($attendances, $month);

        $csvHeader = [
            'date', 'work_start', 'work_end', 'total_rest_time', 'total_work_time', 'created_at', 'updated_at'
        ];
        $filename = "attendance_{$month}_user{$id}.csv";
        // レスポンスとしてCSVファイルを返す処理
        $response = new StreamedResponse(
            function () use ($csvHeader, $items) {
                $createdCsvFile = fopen('php://output', 'w');
                // ヘッダーをCSVファイルに書き込む
                fputcsv($createdCsvFile, $csvHeader);
                // データをCSVファイルに書き込む
                foreach ($items as $csvRow) {
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
