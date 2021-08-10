<?php

/**
 * クイズ画面処理
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("config.php");
require_once("util.php");

session_start();
$dbErrorMessage = "";

// クイズ設定
$settingType = filter_input(INPUT_POST, "quiz-type");
$settingCount = filter_input(INPUT_POST, "quiz-count");
$settingAudio = filter_input(INPUT_POST, "quiz-audio");

// 解答した現在の問題数
$currentCount = filter_input(INPUT_POST, "current-count");

if (!empty($settingType) && !isset($_SESSION["quizData"])) {
  // 準備画面経由で初めてクイズ画面表示の場合
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

    // 問題取得
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
        // 取得データのコピー作成
        $tmpRows[] = array("question" => $row["question"], "answer" => $row["answer"]);
      }
      foreach ($rows as $row) {
        $addedData = false;
        $data["question"] = $row["question"];
        $data["answer"] = $row["answer"];
        $data["choices"] = [];

        // 選択肢を作成
        // 正解は１つ、それ以外は不正解となるように
        shuffle($tmpRows);
        foreach ($tmpRows as $tmpRow) {
          if ($row["answer"] !== $tmpRow["answer"] && $row["question"] !== $tmpRow["question"]) {
            $data["choices"][] = $tmpRow["answer"];
            if (count($data["choices"]) === (CHOICES_COUNT - 1)) {
              $data["choices"][] = $row["answer"];
              shuffle($data["choices"]);
              $_SESSION["quizData"][] = $data;
              $addedData = true;
              break;
            }
          }
        }

        // データ不足により選択肢が正しく作成できない場合
        if (!$addedData) {
          $dbErrorMessage = "選択肢作成にてエラーが発生しました。選択肢の数が不足しています。問題を追加登録してください。";
          break;
        }
      }

      // 作成問題データや設定等をセッションへ保存
      $_SESSION["quizCount"] = (int)$settingCount;
      $_SESSION["quizType"] = $settingType;
      $_SESSION["enabledAudio"] = $settingAudio === "有" ? true : false;
      $_SESSION["quizCurrentIndex"] = 0;
      $_SESSION["startTime"] = time();
    }
  } catch (PDOException $e) {
    $dbErrorMessage = $e->getMessage();
  }
} else {
  // 2問目以降の場合
  if (!isset($_SESSION["quizData"])) {
    // 問題データが作成されていない場合はリダイレクト
    header("Location: ./quizSetting.php");
    exit;
  } else {
    if (!empty($currentCount) && ($_SESSION["quizCurrentIndex"] === ($currentCount - 1))) {
      // 不正解の問題、答えをセッションへ追加保存
      $incorrectQuestion = filter_input(INPUT_POST, "incorrect-question");
      $incorrectAnswer = filter_input(INPUT_POST, "incorrect-answer");
      if (!empty($incorrectQuestion) && !empty($incorrectAnswer)) {
        $_SESSION["incorrect"][] = ["question" => $incorrectQuestion, "answer" => $incorrectAnswer];
      }

      // 問題を次へ進める
      $_SESSION["quizCurrentIndex"]++;

      // 設定されている問題数を超えた場合
      if ($_SESSION["quizCount"] < ($_SESSION["quizCurrentIndex"] + 1)) {
        $_SESSION["finished"] = true;
      }
    }
  }
}

$finished = isset($_SESSION["finished"]) ? $_SESSION["finished"] : false;
if ($finished) {
  // クイズ終了の場合
  // 結果集計処理
  $incorrectCount = isset($_SESSION["incorrect"]) ? count($_SESSION["incorrect"]) : 0;
  if (!isset($_SESSION["endTime"])) {
    $_SESSION["endTime"] = time();
    if (isset($_SESSION["user"])) {
      $levelUpCount = floor((substr($_SESSION["user"]["answerCount"], -1)  + ($_SESSION["quizCount"] - $incorrectCount)) / USER_LEVEL_DENOMINATOR);
      $_SESSION["user"]["registCount"] += $levelUpCount;
      $_SESSION["user"]["answerCount"] += ($_SESSION["quizCount"] - $incorrectCount);
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
        $stmt = $pdo->prepare("update users set answer_count = :answer_count, regist_count = :regist_count where name = :name");
        $stmt->bindValue(":answer_count", $_SESSION["user"]["answerCount"]);
        $stmt->bindValue(":regist_count", $_SESSION["user"]["registCount"]);
        $stmt->bindValue(":name", $_SESSION["user"]["name"]);
        $stmt->execute();
      } catch (PDOException $e) {
        $dbErrorMessage = $e->getMessage();
      }
    }
  }
} else {
  if (empty($dbErrorMessage)) {
    // クイズ画面表示用情報
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
                  <td class="fadeup delay-time-1"><?= $_SESSION["quizType"] ?></td>
                </tr>
                <tr>
                  <th>問題数</th>
                  <td class="fadeup delay-time-2"><?= $_SESSION["quizCount"] ?></td>
                </tr>
                <tr>
                  <th>不正解数</th>
                  <td class="fadeup delay-time-3"><?= $incorrectCount ?></td>
                </tr>
                <tr>
                  <th>正答率</th>
                  <td class="fadeup delay-time-4"><?= round(($_SESSION["quizCount"] - $incorrectCount) / ($_SESSION["quizCount"]) * 100, 1) ?>%</td>
                </tr>
                <tr>
                  <th>経過時間</th>
                  <td class="fadeup delay-time-5"><?= $_SESSION["endTime"] - $_SESSION["startTime"] ?>秒</td>
                </tr>
                <?php if (isset($_SESSION["user"])) : ?>
                  <tr>
                    <th>累計正解数</th>
                    <td class="fadeup delay-time-6"><?= $_SESSION["user"]["answerCount"] ?></td>
                  </tr>
                  <tr>
                    <th>レベル</th>
                    <td class="">
                      <span id="user-level">
                        <?= floor(($_SESSION["user"]["answerCount"] - ($_SESSION["quizCount"] - $incorrectCount)) / USER_LEVEL_DENOMINATOR) ?>
                      </span>
                      <div id="count-progress">
                        <div id="count-progress-bar"></div>
                      </div>
                    </td>
                  </tr>
                <?php endif ?>
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
              <p id="text-question" class="fadein"><?= h($question) ?></p>
              <p id="text-answer"><?= h($answer) ?></p>
            </fieldset>
            <fieldset class="quiz-fieldset">
              <legend>選択肢</legend>
              <div id="choices" class="fadein">
                <?php
                for ($i = 1; $i <= count($choices); $i++) {
                  $choice = $choices[$i - 1];
                  echo "<div class='choice-area' id='choice-area-" . $i . "'><input type='radio' name='choice' id='choice-" . $i . "' value='" . h($choice) . "'><label for='choice-" . $i . "' class='choice-label'>" . h($choice) . "</label></div>";
                }
                ?>
              </div>
            </fieldset>
            <p id="notification-area"><span id="answer-message" class="fadein"></span></p>
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
            <?php if ($_SESSION["enabledAudio"]) : ?>
              <audio id="audio-correct" controls>
                <source src="./audio/correct.mp3">
              </audio>
              <audio id="audio-incorrect" controls>
                <source src="./audio/incorrect.mp3">
              </audio>
            <?php endif ?>
          <?php endif ?>
        <?php else : ?>
          <p class="message color-red"><?= DB_ERROR_MESSAGE ?></p>
          <p class="message"><?= $dbErrorMessage ?></p>
          <div class="btn-wrapper">
            <a href="edit.php" class="btn btn-green btn-normal">問題編集</a>
            <a href="index.php" class="btn btn-blue btn-normal">トップ画面</a>
          </div>
        <?php endif ?>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>
<?php if ($finished && isset($_SESSION["user"])) : ?>
  <script type="text/javascript">
    var ac = <?= $_SESSION["user"]["answerCount"] - ($_SESSION["quizCount"] - $incorrectCount) ?>;
    var cc = <?= $_SESSION["quizCount"] - $incorrectCount ?>;
    progressBarAnimate(ac, cc);

    function progressBarAnimate(answerCount, correctCount) {
      var answerCountOnesPlace = Number(answerCount.toString().slice(-1));
      $("#count-progress-bar").css("width", answerCountOnesPlace * 10 + "%");
      if (answerCountOnesPlace + correctCount < 10) {
        $("#count-progress-bar").animate({
          width: (answerCountOnesPlace + correctCount) * 10 + "%"
        }, 1000);
      } else {
        $("#count-progress-bar").animate({
          width: "100%"
        }, 1000, function() {
          $("#user-level").text(Number($("#user-level").text()) + 1);
          progressBarAnimate(0, correctCount - (10 - answerCountOnesPlace));
        });
      }
    }
  </script>
<?php endif ?>