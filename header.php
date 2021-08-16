<?php

/**
 * 共通ヘッダー
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Word Book</title>
    <link rel="icon" href="image/favicon.ico">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/headerMenu.js"></script>
    <script src="js/dataTable.js"></script>
    <script src="js/quiz.js"></script>
</head>

<body>
    <header>
        <div id="logo">
            <a href="./">WordBook</a>
        </div>
        <nav id="pc-nav">
            <ul>
                <?php if (isset($_SESSION["user"])) : ?>
                    <li><a href="edit.php">問題編集</a></li>
                <?php endif ?>
                <li><a href="quizSetting.php?type=和訳">和訳クイズ</a></li>
                <li><a href="quizSetting.php?type=英訳">英訳クイズ</a></li>
                <?php if (isset($_SESSION["user"])) : ?>
                    <li><a href="logout.php">ログアウト</a></li>
                <?php else : ?>
                    <li><a href="login.php">ログイン</a></li>
                <?php endif ?>
            </ul>
        </nav>
        <div id="hamburger">
            <span></span>
        </div>
    </header>
    <nav id="sp-nav">
        <ul>
            <li><a href="./">トップ</a></li>
            <?php if (isset($_SESSION["user"])) : ?>
                <li><a href="edit.php">問題編集</a></li>
            <?php endif ?>
            <li><a href="quizSetting.php?type=和訳">和訳クイズ</a></li>
            <li><a href="quizSetting.php?type=英訳">英訳クイズ</a></li>
            <?php if (isset($_SESSION["user"])) : ?>
                <li><a href="logout.php">ログアウト</a></li>
            <?php else : ?>
                <li><a href="login.php">ログイン</a></li>
            <?php endif ?>
            <li id="close"><span><i class="fas fa-times"></i>閉じる</span></li>
        </ul>
    </nav>