# **Atte(アット) -勤怠管理システム-**
- Atte(アット)は勤怠管理システムです。クリック1つで勤務時間と休憩時間をデータ管理することが出来ます。仕事の効率化・打刻の徹底・実態とかけ離れない勤怠の記録を実現します。
< ----トップ画面の画像---->

## 作成した目的
1. 現状の問題
- 社員側
#### 勤怠管理の徹底が出来ず、実際の労号時間との差異発生
- 管理者側
#### 個々の社員の労働時間や残業時間の把握、管理困難
1. 解決策と目的
勤怠の入力を気軽に出来るシステムを導入することで、ここの勤怠管理が容易となり、管理者が正当な評価を実現する。

## アプリケーションURL
- デプロイのURLを貼り付ける
- ログインなどがあれば、注意事項など

## 他のリポジトリ
- 関連するリポジトリがあれば記載する
- 例)バックエンドのリポジトリ、フロントエンドのリポジトリ

## 機能一覧
- 例)ログイン機能

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

## 環境構築
- 他の人でもプロジェクトを実行出来るようコマンドや編集ファイルを記載する

## 環境変数
- MYSQL_ROOT_PASSWORD: root
- MYSQL_DATABASE: laravel_db
- MYSQL_USER: laravel_user
- MYSQL_PASSWORD: laravel_pass

## テーブル設計
![](./table.drawio)

## ER図
![](./er.drawio)

## 他に記載することがあれば記述する
- 例)アカウントの種類(テストユーザーなど)