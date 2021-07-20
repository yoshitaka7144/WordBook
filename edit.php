<?php

require_once("config.php");
require_once("validation.php");

function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$inputId = filter_input(INPUT_POST, "inputId");
$inputType = filter_input(INPUT_POST, "inputType");
$inputQuestion = filter_input(INPUT_POST, "inputQuestion");
$inputAnswer = filter_input(INPUT_POST, "inputAnswer");

$registType = "";
if (isset($_POST["create"])) {
  $registType = REGIST_TYPE_CREATE;
} elseif (isset($_POST["update"])) {
  $registType = REGIST_TYPE_UPDATE;
} elseif (isset($_POST["delete"])) {
  $registType = REGIST_TYPE_DELETE;
}

$pageType = PAGE_TYPE_DEFAULT;
if (!empty($registType)) {
  $errors = validation($_POST, $registType);
  if (empty($errors)) {
    $pageType = PAGE_TYPE_CONFIRM;
  } else {
    $pageType = PAGE_TYPE_ERROR;
  }
}

$dbErrorMessage = "";
if ($pageType !== PAGE_TYPE_CONFIRM) {
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
    $stmt = $pdo->prepare("select id, type, question, answer from words order by id limit 0, :limitCount");
    $stmt->bindValue(":limitCount", (int)MAX_TABLE_ROW_COUNT, pdo::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $stmt = $pdo->prepare("select count(*) from words");
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();
    $maxPage = ceil($rowCount / MAX_TABLE_ROW_COUNT);
  } catch (PDOException $e) {
    $dbErrorMessage = $e->getMessage();
  }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <?php if ($pageType === PAGE_TYPE_CONFIRM) : ?>
        <div class="edit-confirm">
          <p class="title"><?php echo $registType ?>内容確認</p>
          <?php if ($registType !== REGIST_TYPE_CREATE) : ?>
            <p class="message">ID：<?php echo h($inputId) ?></p>
          <?php endif ?>
          <p class="message">種類：<?php echo h($inputType) ?></p>
          <p class="message">問題：<?php echo h($inputQuestion) ?></p>
          <p class="message">答え：<?php echo h($inputAnswer) ?></p>
          <form action="" method="post">
            <input type="submit" value="戻る">
            <input type="hidden" name="inputId" value="<?php echo h($inputId) ?>">
            <input type="hidden" name="inputType" value="<?php echo h($inputType) ?>">
            <input type="hidden" name="inputQuestion" value="<?php echo h($inputQuestion) ?>">
            <input type="hidden" name="inputAnswer" value="<?php echo h($inputAnswer) ?>">
          </form>
          <form action="dataRegist.php" method="post">
            <input type="submit" name="registType" value="<?php echo $registType ?>">
            <input type="hidden" name="inputId" value="<?php echo h($inputId) ?>">
            <input type="hidden" name="inputType" value="<?php echo h($inputType) ?>">
            <input type="hidden" name="inputQuestion" value="<?php echo h($inputQuestion) ?>">
            <input type="hidden" name="inputAnswer" value="<?php echo h($inputAnswer) ?>">
          </form>
        </div>
      <?php else : ?>
        <div class="edit-input">
          <p class="title">問題編集</p>
          <p class="message">問題の登録、更新、削除を行います</p>
          <p class="message">データ一覧の行を選択で参照できます</p>
          <form action="" method="post">
            <?php if ($pageType === PAGE_TYPE_ERROR) : ?>
              <div id="error-area">
                <?php foreach ($errors as $error) : ?>
                  <p class="error-message"><?php echo $error ?></p>
                <?php endforeach ?>
              </div>
            <?php endif ?>
            <label for="inputId">ID(登録時は不要です)</label>
            <input type="text" name="inputId" id="inputId" value="<?php echo h($inputId) ?>">
            <label for="inputType">種類</label>
            <select name="inputType" id="inputType">
              <option value="和訳" <?php if ($inputType === "和訳") {
                                    echo "selected";
                                  } ?>>和訳</option>
              <option value="英訳" <?php if ($inputType === "英訳") {
                                    echo "selected";
                                  } ?>>英訳</option>
            </select>
            <label for="inputQuestion">問題</label>
            <input type="text" name="inputQuestion" id="inputQuestion" value="<?php echo h($inputQuestion) ?>">
            <label for="inputAnswer">答え</label>
            <input type="text" name="inputAnswer" id="inputAnswer" value="<?php echo h($inputAnswer) ?>">
            <input type="submit" name="create" value="登録">
            <input type="submit" name="update" value="更新">
            <input type="submit" name="delete" value="削除">
          </form>
        </div>
        <div class="edit-table">
          <p class="title">登録データ一覧</p>
          <p class="message">データを選択すると問題編集欄に反映されます</p>
          <div class="radio">
            <input id="radio-all" name="radio" type="radio" value="all" checked>
            <label for="radio-all" class="radio-label">全データ</label>
            <input id="radio-japanese" name="radio" type="radio" value="和訳">
            <label for="radio-japanese" class="radio-label">和訳</label>
            <input id="radio-English" name="radio" type="radio" value="英訳">
            <label for="radio-English" class="radio-label">英訳</label>
          </div>
          <?php if (!empty($dbErrorMessage)) : ?>
            <p class="message">データベース接続にてエラーが発生しています。</p>
            <?php echo $dbErrorMessage ?>
          <?php else : ?>
            <table id="data-table">
              <thead>
                <tr>
                  <th class="table-header">ID</th>
                  <th class="table-header">種類</th>
                  <th class="table-header">問題</th>
                  <th class="table-header">答え</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $row) : ?>
                  <?php echo "<tr class='data-row'>" ?>
                  <?php echo "<td>" . h($row["id"]) . "</td>" ?>
                  <?php echo "<td>" . h($row["type"]) . "</td>" ?>
                  <?php echo "<td>" . h($row["question"]) . "</td>" ?>
                  <?php echo "<td>" . h($row["answer"]) . "</td>" ?>
                  <?php echo "</tr>" ?>
                <?php endforeach ?>
              </tbody>
            </table>
            <div class="pagination">
              <p class="message"><span id="current-page">1</span> / <span id="max-page"><?php echo $maxPage ?></span></p>
              <input class="btn btn-blue" id="btn-prev" type="button" value="prev" disabled>
              <input class="btn btn-blue" id="btn-next" type="button" value="next" <?php echo (int)$maxPage === 1 ? "disabled" : "" ?>>
            </div>
          <?php endif ?>
        </div>
      <?php endif ?>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>