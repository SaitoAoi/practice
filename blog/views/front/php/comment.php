<?php
session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];//ログイン中のユーザーID
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"];//ログイン中のユーザーネーム
$post_id=$_POST["post_id"];//投稿記事ID
$sql = "SELECT title,body,created_at,post_id,user_name FROM post WHERE post_id = :post_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":post_id",$post_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$member="";
$user_name=$item["user_name"];//記事のユーザー名
$title = $item["title"];//タイトル
$body =$item["body"];//内容
$created_at=$item["created_at"];//投稿日時

$member .= 
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
</div>
</div>";

?>

<!DOCTYPE html>
<html>
<head>
<title>コメント</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../css/post.css" />
</head>
<body>
<h1>コメント</h1>
<p><?php echo $name?>さんがログイン中。</p>
<?php echo $member ?>
<a href="http://localhost/php_project/views/front/php/post.php"><input type="button" value="投稿一覧へ"></a>
<a href="http://localhost/php_project/views/front/php/post_form.php"><input type="button" value="新規投稿"></a>
<a href="http://localhost/php_project/views/front/php/new_mypage.php"><input type="button" value="マイページ"></a>
</body>
</html>