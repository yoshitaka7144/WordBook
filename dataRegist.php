<?php

/**
 * 問題データ登録処理
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

session_start();
require_once("config.php");
require_once("validation.php");

// 二重処理防止用リダイレクト
if (isset($_SESSION["dbConnected"])) {
    header("Location: edit.php");
    exit;
}

// 入力内容
$inputId = filter_input(INPUT_POST, "inputId");
$inputType = filter_input(INPUT_POST, "inputType");
$inputQuestion = filter_input(INPUT_POST, "inputQuestion");
$inputAnswer = filter_input(INPUT_POST, "inputAnswer");

// DB処理種類
$registType = filter_input(INPUT_POST, "registType");

// 入力内容チェック処理
$inputErrors = validation($_POST, $registType);

$resultMessage = "";
$dbErrored = false;
if (!empty($inputErrors)) {
    $resultMessage = "不正な入力内容があります。やり直してください。";
} else {
    // データベース処理
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

        if ($registType === REGIST_TYPE_CREATE) {
            // 登録処理
            $stmt = $pdo->prepare("insert into words (type, question, answer) values (:type, :question, :answer)");
            $stmt->bindValue(":type", $inputType);
            $stmt->bindValue(":question", $inputQuestion);
            $stmt->bindValue(":answer", $inputAnswer);
            $stmt->execute();
            $resultMessage = $registType . "処理が完了しました。";
        } elseif ($registType === REGIST_TYPE_UPDATE) {
            // 更新処理
            $stmt = $pdo->prepare("update words set type = :type, question = :question, answer = :answer where id = :id");
            $stmt->bindValue(":type", $inputType);
            $stmt->bindValue(":question", $inputQuestion);
            $stmt->bindValue(":answer", $inputAnswer);
            $stmt->bindValue(":id", (int)$inputId, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count <= 0) {
                $resultMessage = "更新対象データが存在しません。";
            } else {
                $resultMessage = $registType . "処理が完了しました。";
            }
        } elseif ($registType === REGIST_TYPE_DELETE) {
            // 削除処理
            $stmt = $pdo->prepare("delete from words where id = :id");
            $stmt->bindValue(":id", (int)$inputId, pdo::PARAM_INT);
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count <= 0) {
                $resultMessage = "削除対象データが存在しません。";
            } else {
                $resultMessage = $registType . "処理が完了しました。";
            }
        } else {
            $resultMessage = "不正な処理が行われました。やり直してください。";
        }

        // 二重処理防止用にセッションへ代入
        $_SESSION["dbConnected"] = true;
    } catch (PDOException $e) {
        $resultMessage = $e->getMessage();
        $dbErrored = true;
    }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
    <div class="container">
        <div class="main-contents">
            <div class="edit-confirm">
                <?php if ($dbErrored) : ?>
                    <p class="message color-red"><?= DB_ERROR_MESSAGE ?></p>
                <?php endif ?>
                <p class="message"><?= $resultMessage ?></p>
                <div class="btn-wrapper">
                    <a href="edit.php" class="btn btn-green btn-normal">管理画面</a>
                    <a href="index.php" class="btn btn-blue btn-normal">トップ画面</a>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>