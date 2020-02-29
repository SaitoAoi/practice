<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );
$conn = dbConnect();//呼び出しは1回でOK
$errors = array();
$userID = $_SESSION["user_id"];
$post_id=$_POST["post_id"];
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
  <title>新規コメント</title>
  <link rel="stylesheet" type="text/css" href="../../css/_form.css" />
  </head>
  <body>
  <p><?php echo $name?>さんがログイン中。</p>
  <div id="Wrap">
  <form action="http://localhost/php_project/views/front/php/comment_catch.php" method="POST">
  <div id="title">
  <h1>新規コメント</h1>
  </div>
  <div id="Main_wrap">
  <div id="body">
  <textarea class="input" name="body"></textarea>
  </div>
  <input type="hidden" name="post_id" value="<?=$post_id?>">
  <div id="btn"><input type="submit" value="コメントする" />
  </div>      
  </form>
  </div>
  <br>
  <a href="http://localhost/php_project/views/front/php/new_mypage.php">
  マイページ
  </div>
  </body>
</html>