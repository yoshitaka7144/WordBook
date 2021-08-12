<?php

/**
 * 共通で使用する関数
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

/**
 * エスケープ処理
 *
 * @param string $str 処理対象文字列
 * @return string エスケープ処理済み文字列
 */
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * クイズ関連のセッションクリア
 *
 * @return void
 */
function unsetQuizSession()
{
  unset($_SESSION["dbConnected"]);
  unset($_SESSION["quizData"]);
  unset($_SESSION["quizCount"]);
  unset($_SESSION["quizType"]);
  unset($_SESSION["enabledAudio"]);
  unset($_SESSION["quizCurrentIndex"]);
  unset($_SESSION["startTime"]);
  unset($_SESSION["incorrect"]);
  unset($_SESSION["finished"]);
  unset($_SESSION["endTime"]);
}

/**
 * 編集可能回数を1減らしてデータ更新
 *
 * @param PDO $pdo
 * @return void
 */
function minusRegistCount($pdo)
{
  $stmt = $pdo->prepare("update users set regist_count = :regist_count where name = :name");
  $stmt->bindValue(":regist_count", $_SESSION["user"]["registCount"] - 1);
  $stmt->bindValue(":name", $_SESSION["user"]["name"]);
  $stmt->execute();

  $_SESSION["user"]["registCount"]--;
}

/**
 * 最終アクセス日更新処理
 *
 * @return void
 */
function updateLastAccessDate()
{
  // 現在日付
  $nowDate = date("Y-m-d");

  // セッションにある最終アクセス日と現在日付比較
  if ($_SESSION["user"]["lastAccessDate"] < $nowDate) {
    // 編集可能回数をレベルの数値にする
    $plusCount = floor($_SESSION["user"]["answerCount"] / USER_LEVEL_DENOMINATOR);
    if ($_SESSION["user"]["registCount"] < $plusCount) {
      $_SESSION["user"]["registCount"] = $plusCount;
    }

    // 更新
    $_SESSION["user"]["lastAccessDate"] = $nowDate;
    $pdo = new PDO(
      "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . ";charset=utf8mb4",
      DB_USER,
      DB_PASS,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]
    );
    $stmt = $pdo->prepare("update users set regist_count = :regist_count, last_access_date = :last_access_date where name = :name");
    $stmt->bindValue(":regist_count", $_SESSION["user"]["registCount"]);
    $stmt->bindValue(":last_access_date", $_SESSION["user"]["lastAccessDate"]);
    $stmt->bindValue(":name", $_SESSION["user"]["name"]);
    $stmt->execute();
  }
}
