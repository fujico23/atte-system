@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <h2 class="form__heading">{{ $name }}さんお疲れ様です！</h2>
    <div class="attendance-system">
        <form action="/store" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="work__group">
                <button type="submit" name="action" value="work_start">勤務開始</button>
                <button type="submit" name="action" value="work_end" >勤務終了</button>
            </div>
            <div class="break__group">
                <button type="submit" name="action" value="break_start">休憩開始</button>
                <button type="submit" name="action" value="break_end">休憩終了</button>
            </div>
        </form>
    </div>
</div>
@endsection