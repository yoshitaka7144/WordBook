<?php

/**
 * クイズ準備画面
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("config.php");
require_once("util.php");

session_start();
unsetSession();

// 問題種類
$type = filter_input(INPUT_GET, "type");

$dbErrorMessage = "";
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
  // 問題種類を全て取得
  $stmt = $pdo->prepare("select type from words group by type");
  $stmt->execute();
  $rows = $stmt->fetchAll();
  if (count($rows) === 0) {
    $dbErrorMessage = "問題データが登録されていません。問題を追加登録してください。";
  }
} catch (PDOException $e) {
  $dbErrorMessage = $e->getMessage();
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <div class="quiz">
        <?php if (!empty($dbErrorMessage)) : ?>
          <p class="message color-red"><?= DB_ERROR_MESSAGE ?></p>
          <p class="message"><?= $dbErrorMessage ?></p>
          <div class="btn-wrapper">
            <a href="edit.php" class="btn btn-green btn-normal">問題編集</a>
            <a href="index.php" class="btn btn-blue btn-normal">トップ画面</a>
          </div>
        <?php else : ?>
          <p class="title">問題準備</p>
          <fieldset class="quiz-fieldset">
            <legend>説明</legend>
            <p class="message">・問題の種類について</p>
            <table class="description-table">
              <tr>
                <th>和訳</th>
                <td>問題の英単語に対して同じ意味を表す適切な<span class="highlight-y">日本語を選択する</span></td>
              </tr>
              <tr>
                <th>英訳</th>
                <td>問題の日本語に対して同じ意味を表す適切な<span class="highlight-y">英単語を選択する</span></td>
              </tr>
            </table>
            <p class="message">・解答方法について</p>
            <p class="message">　4つの選択肢の中から適切なものを選択し、<span class="highlight-g">解答するボタン</span>を押下してください。正解 or 不正解のメッセージが表示され<span class="highlight-b">次へボタン</span>が押下可能になります。ボタン押下で次の問題へ進みます。最後の問題の場合は結果画面へ進みます。</p>
          </fieldset>
          <fieldset class="quiz-fieldset">
            <legend>設定</legend>
            <form action="quiz.php" method="post">
              <table class="input-form">
                <tr>
                  <th>問題種類</th>
                  <td>
                    <select class="form-select" name="quiz-type" id="quiz-type">
                      <?php
                      foreach ($rows as $row) {
                        if ($type === h($row["type"])) {
                          echo "<option value='" . h($row["type"]) . "' selected>" . h($row["type"]) . "</option>";
                        } else {
                          echo "<option value='" . h($row["type"]) . "'>" . h($row["type"]) . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>問題数</th>
                  <td>
                    <select class="form-select" name="quiz-count" id="quiz-count">
                      <?php
                      for ($i = SELECT_COUNT_MIN; $i <= SELECT_COUNT_MAX; $i++) {
                        if ($i === DEFAULT_SELECTED_NUMBER) {
                          echo "<option value='" . $i . "' selected>" . $i . "</option>";
                        } else {
                          echo "<option value='" . $i . "'>" . $i . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>音声の有無</th>
                  <td>
                    <input type="checkbox" class="form-checkbox" name="quiz-audio" id="quiz-audio" value="有">
                    <label for="quiz-audio" class="message">解答時に音声再生</label>
                  </td>
                </tr>
              </table>
              <div class="btn-wrapper">
                <input class="btn btn-blue btn-normal poyopoyo" type="submit" value="スタート">
              </div>
            </form>
          </fieldset>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>