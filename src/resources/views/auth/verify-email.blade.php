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

                        <p>{{ __('ご入力いただいたメールアドレスに認証リンクを送信したので、ご確認ください。') }}</p>
                        <p>{{ __('もし認証メールが届かない場合は再送させていただきます。') }}</p>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-verification">{{ __('認証メールを再送する') }}</button>
                        </form>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="btn btn-logout">
                                ログアウト
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>