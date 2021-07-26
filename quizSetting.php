<?php
session_start();
$_SESSION = array();
session_destroy();
require_once("config.php");

function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$selectCountStart = 5;
$selectCountEnd = 20;
$defaultSelectedNumber = 10;
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
  $stmt = $pdo->prepare("select type from words group by type");
  $stmt->execute();
  $rows = $stmt->fetchAll();
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
        <?php else : ?>
          <p class="title">問題準備</p>
          <fieldset class="quiz-fieldset">
            <legend>説明</legend>
            <p class="message">・問題の種類について</p>
            <table class="description-table">
              <tr>
                <th>和訳</th>
                <td>問題の英単語に対して同じ意味を表す適切な日本語を選択する</td>
              </tr>
              <tr>
                <th>英訳</th>
                <td>問題の日本語に対して同じ意味を表す適切な英単語を選択する</td>
              </tr>
            </table>
            <p class="message">・解答方法について</p>
            <p class="message">　4つの選択肢の中から適切なものを選択し、"解答する"ボタンを押下してください。正解 or 不正解のメッセージが表示され"次へ"ボタンが押下可能になります。ボタン押下で次の問題へ進みます。最後の問題の場合は結果画面へ進みます。</p>
            <p class="message">・例題</p>
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
                      for ($i = $selectCountStart; $i <= $selectCountEnd; $i++) {
                        if ($i === $defaultSelectedNumber) {
                          echo "<option value='" . $i . "' selected>" . $i . "</option>";
                        } else {
                          echo "<option value='" . $i . "'>" . $i . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </td>
                </tr>
              </table>
              <div class="btn-wrapper">
                <input class="btn btn-blue btn-normal" type="submit" value="スタート">
              </div>
            </form>
          </fieldset>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>