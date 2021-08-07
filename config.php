<?php

/**
 * 定数定義
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

/**
 * データベース：ホスト名
 */
define("DB_HOST", "localhost");
/**
 * データベース：データベース名
 */
define("DB_NAME", "test");
/**
 * データベース：ユーザー名
 */
define("DB_USER", "yoshi");
/**
 * データベース：パスワード
 */
define("DB_PASS", "test");
/**
 * データベース：エラーメッセージ
 */
define("DB_ERROR_MESSAGE", "データベース処理にてエラーが発生しました。");

/**
 * 編集ページ：デフォルト
 */
define("PAGE_TYPE_DEFAULT", 0);
/**
 * 編集ページ：エラー
 */
define("PAGE_TYPE_ERROR", -1);
/**
 * 編集ページ：確認画面
 */
define("PAGE_TYPE_CONFIRM", 1);

/**
 * 一覧テーブル表示行数
 */
define("MAX_TABLE_ROW_COUNT", 7);

/**
 * 表示対象：全データ
 */
define("SELECT_TYPE_ALL", "all");

/**
 * データベース処理：登録
 */
define("REGIST_TYPE_CREATE", "登録");
/**
 * データベース処理：更新
 */
define("REGIST_TYPE_UPDATE", "更新");
/**
 * データベース処理：削除
 */
define("REGIST_TYPE_DELETE", "削除");

/**
 * クイズ準備：問題数最小
 */
define("SELECT_COUNT_MIN", 5);
/**
 * クイズ準備：問題数最大
 */
define("SELECT_COUNT_MAX", 20);
/**
 * クイズ準備：デフォルト問題数
 */
define("DEFAULT_SELECTED_NUMBER", 10);

/**
 * クイズ：選択肢の数
 */
define("CHOICES_COUNT", 4);
