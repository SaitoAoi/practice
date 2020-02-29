<?php
session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$search_word=$_POST["serach_word"];//検索ワード
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"]; //ログイン中のユーザー名

//検索ワードに当てはまる、タイトル、記事内容を見つける
$sql="SELECT user_name, title, body, created_at, post_id from post where title like '$search_word' or body like '$search_word'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$text="";

foreach ($items as $item) {
  $user_name=$item["user_name"];
  $title = $item["title"];
  $body =$item["body"];
  $created_at=$item["created_at"];
  $post_id=$item["post_id"];
 
  $text .=
  "<div id='wrap'>
  <div id='name'>
    <div id='n1'>ユーザー:</div>
    <div id='n2'>$user_name</div>
  </div>
  <div id='title'>
    <div id='n3'>タイトル:</div>
    <div id='n4'>$title</div>
  </div>
  <div id='body'>
    <div id='n8'>本文　　:</div>
    <div id='n5'>$body</div>
  </div>
  <div id='date'>
    <div id='n6'>作成日時:</div>
    <div id='n7'>$created_at</div>
    <form action='http://localhost/php_project/views/front/php/post_details.php' method='POST'>
    <input type='hidden' name='post_id' value='$post_id'>
    <input type='submit' value='投稿詳細'>
    </form>
  </div>

  </div>";

}

?>

<!DOCTYPE html>
<html>
<head>
<title>投稿一覧</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../css/post.css" />
</head>
<body>
<p><?php echo $name?>さんがログイン中。</p>
<p>検索ワード:<?php echo $search_word ?></p>
<p>検索結果</p>
<div id="reslut"><?php echo $text ?></div>
<a href="http://localhost/php_project/views/front/php/post.php"><input type="button" value="投稿一覧へ"></a>
<a href="http://localhost/php_project/views/front/php/post_form.php"><input type="button" value="新規投稿"></a>
<a href="http://localhost/php_project/views/front/php/new_mypage.php"><input type="button" value="マイページ"></a>
</body>
</html>