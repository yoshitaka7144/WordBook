<?php 

/**
 * ログアウト処理
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

// セッションを全て破棄
session_start();
$_SESSION = array();
session_destroy();

// トップページへ
header("Location: ./index.php");