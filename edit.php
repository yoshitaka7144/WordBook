<?php
session_start();
$_SESSION = array();
session_destroy();
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

$btnColor = ["" => "", REGIST_TYPE_CREATE => "btn-blue", REGIST_TYPE_UPDATE => "btn-green", REGIST_TYPE_DELETE => "btn-red"];

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <?php if ($pageType === PAGE_TYPE_CONFIRM) : ?>
        <div class="edit-confirm">
          <p class="title"><?= h($registType) ?>：内容確認</p>
          <table class="confirm-table">
            <?php if ($registType !== REGIST_TYPE_CREATE) : ?>
              <tr>
                <th>ID</th>
                <td><?= h($inputId) ?></td>
              </tr>
            <?php endif ?>
            <tr>
              <th>種類</th>
              <td><?= h($inputType) ?></td>
            </tr>
            <tr>
              <th>問題</th>
              <td><?= h($inputQuestion) ?></td>
            </tr>
            <tr>
              <th>答え</th>
              <td><?= h($inputAnswer) ?></td>
            </tr>
          </table>
          <p class="message">上記の内容で<?= h($registType) ?>処理を行います</p>
          <div class="btn-wrapper">
            <form action="" method="post">
              <input class="btn btn-gray btn-normal" type="submit" value="戻る">
              <input type="hidden" name="inputId" value="<?php echo h($inputId) ?>">
              <input type="hidden" name="inputType" value="<?php echo h($inputType) ?>">
              <input type="hidden" name="inputQuestion" value="<?php echo h($inputQuestion) ?>">
              <input type="hidden" name="inputAnswer" value="<?php echo h($inputAnswer) ?>">
            </form>
            <form action="dataRegist.php" method="post">
              <input class="btn btn-normal <?= $btnColor[$registType] ?>" type="submit" name="registType" value="<?php echo $registType ?>">
              <input type="hidden" name="inputId" value="<?php echo h($inputId) ?>">
              <input type="hidden" name="inputType" value="<?php echo h($inputType) ?>">
              <input type="hidden" name="inputQuestion" value="<?php echo h($inputQuestion) ?>">
              <input type="hidden" name="inputAnswer" value="<?php echo h($inputAnswer) ?>">
            </form>
          </div>
        </div>
      <?php else : ?>
        <div class="edit-input">
          <p class="title">編集</p>
          <p class="message">問題の登録、更新、削除を行います</p>
          <p class="message">データ一覧の行を選択で参照できます</p>
          <p class="message color-blue">※登録時はIDは不要です</p>
          <?php if ($pageType === PAGE_TYPE_ERROR) : ?>
            <div id="error-area">
              <?php foreach ($errors as $error) : ?>
                <p class="message color-red"><?php echo $error ?></p>
              <?php endforeach ?>
            </div>
          <?php endif ?>
          <form action="" method="post">
            <table class="input-form">
              <tr>
                <th>ID</th>
                <td><input type="text" class="form-text" name="inputId" id="inputId" value="<?php echo h($inputId) ?>"></td>
              </tr>
              <tr>
                <th>種類</th>
                <td>
                  <select class="form-select" name="inputType" id="inputType">
                    <option value="和訳" <?php if ($inputType === "和訳") {
                                          echo "selected";
                                        } ?>>和訳</option>
                    <option value="英訳" <?php if ($inputType === "英訳") {
                                          echo "selected";
                                        } ?>>英訳</option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>問題</th>
                <td><input type="text" class="form-text" name="inputQuestion" id="inputQuestion" value="<?php echo h($inputQuestion) ?>"></td>
              </tr>
              <tr>
                <th>答え</th>
                <td><input type="text" class="form-text" name="inputAnswer" id="inputAnswer" value="<?php echo h($inputAnswer) ?>"></td>
              </tr>
            </table>
            <div class="btn-wrapper">
              <input class="btn btn-small btn-blue" type="submit" name="create" value="登録">
              <input class="btn btn-small btn-green" type="submit" name="update" value="更新">
              <input class="btn btn-small btn-red" type="submit" name="delete" value="削除">
            </div>
          </form>
        </div>
        <div class="edit-table">
          <p class="title">登録データ一覧</p>
          <fieldset class="radio-fieldset">
            <legend>データ表示対象</legend>
            <input id="radio-all" name="radio" type="radio" value="all" checked>
            <label for="radio-all" class="radio-label">全データ</label>
            <input id="radio-japanese" name="radio" type="radio" value="和訳">
            <label for="radio-japanese" class="radio-label">和訳</label>
            <input id="radio-English" name="radio" type="radio" value="英訳">
            <label for="radio-English" class="radio-label">英訳</label>
          </fieldset>
          <p class="message">データ行を選択すると編集欄に反映されます</p>
          <?php if (!empty($dbErrorMessage)) : ?>
            <p class="message color-red"><?= DB_ERROR_MESSAGE ?></p>
            <p class="message"><?= $dbErrorMessage ?></p>
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
              <p class="message"><span id="current-page">1</span> / <span id="max-page"><?php echo $maxPage ?></span> ページ</p>
              <div class="btn-wrapper">
                <input class="btn btn-blue btn-normal" id="btn-prev" type="button" value="前へ" disabled>
                <input class="btn btn-blue btn-normal" id="btn-next" type="button" value="次へ" <?php echo (int)$maxPage === 1 ? "disabled" : "" ?>>
              </div>
            </div>
          <?php endif ?>
        </div>
      <?php endif ?>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>