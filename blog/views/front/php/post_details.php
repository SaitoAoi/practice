<?php
session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];//ユーザーID
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"];//ログイン中のユーザー名
$post_id=$_POST["post_id"];//記事のID
$sql="SELECT title,body,created_at,user_name FROM post WHERE post_id = :post_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":post_id",$post_id);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$member="";
$user_name=$item["user_name"];//記事の投稿者
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
<form
action='http://localhost/php_project/views/front/php/comment_form.php'
method='POST'>
<input type='hidden' name='post_id' value='$post_id'>
<input type='submit' value='コメントする'>
</form>
</div>";

//記事へのコメントを取得
$sql = "SELECT body,created_at,user_name FROM comment WHERE post_id = :post_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":post_id",$post_id);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$comment="";
foreach ($items as $item) {
$user_name=$item["user_name"];
$body =$item["body"];
$created_at=$item["created_at"];

$comment .= 
"<div id='wrap'>
<div id='name'>
<div id='n1'>ユーザー:</div>
<div id='n2'>$user_name</div>
</div>
<div id='body'>
<div id='n3'>コメント:</div>
<div id='n4'>$body</div>
</div>
<div id='date'>
<div id='n6'>作成日時:</div>
<div id='n7'>$created_at</div>
</div>
</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>投稿詳細</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../css/post.css" />
</head>
<body>
<h1>投稿詳細</h1>
<p><?php echo $name?>さんがログイン中。</p>
<?php echo $member ?>
<?php echo $comment?>
<a href="http://localhost/php_project/views/front/php/post.php"><input type="button" value="投稿一覧へ"></a>
<a href="http://localhost/php_project/views/front/php/post_form.php"><input type="button" value="新規投稿"></a>
<a href="http://localhost/php_project/views/front/php/new_mypage.php"><input type="button" value="マイページ"></a>
</body>
</html>