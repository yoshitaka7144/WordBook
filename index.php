<?php

/**
 * トップページ
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("util.php");
session_start();
unsetSession();
?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
    <div class="container">
        <div class="main-contents">
            <div class="menu">
                <p class="title">編集メニュー</p>
                <p class="message">問題の追加や修正を行います</p>
                <a href="edit.php" class="btn btn-normal btn-green">問題編集</a>
            </div>
            <div class="menu">
                <p class="title">クイズメニュー</p>
                <p class="message">問題の種類を選択してください</p>
                <p><a href="quizSetting.php?type=和訳" class="btn btn-normal btn-blue">和訳クイズ</a></p>
                <p><a href="quizSetting.php?type=英訳" class="btn btn-normal btn-blue">英訳クイズ</a></p>
            </div>
        </div>
    </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>