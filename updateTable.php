<?php

/**
 * 一覧テーブル更新用データを返す
 * jsでajaxで呼び出される
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

header("Content-Type: application/json; charset=UTF-8");
require_once("config.php");
require_once("util.php");

$type = filter_input(INPUT_POST, "type");
$page = filter_input(INPUT_POST, "page");
$offset = ($page - 1) * MAX_TABLE_ROW_COUNT;

$result = [];
try {
  $pdo = new PDO(
    "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . ";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );

  // 対象データ取得
  if ($type === SELECT_TYPE_ALL) {
    // 表示対象が全データの場合
    $stmt = $pdo->prepare("select id, type, question, answer from words order by id limit :offset, :limitCount");
  } else {
    $stmt = $pdo->prepare("select id, type, question, answer from words where type = :type order by id limit :offset, :limitCount");
    $stmt->bindValue(":type", $type);
  }
  $stmt->bindValue(":offset", (int)$offset, pdo::PARAM_INT);
  $stmt->bindValue(":limitCount", (int)MAX_TABLE_ROW_COUNT, pdo::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll();

  // ページ数取得
  if ($type === SELECT_TYPE_ALL) {
    $stmt = $pdo->prepare("select count(*) from words");
  } else {
    $stmt = $pdo->prepare("select count(*) from words where type = :type");
    $stmt->bindValue(":type", $type);
  }
  $stmt->execute();
  $rowCount = $stmt->fetchColumn();
  $maxPage = (int)$rowCount === 0 ? 1 : ceil($rowCount / MAX_TABLE_ROW_COUNT);
} catch (PDOException $e) {
  $result["error"] = $e->getMessage();
  echo json_encode($result);
  exit;
}

// 一覧表示用データ作成
foreach ($rows as $key => $value) {
  // jsからajaxで呼び出される為ここでエスケープ処理
  $rows[$key]["id"] = h($rows[$key]["id"]);
  $rows[$key]["type"] = h($rows[$key]["type"]);
  $rows[$key]["question"] = h($rows[$key]["question"]);
  $rows[$key]["answer"] = h($rows[$key]["answer"]);
}
$result = ["rows" => $rows, "currentPage" => $page, "maxPage" => $maxPage];

// 結果を返す
echo json_encode($result);
