@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            @if ($previousAttendance)
              <a href="{{ route('attendance.index', $previousAttendance) }}"  class="date__link" > < </a>
            @endif
               <h2 class="form__heading">{{ $newestDate }}</h2>
              <a href="{{ route('attendance.index') }}" class="date__link"> > </a>
        </div>
    </div>
    <table class="attendance__table">
        <tr class="table__header">
            <th>名前</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>休憩時間</th>
            <th>勤務時間</th>
        </tr>
        <tr class="table__row">
            <td>テスト太郎</td>
            <td>10:00:00</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:30:00</td>
        </tr>
        <tr class="table__row">
            <td>テスト次郎</td>
            <td>10:00:10</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:29:50</td>
        </tr>
        <tr class="table__row">
            <td>テスト三郎</td>
            <td>10:00:10</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:29:50</td>
        </tr>
        <tr class="table__row">
            <td>テスト四郎</td>
            <td>10:00:10</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:29:50</td>
        </tr>
        @foreach ($dates as $date)
        <tr class="table__row">
            <td>{{ $date['name'] }}</td>
            <td>{{ $date['work_start'] }}</td>
            <td>{{ $date['work_end'] }}</td>
            <td>{{ gmdate("H:i:s", $date['total_rest_time']) }}</td>
            <td>{{ $date['total_work_time'] }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagenation">12345678910...2021</div>

</div>
@endsection