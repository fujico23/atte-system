<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <h1 class="header__heading">Atte</h1>
            <ul class="header-nav">
                @if (Auth::check())
                <li class="header-nav__item">
                    <a href="/" class="header-nav__link">ホーム</a>
                </li>
                <li class="header-nav__item">
                    <a href="/attendance" class="header-nav__link">日付一覧</a>
                </li>
                <li class="header-nav__item">
                    <form class="logout-nav__button" action="/logout" method="post">
                        @csrf
                        <button class="header-nav__button-submit">ログアウト</button>
                    </form>
                </li>
                @endif
            </ul>
            @yield('link')
        </header>
        <main class="main">
            <div class="content">
                @yield('content')
            </div>
        </main>
        <footer class="footer">Atte,inc.</footer>
    </div>

</body>

</html>