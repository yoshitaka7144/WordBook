<?php
function validation($data, $registType){
  $errors = [];

  if(($registType === REGIST_TYPE_UPDATE || $registType === REGIST_TYPE_DELETE) && empty($data["inputId"])){
    $errors[] = "IDを入力してください";
  }

  if(empty($data["inputType"])){
    $errors[] = "種類を選択してください";
  }else{
    if($data["inputType"] !== "和訳" && $data["inputType"] !== "英訳"){
      $errors[] = "種類が不正な入力です";
    }
  }

  if(empty($data["inputQuestion"])){
    $errors[] = "問題を入力してください";
  }else{
    if($data["inputType"] === "和訳" && !preg_match("/^[a-zA-Z]+$/", $data["inputQuestion"])){
      $errors[] = "問題は英語で入力してください";
    }
  }

  if(empty($data["inputAnswer"])){
    $errors[] = "答えを入力してください";
  }else{
    if($data["inputType"] === "英訳" && !preg_match("/^[a-zA-Z]+$/", $data["inputAnswer"])){
      $errors[] = "答えは英語で入力してください";
    }
  }

  return $errors;
}
?>