<?php
session_start();
require_once("config.php");
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$incorrectQuestion = filter_input(INPUT_POST, "incorrect-question");
$incorrectAnswer = filter_input(INPUT_POST, "incorrect-answer");
if (!empty($incorrectQuestion) && !empty($incorrectAnswer)) {
  $_SESSION["incorrect"][] = ["question" => $incorrectQuestion, "answer" => $incorrectAnswer];
}

$settingType = filter_input(INPUT_POST, "quiz-type");
$settingCount = filter_input(INPUT_POST, "quiz-count");
$finished = false;

if (!empty($settingType)) {
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
    $stmt = $pdo->prepare("select id, type, question, answer from words where type = :type order by rand() limit :limit");
    $stmt->bindValue(":type", $settingType);
    $stmt->bindValue(":limit", (int)$settingCount, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $_SESSION["quizData"] = $rows;
    $_SESSION["quizCount"] = (int)$settingCount;
    $_SESSION["quizCurrentIndex"] = 0;
  } catch (PDOException $e) {
    $dbErrorMessage = $e->getMessage();
  }
} else {
  if (!isset($_SESSION["quizData"])) {
    header("Location: ./quizSetting.php");
    exit;
  } else {
    $_SESSION["quizCurrentIndex"]++;
    if ($_SESSION["quizCount"] < ($_SESSION["quizCurrentIndex"] + 1)) {
      $finished = true;
    }
  }
}

if ($finished) {
  $incorrectCount = isset($_SESSION["incorrect"]) ? count($_SESSION["incorrect"]) : 0;
} else {
  $quizData = $_SESSION["quizData"];
  $question = $quizData[$_SESSION["quizCurrentIndex"]]["question"];
  $answer = $quizData[$_SESSION["quizCurrentIndex"]]["answer"];
  $count = $_SESSION["quizCurrentIndex"] + 1;
  $choices = [];
  shuffle($quizData);
  foreach ($quizData as $quiz) {
    if ($quiz["answer"] !== $answer) {
      $choices[] = $quiz["answer"];
      if (count($choices) === CHOICES_COUNT - 1) {
        $choices[] = $answer;
        shuffle($choices);
        break;
      }
    }
  }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <div class="quiz">
        <?php if ($finished) : ?>
          <div class="result">
            <p class="title">クイズ結果</p>
            <p class="message">aaaaaaa</p>
            <p><?= "問題数:" . $_SESSION["quizCount"] ?></p>
            <p><?= "不正解数:" . $incorrectCount ?></p>
            <?php
            if (isset($_SESSION["incorrect"])) {
              echo "<p>不正解だった問題と答え</p>";
              foreach ($_SESSION["incorrect"] as $item) {
                echo  "<p>問題:" . h($item["question"]) . "答え：" . h($item["answer"]) . "</p>";
              }
            }
            ?>
            <div class="btn-wrapper">
              <a href="quizSetting.php" class="btn btn-blue btn-normal">クイズ設定画面へ</a>
              <a href="index.php" class="btn btn-blue btn-normal">トップ画面へ</a>
            </div>
          </div>
        <?php else : ?>
          <p class="title"><?= "第 " . $count . " 問目" ?></p>
          <fieldset class="quiz-fieldset">
            <legend>問題</legend>
            <p id="text-question"><?= h($question) ?></p>
            <p id="text-answer"><?= h($answer) ?></p>
          </fieldset>
          <fieldset class="quiz-fieldset">
            <legend>選択肢</legend>
            <div class="choices">
              <?php
              for ($i = 1; $i <= count($choices); $i++) {
                $choice = $choices[$i - 1];
                echo "<div class='choice-area' id='choice-area-" . $i . "'><input type='radio' name='choice' id='choice-" . $i . "' value='" . h($choice) . "'><label for='choice-" . $i . "' class='choice-label'>" . h($choice) . "</label></div>";
              }
              ?>
            </div>
          </fieldset>
          <p id="answer-message" class="message"></p>
          <form action="" method="post">
            <input type="hidden" name="incorrect-question" value="">
            <input type="hidden" name="incorrect-answer" value="">
            <div class="btn-wrapper">
              <input class="btn btn-green btn-normal" type="button" id="btn-answer" value="解答する">
              <?php if ($count === $_SESSION["quizCount"]) : ?>
                <input class="btn btn-blue btn-normal" id="btn-quiz-next" type="submit" value="結果画面へ" disabled>
              <?php else : ?>
                <input class="btn btn-blue btn-normal" id="btn-quiz-next" type="submit" value="次へ" disabled>
              <?php endif ?>
            </div>
          </form>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>