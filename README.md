# **Atte(アット) -勤怠管理システム**
- Atte(アット)は勤怠管理システムです。クリック1つで勤務時間と休憩時間をデータ管理することが出来ます。仕事の効率化・打刻の徹底・実態とかけ離れない勤怠の記録を実現します。
< ----トップ画面の画像---->

## 作成した目的
### 現状の問題
#### 社員側
- 勤怠管理の徹底が出来ず、実際の労号時間との差異発生
#### 管理者側
- 個々の社員の労働時間や残業時間の把握、管理困難
### 解決策と目的
勤怠の入力を気軽に出来るシステムを導入することで、ここの勤怠管理が容易となり、管理者が正当な評価を実現する。

## アプリケーションURL
- AWS http://パブリックIPv4アドレス
<!-- - ログインなどがあれば、注意事項など -->

## 他のリポジトリ
- GitHub SSH git@github.com:fujico23/atte-system.git
- 
<!-- - 関連するリポジトリがあれば記載する -->
<!-- - 例)バックエンドのリポジトリ、フロントエンドのリポジトリ -->

## 機能一覧
- 会員登録
- ログイン
- ログアウト
- 勤務開始(日を跨いだ時点で翌日の出勤操作に切り替える)
- 勤務終了(日を跨いだ時点で翌日の出勤操作に切り替える)
- 休憩開始(※1日で何度も休憩が可能)
- 休憩終了
- 日付別勤怠情報取得
- ページネーション(5件ずつ取得)
- 社員一覧ページ表示（当日の勤務状況を表示する）
- 社員別ページ表示（社員別ページは月毎に表示する/CSVファイルを社員別・月毎にダウンロードする）


## 使用技術(実行環境)
- Laravel 8.x
- PHP 7.4.9-fpm
- MySQL 8.0.26
- nginx 1.21.1

## Laravel環境構築
- docker-compose exec php bash
- composer install
- .env.exampleファイルから.envを作成し、環境変数を変更
- php artisan key:generate
- php artisan migrate
- php artisan db:seed

<!-- ## 環境構築 -->
<!-- - 他の人でもプロジェクトを実行出来るようコマンドや編集ファイルを記載する -->

## 環境変数
### テスト環境
- DB_CONNECTION=mysql
- DB_HOST=mysql
- DB_PORT=3306
- DB_DATABASE=laravel_db
- DB_USERNAME=laravel_user
- DB_PASSWORD=laravel_pass

- MAIL_MAILER=smtp
- MAIL_HOST=mailhog
- MAIL_PORT=1025
- MAIL_USERNAME=null
- MAIL_PASSWORD=null
- MAIL_ENCRYPTION=null

## テーブル設計
![](./table.drawio.svg)

## ER図
![](./er.drawio.svg)

## 他に記載することがあれば記述する
<!-- - 例)アカウントの種類(テストユーザーなど) -->