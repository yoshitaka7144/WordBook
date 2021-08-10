<?php

/**
 * トップページ
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("config.php");
require_once("util.php");
session_start();
unsetSession();

if (isset($_SESSION["user"])) {
  updateLastAccessDate();
}
?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <?php if (isset($_SESSION["user"])) : ?>
        <div class="menu">
          <p class="title">ログイン情報</p>
          <table class="login-info">
            <tr>
              <th>ユーザー名</th>
              <td><?=h($_SESSION["user"]["name"])?></td>
            </tr>
            <tr>
              <th>レベル</th>
              <td><?=h(floor($_SESSION["user"]["answerCount"]/USER_LEVEL_DENOMINATOR))?></td>
            </tr>
            <tr>
              <th>aaa</th>
              <td><div class="count-progress"><div class="coount-progress-bar"></div></div></td>
            </tr>
            <tr>
              <th>累計正解数</th>
              <td><?=h($_SESSION["user"]["answerCount"])?></td>
            </tr>
            <tr>
              <th>編集可能回数</th>
              <td><?=h($_SESSION["user"]["registCount"])?></td>
            </tr>
          </table>
        </div>
        <div class="menu">
          <p class="title">編集メニュー</p>
          <p class="message">問題の追加や修正を行います</p>
          <p class="message color-blue">編集可能回数：<?= h($_SESSION["user"]["registCount"]) ?></p>
          <a href="edit.php" class="btn btn-normal btn-green">問題編集</a>
        </div>
      <?php else : ?>
        <div class="menu">
          <p class="title">ログイン</p>
          <p class="message">ログインユーザーのみ問題編集機能は利用できます</p>
          <a href="login.php" class="btn btn-normal btn-green">ログイン</a>
          <p><a href="createUser.php">新規ユーザー登録はこちらから</a></p>
        </div>
      <?php endif ?>
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