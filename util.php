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


function unsetSession()
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


function minusRegistCount($pdo)
{
  $stmt = $pdo->prepare("update users set regist_count = :regist_count where name = :name");
  $stmt->bindValue(":regist_count", $_SESSION["user"]["registCount"] - 1);
  $stmt->bindValue(":name", $_SESSION["user"]["name"]);
  $stmt->execute();

  $_SESSION["user"]["registCount"]--;
}
