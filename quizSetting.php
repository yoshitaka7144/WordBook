<?php
session_start();
$_SESSION = array();
session_destroy();
require_once("config.php");

function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

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
          <p class="error-message"><?=DB_ERROR_MESSAGE?></p>
          <p class="message"><?=$dbErrorMessage?></p>
        <?php else : ?>
          <form action="quiz.php" method="post">
            <p class="title">問題設定</p>
            <p class="message">問題種類</p>
            <select name="quiz-type" id="quiz-type">
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
            <p class="message">問題数</p>
            <select name="quiz-count" id="quiz-count">
              <?php
              for ($i = 5; $i <= 20; $i++) {
                if ($i === 10) {
                  echo "<option value='" . $i . "' selected>" . $i . "</option>";
                } else {
                  echo "<option value='" . $i . "'>" . $i . "</option>";
                }
              }
              ?>
            </select>
            <div>
            <input class="btn btn-blue" type="submit" value="スタート">
            </div>
          </form>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>