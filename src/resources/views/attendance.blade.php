@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            <a href="{{ route('attendance.index', $previous) }}" class="date__link"> &lsaquo; </a>
            <h2 class="form__heading">{{ $date }}</h2>
            <a href="{{ route('attendance.index', $next) }}" class="date__link"> &rsaquo; </a>
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
            <td>{{ $date['workStart'] }}</td>
            <td>{{ $date['workEnd'] ?? '入力漏れ' }}</td>
            <td>{{ $date['restTime'] }}</td>
            <td>{{ $date['workTime'] }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination">{{ $datesPaginate->links() }}</div>
</div>
@endsection