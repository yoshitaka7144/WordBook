<?php
function validation($data, $registType){
  $errors = [];

  if(($registType === REGIST_TYPE_UPDATE || $registType === REGIST_TYPE_DELETE) && empty($data["inputId"])){
    $errors[] = "IDを入力してください";
  }

  if(empty($data["inputType"])){
    $errors[] = "種類を選択してください";
  }

  if(empty($data["inputQuestion"])){
    $errors[] = "問題を入力してください";
  }

  if(empty($data["inputAnswer"])){
    $errors[] = "答えを入力してください";
  }

  return $errors;
}
?>