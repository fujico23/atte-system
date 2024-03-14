@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            <a href="{{ route('detail.show', ['id' => $user['id'], 'month' => $previous]) }}" class="date__link"> &lsaquo; </a>
            <h2 class="form__heading">{{ $month }}</h2>
            <a href="{{ route('detail.show', ['id' => $user['id'], 'month' => $next]) }}" class="date__link"> &rsaquo; </a>
        </div>
        <h2 class="form__heading">{{ $name }}</h2>
    </div>
    <form action="{{ route('export.csv', ['id' => $user['id'], 'month' => $month]) }}" method="post">
        @csrf
        <input class="export__btn" type="submit" value="EXPORT">
    </form>
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
            <td>{{ $item['workStart'] }}</td>
            <td>{{ $item['workEnd'] ?? '入力漏れ' }}</td>
            <td>{{ $item['restTime'] }}</td>
            <td>{{ $item['workTime'] }}</td>
        </tr>
        @endforeach
    </table>

</div>
@endsection