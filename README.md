# WordBook

![quiz](https://user-images.githubusercontent.com/18690548/129211444-b9dbd4a5-f0d7-4338-b6a3-759ca56a7d49.gif)

# 概要
日本語と英語の単語帳アプリケーションです。  
和訳と英訳の問題作成、クイズができます。  
スマートフォンからも実行可能です。 

# URL
https://mikanbako.sakura.ne.jp/Wordbook/

# 使用言語
* PHP
* Javascript(jQuery含む)
* HTML
* CSS
* MySQL

# 開発環境
* Windows10
* XAMPP
* Visual Studio Code

# 機能一覧
* トップ画面
* ログイン画面
  * ログイン
  * 新規ユーザー登録
* 問題編集画面
  * 登録
  * 更新
  * 削除
  * データ一覧閲覧
* クイズ画面
  * 出題設定
  * クイズ解答
  * 結果表示

# 使用方法
## トップ画面
クイズ画面へ遷移できます。非ログイン時はログイン画面へ遷移できます。ログイン時はユーザー情報の表示、編集画面へ遷移できます。  
![top1](https://user-images.githubusercontent.com/18690548/129180736-ae92c53c-285f-48d6-aacc-4a29f93d33af.PNG)
![top2](https://user-images.githubusercontent.com/18690548/129208072-42d91229-1d57-4c32-bcdc-206717f32df4.PNG)

## ログイン画面
ログインまたは新規ユーザー登録してログインできます。
![login](https://user-images.githubusercontent.com/18690548/129207085-349e28da-c901-403b-8cac-fd334992c65e.gif)
### ログイン
ユーザー名とパスワードを入力してログインします。
### 新規ユーザー登録
ユーザー名とパスワードを入力して新規登録します。パスワードは半角英数字4文字以上の制限があります。既に存在しているユーザー名の場合は登録できません。

## 問題編集画面
ログインしているユーザーのみアクセス可能なページです。
### 編集
ログインしている且つ編集可能回数がある場合、問題の登録、更新、削除ができます。(画像は登録処理です)  
![regist](https://user-images.githubusercontent.com/18690548/129209769-05bd6176-7cb7-4024-8c86-49c7f9aa3814.gif)

### データ一覧表示
登録されているデータを確認できます。  
![view](https://user-images.githubusercontent.com/18690548/129210487-2286bc69-163f-43a4-964f-35d86d26648f.gif)

## クイズ画面
### 設定画面
出題される問題種類、問題数、音声の有無を設定できます。スタートボタンを押すとクイズが始まります。  
![setting](https://user-images.githubusercontent.com/18690548/129210931-24172118-7a36-4a08-b300-a4682203c455.PNG)

### クイズ
設定されたクイズが出題されます。選択肢を1つ選んで解答していきます。最後に結果画面が表示されます。  
![quiz](https://user-images.githubusercontent.com/18690548/129211444-b9dbd4a5-f0d7-4338-b6a3-759ca56a7d49.gif)

# テーブル定義書
[テーブル定義書（Googleスプレッドシート）](https://docs.google.com/spreadsheets/d/1kAnCUVVjwLSICFZMSjreRandVdp8pJWhO918HXTGdZw/edit?usp=sharing)

# テスト

# リンク
* [Github](https://github.com/yoshitaka7144)
* [ポートフォリオ](https://mikanbako.sakura.ne.jp/portfolio/)