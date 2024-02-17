@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
            @if ($previousDate)
            <a href="{{ route('attendance.index', $previousDate) }}" class="date__link"> &lsaquo; </a>
            @endif
            <h2 class="form__heading">{{ $date }}</h2>
            @if ($nextDate)
            <a href="{{ route('attendance.index', $nextDate) }}" class="date__link"> &rsaquo; </a>
            @endif
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
        <!-- <tr class="table__row">
            <td>テスト太郎</td>
            <td>10:00:00</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:30:00</td>
        </tr> -->
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
    <div class="pagination">{{ $datesPaginate->links('vendor.pagination.bootstrap-4') }}</div>

</div>
@endsection