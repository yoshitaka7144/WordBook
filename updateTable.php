<?php
require_once("config.php");
header("Content-Type: application/json; charset=UTF-8");

$type = filter_input(INPUT_POST,"type");
$page = filter_input(INPUT_POST,"page");
$offset = ($page-1) * MAX_TABLE_ROW_COUNT;

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
  if($type === SELECT_TYPE_ALL){
    $stmt = $pdo->prepare("select id, type, question, answer from words order by id limit :offset, :limitCount");
  }else{
    $stmt = $pdo->prepare("select id, type, question, answer from words where type = :type order by id limit :offset, :limitCount");
    $stmt->bindValue(":type", $type);
  }
  $stmt->bindValue(":offset", (int)$offset, pdo::PARAM_INT);
  $stmt->bindValue(":limitCount", (int)MAX_TABLE_ROW_COUNT, pdo::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll();

  if($type === SELECT_TYPE_ALL){
    $stmt = $pdo->prepare("select id, type, question, answer from words");
  }else{
    $stmt = $pdo->prepare("select id, type, question, answer from words where type = :type");
    $stmt->bindValue(":type", $type);
  }
  $stmt->execute();
  $rowCount = $stmt->rowCount();
  $maxPage = ceil($rowCount / MAX_TABLE_ROW_COUNT);
} catch (PDOException $e) {
  //throw $th;
}

$result = [$rows, $page, $maxPage];

echo json_encode($result);
