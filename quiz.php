<?php
session_start();
require_once("config.php");
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$dbErrorMessage = "";
$settingType = filter_input(INPUT_POST, "quiz-type");
$settingCount = filter_input(INPUT_POST, "quiz-count");
$currentCount = filter_input(INPUT_POST, "current-count");

if (!empty($settingType) && !isset($_SESSION["quizData"])) {
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

    // 問題数不足チェック
    if (count($rows) < $settingCount) {
      $dbErrorMessage = "指定された種類の問題数が不足しています。データを追加登録してください。";
    } else {
      // 問題データ作成
      foreach ($rows as $row) {
        $answers[] = $row["answer"];
      }
      foreach ($rows as $row) {
        $data["question"] = $row["question"];
        $data["answer"] = $row["answer"];
        $data["choices"] = [];
        shuffle($answers);
        foreach ($answers as $answer) {
          if ($row["answer"] !== $answer) {
            $data["choices"][] = $answer;
            if (count($data["choices"]) === (CHOICES_COUNT - 1)) {
              $data["choices"][] = $row["answer"];
              shuffle($data["choices"]);
              $_SESSION["quizData"][] = $data;
              break;
            }
          }
        }
      }

      $_SESSION["quizCount"] = (int)$settingCount;
      $_SESSION["quizType"] = $settingType;
      $_SESSION["quizCurrentIndex"] = 0;
      $_SESSION["startTime"] = time();
    }
  } catch (PDOException $e) {
    $dbErrorMessage = $e->getMessage();
  }
} else {
  if (!isset($_SESSION["quizData"])) {
    header("Location: ./quizSetting.php");
    exit;
  } else {
    if (!empty($currentCount) && ($_SESSION["quizCurrentIndex"] === ($currentCount - 1))) {
      $incorrectQuestion = filter_input(INPUT_POST, "incorrect-question");
      $incorrectAnswer = filter_input(INPUT_POST, "incorrect-answer");
      if (!empty($incorrectQuestion) && !empty($incorrectAnswer)) {
        $_SESSION["incorrect"][] = ["question" => $incorrectQuestion, "answer" => $incorrectAnswer];
      }
      $_SESSION["quizCurrentIndex"]++;
      if ($_SESSION["quizCount"] < ($_SESSION["quizCurrentIndex"] + 1)) {
        $_SESSION["finished"] = true;
      }
    }
  }
}

$finished = isset($_SESSION["finished"]) ? $_SESSION["finished"] : false;
if ($finished) {
  $incorrectCount = isset($_SESSION["incorrect"]) ? count($_SESSION["incorrect"]) : 0;
  if (!isset($_SESSION["endTime"])) {
    $_SESSION["endTime"] = time();
  }
} else {
  if (empty($dbErrorMessage)) {
    $quizData = $_SESSION["quizData"];
    $question = $quizData[$_SESSION["quizCurrentIndex"]]["question"];
    $answer = $quizData[$_SESSION["quizCurrentIndex"]]["answer"];
    $choices = $quizData[$_SESSION["quizCurrentIndex"]]["choices"];
    $count = $_SESSION["quizCurrentIndex"] + 1;
  }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <div class="quiz">
        <?php if (empty($dbErrorMessage)) : ?>
          <?php if ($finished) : ?>
            <p class="title">クイズ結果</p>
            <div class="result">
              <table class="result-table">
                <tr>
                  <th>問題種類</th>
                  <td><?= $_SESSION["quizType"] ?></td>
                </tr>
                <tr>
                  <th>問題数</th>
                  <td><?= $_SESSION["quizCount"] ?></td>
                </tr>
                <tr>
                  <th>不正解数</th>
                  <td><?= $incorrectCount ?></td>
                </tr>
                <tr>
                  <th>正答率</th>
                  <td><?= round(($_SESSION["quizCount"] - $incorrectCount) / ($_SESSION["quizCount"]) * 100, 1) ?>%</td>
                </tr>
                <tr>
                  <th>経過時間</th>
                  <td><?= $_SESSION["endTime"] - $_SESSION["startTime"] ?>秒</td>
                </tr>
              </table>
              <fieldset class="incorrect-area">
                <legend>不正解の問題と正解</legend>
                <?php
                if (isset($_SESSION["incorrect"])) {
                  foreach ($_SESSION["incorrect"] as $item) {
                    echo  "<p class='message'>問題：" . h($item["question"]) . "</p>";
                    echo  "<p class='message'>正解：" . h($item["answer"]) . "</p><br>";
                  }
                }
                ?>
              </fieldset>
            </div>
            <div class="btn-wrapper">
              <a href="quizSetting.php?type=<?= $_SESSION["quizType"] ?>" class="btn btn-blue btn-normal">クイズ設定画面へ</a>
              <a href="index.php" class="btn btn-blue btn-normal">トップ画面へ</a>
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
            <p id="notification-area"><span id="answer-message" class="message"></span></p>
            <form action="" method="post">
              <input type="hidden" name="incorrect-question" value="">
              <input type="hidden" name="incorrect-answer" value="">
              <input type="hidden" name="current-count" value="<?= $count ?>">
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
        <?php else : ?>
          <p class="message color-red"><?= DB_ERROR_MESSAGE ?></p>
          <p class="message"><?= $dbErrorMessage ?></p>
          <div class="btn-wrapper">
            <a href="edit.php" class="btn btn-green btn-normal">管理画面へ</a>
            <a href="index.php" class="btn btn-blue btn-normal">トップ画面へ</a>
          </div>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>