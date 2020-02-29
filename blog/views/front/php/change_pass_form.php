<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"];
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
  <meta charset="utf-8" />
  <title>パスワードの変更</title>
  <link rel="stylesheet" type="text/css" href="../../css/register_form.css" />
  </head>
  <body>
  <p><?php echo $name?>さんがログイン中。</p>
  <form action="http://localhost/php_project/views/front/php/change_pass_catch.php" method="POST">
  <p>現在のパスワード:<input type="text" name="old_pass"></p>
  <p>新しいパスワード:<input type="text" name="new_pass"></p>
  <input type="submit" value="変更">  
  </form>
  <br>
  <a href="http://localhost/php_project/views/front/php/new_mypage.php">戻る</a>
  </body>
</html>
  