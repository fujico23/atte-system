@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            <a href="{{ route('attendance.index', $previousDate) }}" class="date__link"> &lsaquo; </a>
            <h2 class="form__heading">{{ $date }}</h2>
            <a href="{{ route('attendance.index', $nextDate) }}" class="date__link"> &rsaquo; </a>
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
        @foreach ($datesPaginate->items() as $date)
        <tr class="table__row">
            <td>{{ $date['name'] }}</td>
            <td>{{ $date['work_start'] }}</td>
            <td>{{ $date['work_end'] ?? '入力漏れ' }}</td>
            <td>{{ gmdate("H:i:s", $date['total_rest_time']) }}</td>
            <td>{{ $date['total_work_time'] }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination">{{ $datesPaginate->links() }}</div>
</div>
@endsection