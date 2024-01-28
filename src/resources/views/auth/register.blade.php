@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="content__inner">
    <h2 class="form__heading">会員登録</h2>
    <form class="auth-form" action="/register" method="post">
        @csrf
        <div class="auth-form__inner">
            <div class="auth-form__group">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="名前">
            </div>
            <p class="auth-error">@error('name')
                {{ $message }}
                @enderror
            </p>
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
                <input type="password" name="password_confirmation" placeholder="確認用パスワード">
            </div>
            <div class="auth-form__group">
                <input class="auth-form__group__button" type="submit" value="会員登録">
            </div>
        </div>
    </form>

    <div class="confirm-form">
        <p>アカウントをお持ちの方はこちらから</p>
        <a href="/login">ログイン</a>
    </div>
</div>
@endsection