<?php

/**
 * ユーザー登録ページ
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("config.php");
require_once("util.php");
session_start();
unsetQuizSession();

// 非ログイン時
if (isset($_SESSION["user"])) {
  header("Location: ./index.php");
  exit;
}

$inputUserName = filter_input(INPUT_POST, "inputUserName");
$inputPassword = filter_input(INPUT_POST, "inputPassword");
$createUser = filter_input(INPUT_POST, "createUser");
$errors = [];

if ($createUser === CREATE_USER) {
  if (empty($inputUserName)) {
    $errors[] = "ユーザー名を入力してください。";
  } elseif (mb_strlen($inputUserName) > 10) {
    $errors[] = "ユーザー名は10文字以内で入力してください。";
  } elseif (empty($inputPassword)) {
    $errors[] = "パスワードを入力してください。";
  } elseif (!preg_match("/^[a-zA-Z0-9]{4,}$/", $inputPassword)) {
    $errors[] = "パスワードは半角英数字4文字以上を入力してください。";
  } else {
    // 登録処理
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

      $stmt = $pdo->prepare("select count(*) from users where name = :name");
      $stmt->bindValue(":name", $inputUserName);
      $stmt->execute();
      $rowCount = $stmt->fetchColumn();
      if ($rowCount > 0) {
        // 入力されたユーザー名のデータが既に存在している場合
        $errors[] = "同じユーザー名が存在しています。別のユーザー名を入力してください。";
      } else {
        // 登録処理
        $name = $inputUserName;
        $password = password_hash($inputPassword, PASSWORD_DEFAULT);
        $answerCount = 0;
        $registCount = 0;
        $lastAccessDate = date("Y-m-d");
        $stmt = $pdo->prepare("insert into users (name, password, answer_count, regist_count, last_access_date) values (:name, :password, :answer_count, :regist_count, :last_access_date)");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":password", $password);
        $stmt->bindValue(":answer_count", $answerCount);
        $stmt->bindValue(":regist_count", $registCount);
        $stmt->bindValue(":last_access_date", $lastAccessDate);
        $stmt->execute();

        // セッションへデータ保存
        $_SESSION["user"]["name"] = $name;
        $_SESSION["user"]["answerCount"] = $answerCount;
        $_SESSION["user"]["registCount"] = $registCount;
        $_SESSION["user"]["lastAccessDate"] = $lastAccessDate;

        // トップページへ遷移させる
        header("Location: ./index.php");
        exit;
      }
    } catch (PDOException $e) {
      $errors[] = $e->getMessage();
    }
  }
}

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <div class="login">
        <form action="" method="post">
          <p class="title">ユーザー登録</p>
          <?php if (!empty($errors)) : ?>
            <?php foreach ($errors as $error) : ?>
              <p class="message color-red"><?= $error ?></p>
            <?php endforeach ?>
          <?php endif ?>
          <input type="text" name="inputUserName" id="inputUserName" placeholder="ユーザ名" autocomplete="off" value="<?= h($inputUserName) ?>" maxlength="10">
          <input type="password" name="inputPassword" id="inputPassword" placeholder="パスワード(半角英数字4文字以上)" autocomplete="off">
          <input class="btn btn-green btn-normal" type="submit" value="登録">
          <p class="message"><a class="link" href="login.php">既に登録済みの方はこちらからログイン</a></p>
          <input type="hidden" name="createUser" value="<?= CREATE_USER ?>">
        </form>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>