<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/verify-email.css') }}" rel="stylesheet">
    <title>Atte</title>
</head>

<body>
    <div class="container">
        <div class="container__inner">
            <div class="card">
                <div class="card-header">{{ __('メールアドレスの確認') }}</div>

                <div class="card-body">
                    <div class="btn-container">
                        @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('あなたのEメールアドレスに新しい認証リンクが送信されました。') }}
                        </div>
                        @endif

                        <p>{{ __('入力したメールアドレスに確認メールが届いていないかをご確認ください。') }}</p>
                        <p>{{ __('メールが届かない場合、下のリンクをクリックして再度メールをリクエストして下さい。') }}</p>
                         <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn">{{ __('click here to request another') }}</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>