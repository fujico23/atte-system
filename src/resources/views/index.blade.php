@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

<div class="content__inner">
    @if(session('message'))
    <h2 class="form__heading">{{ session('message') }}</h2>
    @else
    <h2 class="form__heading">{{ $name }}さん、お疲れ様です！</h2>
    @endif
    @if(session('message2'))
    <span style="color: #778899;">{{ session('message2') }}</span>
    @endif
    <div class="attendance-system">
        <form action="/store" id="attendanceForm" method="post">
            @csrf
            <div class="work__group">
                @if(!$workStart && !$workEnd)
                <button type="submit" name="action" id="work_start" value="work_start">勤務開始</button>
                <button type="submit" name="action" id="work_end" value="work_end" disabled>勤務終了</button>
                @elseif($workStart && !$workEnd)
                <button type="submit" name="action" id="work_start" value="work_start" disabled>勤務開始</button>
                <button type="submit" name="action" id="work_end" value="work_end">勤務終了</button>
                @elseif($workStart && $workEnd)
                <button type="submit" name="action" id="work_start" value="work_start" disabled>勤務開始</button>
                <button type="submit" name="action" id="work_end" value="work_end">勤務終了</button>
                @endif
            </div>
            <div class="break__group">
                <!-- 打刻前　-->
                @if(!$workStart && !$restStart && !$restEnd && !$workEnd)
                <button type="submit" name="action" id="rest_start" value="rest_start" disabled>休憩開始</button>
                <button type="submit" name="action" id="rest_end" value="rest_end" disabled>休憩終了</button>
                <!-- 出勤中　就業中 いつでも休憩取れる -->
                @elseif ($workStart && !$restStart && !$restEnd && !$workEnd)
                <button type="submit" name="action" id="rest_start" value="rest_start">休憩開始</button>
                <button type="submit" name="action" id="rest_end" value="rest_end" disabled>休憩終了</button>
                <!-- 出勤中　休憩中 休憩終了出来る-->
                @elseif ($workStart && $restStart && !$restEnd && !$workEnd)
                <button type="submit" name="action" id="rest_start" value="rest_start" disabled>休憩開始</button>
                <button type="submit" name="action" id="rest_end" value="rest_end">休憩終了</button>
                <!-- 出勤中　休憩終了　2回目の休憩取れる -->
                @elseif ($workStart && $restStart && $restEnd && !$workEnd )
                <button type="submit" name="action" id="rest_start" value="rest_start">休憩開始</button>
                <button type="submit" name="action" id="rest_end" value="rest_end" disabled>休憩終了</button>
                <!-- 退勤処理 休憩開始・休憩終了処理不可になる -->
                @elseif ($workStart && $workEnd)
                <button type="submit" name="action" id="rest_start" value="rest_start" disabled>休憩開始</button>
                <button type="submit" name="action" id="rest_end" value="rest_end" disabled>休憩終了</button>
                @endif
            </div>
        </form>
    </div>
</div>




@endsection