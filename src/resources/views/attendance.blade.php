@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <div class="date">
        <div class="date__inner">
          <a href="" class="date__link"> < </a>
          <h2 class="form__heading">2021-11-01</h2>
          <a href="" class="date__link"> > </a>
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
            <td>10:00:20</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:29:40</td>
        </tr>
        <tr class="table__row">
            <td>テスト五郎</td>
            <td>10:00:20</td>
            <td>20:00:00</td>
            <td>00:30:00</td>
            <td>09:29:40</td>
        </tr>
    </table>
    <div class="pagenation">12345678910...2021</div>

</div>
@endsection