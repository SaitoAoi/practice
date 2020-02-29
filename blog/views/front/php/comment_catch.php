<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];
$post_id=$_POST["post_id"];
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);//セレクトで取ってきたカラムを連想配列に入れる
$name = $item["user_name"];

if(empty($_POST)) {
	header("Location: post_form.php");//URL(絶対パスでも相対パスでも)にページが飛ぶ
  exit();
}else{
	 //POSTされたデータを変数に入れる
  $body = isset($_POST["body"]) ? $_POST["body"]:NULL;
  if ($body === ""){
		$errors["body"] = "もう一度登録をやりなおして下さい。";
  }
}

if (count($errors) === 0){//エラーがなかったら
  try{
    //本登録のテーブルに登録
    $sql="INSERT INTO comment (user_id,body,created_at,post_id,user_name) VALUES (:user_id,:body,now(),:post_id,:user_name)";	
    $statement = $pdo -> prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
    $statement->bindValue(':user_id', $userID, PDO::PARAM_STR);
    $statement->bindValue(':body',$body,PDO::PARAM_STR);
    $statement->bindValue(':post_id',$post_id,PDO::PARAM_STR);
    $statement->bindValue(':user_name',$name,PDO::PARAM_STR);
    $result= $statement->execute();
    $pdo = null;
  }catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
  }  
}

$body = <<< EOM
コメントしました。
EOM;
?>

<!DOCTYPE html>
<html>
<head>
<title>投稿完了</title>
<meta charset="utf-8">
</head>
<body>
<p><?php echo $name?>さんがログイン中。</p>
<?php if (count($errors) === 0): ?>
<?=$body?>
<p>
<form action="http://localhost/php_project/views/front/php/post.php" method="POST">
<input type="hidden" name="post_id" value="<?=$post_id?>">
<input type="submit" value="投稿一覧"></a>
</form>
</p>
<br>
<a href="http://localhost/php_project/views/front/php/new_mypage.php">マイページ</a>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){//配列を展開
	echo "<p>".$value."</p>";
}
?>
<?php endif; ?>
</body>