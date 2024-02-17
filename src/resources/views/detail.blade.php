@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            <a href="" class="date__link"> &lsaquo; </a>
            <h2 class="form__heading">{{ $month->format('Y-m') }}</h2>
            <a href="" class="date__link"> &rsaquo; </a>
        </div>
        <h2 class="form__heading">{{ $name }}</h2>
    </div>
    <table class="attendance__table">
        <tr class="table__header">
            <th>日付</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>休憩時間</th>
            <th>勤務時間</th>
        </tr>
        @foreach ($items as $item)
        <tr class="table__row">
            <td>{{ $item['date'] }}</td>
            <td>{{ $item['work_start'] }}</td>
            <td>{{ $item['work_end'] ?? '入力漏れ' }}</td>
            <td>{{ gmdate("H:i:s", $item['total_rest_time']) }}</td>
            <td>{{ $item['total_work_time'] }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination"></div>

</div>
@endsection