# WordBook

![demo](https://user-images.githubusercontent.com/18690548/130126763-076f85a0-26f0-429d-929e-cb6bc098a974.gif)

# 概要
日本語と英語の単語帳アプリケーションです。  
和訳と英訳の問題作成、クイズができます。  
スマートフォンからも実行可能です。 

# URL
https://wordbook.mikanbako.jp

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

# テーブル定義書
ユーザーテーブル（users）と単語テーブル（words）のテーブル定義書です。  
<a href="https://docs.google.com/spreadsheets/d/1kAnCUVVjwLSICFZMSjreRandVdp8pJWhO918HXTGdZw/edit?usp=sharing" target="_blank" rel="noopener noreferrer">テーブル定義書（Googleスプレッドシート）</a>

# テスト
大雑把ですがテストを行いました。  
<a href="https://docs.google.com/spreadsheets/d/1NxQPDQ28mIoBi_U8K8_BsIVLgF6n-kF7VXJyqp0OEfI/edit?usp=sharing" target="_blank" rel="noopener noreferrer">機能単体テスト（Googleスプレッドシート）</a>  

テスト環境  
PC
* OS：Windows10
* ブラウザ：Chrome 91.0.4472.164

スマートフォン
* OS：Android 11
* ブラウザ：Chrome 92.0.4515.131

# 使用方法
## トップ画面
非ログイン時はログイン機能、クイズが利用できます。ログイン時はログイン情報の表示、問題編集機能、クイズが利用できます。  
![top1](https://user-images.githubusercontent.com/18690548/129180736-ae92c53c-285f-48d6-aacc-4a29f93d33af.PNG)
![top2](https://user-images.githubusercontent.com/18690548/129361513-12acc99b-1d37-4204-a7f6-a413eabe72e6.PNG)
### ログイン情報
* ユーザー名：登録されたユーザー名です。
* レベル：累計正解数に応じた数値です。
* 累計正解数：これまでに正解した問題数です。
* 編集可能回数：問題編集を行うことができる回数です。レベルアップ時や最終アクセス日から1日以上経過後のログインで回数が増えます。

## ログイン画面
ログインまたは新規ユーザー登録を行いログインできます。
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
![setting](https://user-images.githubusercontent.com/18690548/129547344-5cc2fb3a-d276-4723-868c-682c24f3afbf.PNG)

### クイズ
設定画面にて設定されたクイズが出題されます。4つの選択肢から1つ選択し解答していき、最後に結果画面が表示されます。クイズは登録データからランダムに選出され、選択肢は正解1つと他の問題データの答え3つから作成されます。  
![quiz](https://user-images.githubusercontent.com/18690548/130127577-a40041b3-0f3c-47de-a573-19886abe42c7.gif)

# リンク
* <a href="https://github.com/yoshitaka7144" target="_blank" rel="noopener noreferrer">Githubアカウント</a>
* <a href="https://portfolio.mikanbako.jp" target="_blank" rel="noopener noreferrer">ポートフォリオ</a>