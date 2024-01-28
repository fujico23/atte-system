@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <h2 class="form__heading">{{ $name }}さんお疲れ様です！</h2>
    <div class="attendance-system">
        <form action="">
            <div class="work__group">
                <input type="submit" name="work_start" value="勤務開始">
                <input type="submit" name="work_end" value="勤務終了">
            </div>
            <div class="break__group">
                <input type="submit" name="break_start" value="休憩開始">
                <input type="submit" name="break_end" value="休憩終了">
            </div>
        </form>
    </div>
</div>
@endsection