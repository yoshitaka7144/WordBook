<?php
session_start();
require_once("config.php");
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$quizData = isset($_SESSION["quizData"]) ? $_SESSION["quizData"] : "";
$inputType = filter_input(INPUT_POST, "quiz-type");
$inputCount = filter_input(INPUT_POST, "quiz-count");

if (!empty($inputType)) {
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
    $stmt->bindValue(":type", $inputType);
    $stmt->bindValue(":limit", (int)$inputCount, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $_SESSION["quizData"] = $rows;
    $_SESSION["quizCount"] = $inputCount;
    $_SESSION["quizCurrentIndex"] = 0;
  } catch (PDOException $e) {
    $dbErrorMessage = $e->getMessage();
  }
} else {
  if (empty($quizData)) {
    header("Location: ./quizSetting.php");
  } else {
    $_SESSION["quizCurrentIndex"]++;
  }
}

$quizData = $_SESSION["quizData"];
$question = $quizData[$_SESSION["quizCurrentIndex"]]["question"];
$answer = $quizData[$_SESSION["quizCurrentIndex"]]["answer"];
$count = $_SESSION["quizCurrentIndex"]+1;
$choices = [];
shuffle($quizData);
foreach($quizData as $quiz){
  if($quiz["answer"] !== $answer){
    $choices[] = $quiz["answer"];
    if(count($choices) === 3) {
      $choices[] = $answer;
      shuffle($choices);
      break;
    }
  }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <div class="quiz">
        <div class="sentence">
          <p class="message"><?="第".$count."問目"?></p>
          <p class="message"><?=$question?></p>
        </div>
        <div class="choices">
          <?php
          for($i=1;$i<=count($choices);$i++){
            $choice = h($choices[$i-1]);
            echo "<div id='choice-area-".$i."'><input type='radio' name='choice' id='choice-".$i."' value='".$choice."'><label for='choice-".$i."' class=''>".$choice."</label></div>";
          }
          ?>
        </div>
        <form action="" method="post">
          <input class="btn btn-blue" type="submit" value="次へ">
        </form>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>