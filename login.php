<?php

/**
 * ログインページ
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */
require_once("config.php");
require_once("util.php");
session_start();
unsetSession();
if (isset($_SESSION["user"])) {
  header("Location: ./index.php");
}

$inputUserName = filter_input(INPUT_POST, "inputUserName");
$inputPassword = filter_input(INPUT_POST, "inputPassword");
$loginUser = filter_input(INPUT_POST, "loginUser");
$errors = [];

if ($loginUser === LOGIN_USER) {
  if (empty($inputUserName)) {
    $errors[] = "ユーザー名を入力してください。";
  } elseif (empty($inputPassword)) {
    $errors[] = "パスワードを入力してください。";
  } else {
    // ログイン処理
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

      $stmt = $pdo->prepare("select name, password, answer_count, regist_count, last_access_date from users where name = :name");
      $stmt->bindValue(":name", $inputUserName);
      $stmt->execute();
      $row = $stmt->fetch();

      if (password_verify($inputPassword, $row["password"])) {
        // セッションへデータ保存
        $_SESSION["user"]["name"] = $inputUserName;
        $_SESSION["user"]["answerCount"] = $row["answer_count"];
        $_SESSION["user"]["registCount"] = $row["regist_count"];
        $_SESSION["user"]["lastAccessDate"] = date("Y-m-d");

        // 最終アクセス日から日付を跨いだログインの場合
        if ($row["last_access_date"] !== $_SESSION["user"]["lastAccessDate"]) {
          $plusCount = floor($_SESSION["user"]["answerCount"] / USER_LEVEL_DENOMINATOR);
          if ($_SESSION["user"]["registCount"] < $plusCount) {
            $_SESSION["user"]["registCount"] = $plusCount;
          }

          // 更新
          $stmt = $pdo->prepare("update users set regist_count = :regist_count, last_access_date = :last_access_date where name = :name");
          $stmt->bindValue(":regist_count", $_SESSION["user"]["registCount"]);
          $stmt->bindValue(":last_access_date", $_SESSION["user"]["lastAccessDate"]);
          $stmt->bindValue(":name", $_SESSION["user"]["name"]);
          $stmt->execute();
        }

        // トップページへ遷移させる
        header("Location: ./index.php");
        exit;
      }else{
        $errors[] = "ユーザー名またはパスワードが間違っています。";
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
          <p class="title">ログイン</p>
          <?php if (!empty($errors)) : ?>
            <?php foreach ($errors as $error) : ?>
              <p class="message color-red"><?= $error ?></p>
            <?php endforeach ?>
          <?php endif ?>
          <input type="text" name="inputUserName" id="inputUserName" placeholder="ユーザ名" autocomplete="off">
          <input type="password" name="inputPassword" id="inputPassword" maxlength="8" placeholder="パスワード" autocomplete="off">
          <input class="btn btn-green btn-normal" type="submit" value="ログイン">
          <p class="message"><a href="createUser.php">新規ユーザー登録はこちらから</a></p>
          <input type="hidden" name="loginUser" value="<?= LOGIN_USER ?>">
        </form>
      </div>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>