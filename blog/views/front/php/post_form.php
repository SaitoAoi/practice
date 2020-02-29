<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();//呼び出しは1回でOK
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
<title>新規投稿</title>
<link rel="stylesheet" type="text/css" href="../../css/register_form.css" />
</head>
<body>  
<p><?php echo $name?>さんがログイン中。</p>
<div id="Wrap">
<form action="http://localhost/php_project/views/front/php/post_catch.php" method="POST">
<div id="title">
<h1>投稿フォーム</h1>
</div>
<div id="Main_wrap">
<div class="middle_wrap">
<div class="small_wrap">タイトル</div>
<div class="small_wrap">
<input type="text" class="input" name="title" />
</div>
</div>
<div class="middle_wrap">
<div class="small_wrap">本文</div>
<div class="small_wrap">
<textarea class="input" name="body"></textarea>
</div>
</div>
<div class="middle_wrap">
<input type="submit" value="投稿" />
</div>
</form>
</div>
</div>
<p><a href="http://localhost/php_project/views/front/php/post.php">投稿一覧へ戻る</a></p>
<p><a href="http://localhost/php_project/views/front/php/new_mypage.php">マイページ</a></p>
</div>
</body>
</html>