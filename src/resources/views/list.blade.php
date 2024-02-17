@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <h2 class="form__heading">社員一覧</h2>
    </div>

    <table class="attendance__table">
        <tr class="table__header">
            <th>名前</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>休憩時間</th>
            <th>勤務時間</th>
        </tr>
        @foreach($users as $user)
        <tr class="table__row">
            <td><a href="/detail/{{$user->id}}">{{ $user->name }}</a></td>
            <td>{{ $user->id }}</td>
            <td>{{ $user->email }}</td>
            <td>00:30:00</td>
            <td>09:30:00</td>
        </tr>
        @endforeach
    </table>

</div>
@endsection