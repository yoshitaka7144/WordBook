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
                <?php if (isset($_SESSION["user"])) : ?>
                    <p class="title">編集メニュー</p>
                    <p class="message">問題の追加や修正を行います</p>
                    <p class="message color-blue">編集可能回数：<?= h($_SESSION["user"]["registCount"]) ?></p>
                    <a href="edit.php" class="btn btn-normal btn-green">問題編集</a>
                <?php else : ?>
                    <p class="title">ログイン</p>
                    <p class="message">ログインユーザーのみ問題編集機能は利用できます</p>
                    <a href="login.php" class="btn btn-normal btn-green">ログイン</a>
                    <p><a href="createUser.php">新規ユーザー登録はこちらから</a></p>
                <?php endif ?>
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