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
            <th>メールアドレス</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>勤務状況</th>
        </tr>
        @foreach($users as $user)
        <tr class="table__row">
            <td><a href="/detail/{{$user['id']}}">{{ $user['name'] }}</a></td>
            <td>{{ $user['email'] }}</td>
            <td>{{ !empty($user['work_start']) ? \Carbon\Carbon::parse($user['work_start'])->format('H:i:s') : '未取得' }}</td>
            <td>{{ !empty($user['work_end']) ? \Carbon\Carbon::parse($user['work_end'])->format('H:i:s') : '未取得' }}</td>
            <td>
                @if (!empty($user['work_start']) && !empty($user['work_end']))
                <span style="color: grey;">退社</span>
                @elseif (!empty($user['work_start']))
                <span style="color: blue;">出勤</span>
                @else
                <span style="color: red;">公休</span>
                @endif
            </td>
        </tr>
        @endforeach

    </table>

</div>
@endsection