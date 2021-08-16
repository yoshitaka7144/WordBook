<?php

/**
 * バリデーション処理
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

/**
 * 入力内容チェック
 *
 * @param string[] $data
 * @param string $registType
 * @return string[] エラーメッセージ
 */
function validation($data, $registType)
{
  // 結果用配列
  $errors = [];

  // IDは更新 or 削除処理時には必須
  if ($registType === REGIST_TYPE_UPDATE || $registType === REGIST_TYPE_DELETE) {
    if (empty($data["inputId"])) {
      $errors[] = "IDを入力してください";
    } elseif (!preg_match("/^[0-9]+$/", $data["inputId"])) {
      $errors[] = "IDは数値を入力してください";
    }
  }

  // 種類は必須
  // 和訳 or 英訳に限定
  if (empty($data["inputType"])) {
    $errors[] = "種類を選択してください";
  } else {
    if ($data["inputType"] !== "和訳" && $data["inputType"] !== "英訳") {
      $errors[] = "種類が不正な入力です";
    }
  }

  // 問題は必須
  // 種類が和訳の場合、問題はアルファベットのみ
  // 種類が英訳の場合、問題にアルファベットは含まない
  if (empty($data["inputQuestion"])) {
    $errors[] = "問題を入力してください";
  } else {
    if (mb_strlen($data["inputQuestion"]) > 15) {
      $errors[] = "問題は15文字以内で入力してください";
    }
    if ($data["inputType"] === "和訳" && !preg_match("/^[a-zA-Z]+$/", $data["inputQuestion"])) {
      $errors[] = "問題は英語で入力してください";
    }
    if ($data["inputType"] === "英訳" && preg_match("/[a-zA-Z]/", $data["inputQuestion"])) {
      $errors[] = "問題は日本語で入力してください";
    }
  }

  // 答えは必須
  // 種類が英訳の場合、答えはアルファベットのみ
  // 種類が和訳の場合、答えにアルファベットは含まない
  if (empty($data["inputAnswer"])) {
    $errors[] = "答えを入力してください";
  } else {
    if (mb_strlen($data["inputAnswer"]) > 15) {
      $errors[] = "答えは15文字以内で入力してください";
    }
    if ($data["inputType"] === "英訳" && !preg_match("/^[a-zA-Z]+$/", $data["inputAnswer"])) {
      $errors[] = "答えは英語で入力してください";
    }
    if ($data["inputType"] === "和訳" && preg_match("/[a-zA-Z]/", $data["inputAnswer"])) {
      $errors[] = "答えは日本語で入力してください";
    }
  }

  // エラーメッセージ配列を返す
  return $errors;
}
