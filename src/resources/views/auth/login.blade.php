@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <h2 class="form__heading">ログイン</h2>
    <form class="auth-form" action="/login" method="post">
        @csrf
        <div class="auth-form__inner">
            <div class="auth-form__group">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス">
            </div>
            <p class="auth-error">@error('email')
                {{ $message }}
                @enderror
            </p>
            <div class="auth-form__group">
                <input type="password" name="password" placeholder="パスワード">
            </div>
            <p class="auth-error">@error('password')
                {{ $message }}
                @enderror
            </p>
            <div class="auth-form__group">
                <input class="auth-form__group__button" type="submit" value="ログイン">
            </div>
        </div>
    </form>

    <div class="confirm-form">
        <p>アカウントをお持ちでない方はこちらから</p>
        <a href="/register">会員登録</a>
    </div>
</div>
@endsection