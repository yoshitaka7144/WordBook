<?php
session_start();
$_SESSION = array();
session_destroy();
?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
    <div class="container">
        <div class="main-contents">
            <div class="menu">
                <p class="title">編集メニュー</p>
                <p class="message">問題の追加や修正を行います</p>
                <a href="edit.php" class="btn btn-normal btn-green">管理画面</a>
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