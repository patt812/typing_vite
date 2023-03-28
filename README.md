<p align="center"><a href="https://ra-cu-da.net" target="_blank"><img src="https://ra-cu-da.net/icons/logo.png" width="100" alt="racuda logo"></a></p>

## 🐫楽打🐫

楽打（らくだ）は文章と出題を自由に設定できるタイピングソフトです。

特定のお題や苦手な文章を集中的に攻略できること目標に作りました。

デモサイトは[こちら](https://ra-cu-da.net)

## 機能

- 測定機能
- 出題設定
- 文章編集
- 統計閲覧
- プロフィール編集

## 技術構成

- Inertia.js (SPA) 0.11
- Laravel 9.19
- MySQL 8.0.32
- Vue 3.2.31
- Vite 4.0.0
- Nginx
- [Docker](https://github.com/ucan-lab/docker-laravel)
- AWS
  - EC2
  - SES
  - Route 53

## カスタマイズ

ローカルで動かしてみる場合は、サインアップ時に登録される文章を定義するファイル[resources/sentence/template.json.example](https://github.com/patt812/typing_vite/blob/de2dcbfc0e7c47afd0076c320a9d2882cb28b2b7/resources/sentence/template.json.example)と、入力パターンを定義するファイル[resources/js/Typing/romaPatterns.js.example](https://github.com/patt812/typing_vite/blob/de2dcbfc0e7c47afd0076c320a9d2882cb28b2b7/resources/js/Typing/romaPatterns.js.example)の編集が必要です。

リンク先のファイルを参考にコピーして.exampleを外し、任意の値を書き込んでください。
