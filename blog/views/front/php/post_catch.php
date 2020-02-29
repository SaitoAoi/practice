<?php

session_start();
header("Content-type: text/html; charset=utf-8");
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
//エラーがあったら表示
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

if(empty($_POST)) {//空であるかどうか確認
  header("Location: post_form.php");//URL(絶対パスでも相対パスでも)にページが飛ぶ
  exit();  
}else{
  $title = isset($_POST["title"]) ? $_POST["title"]:NULL;
  $body = isset($_POST["body"]) ? $_POST["body"]:NULL;
  $post_id= md5(uniqid(rand(), true));
    
    if ($title === ""){
	  $errors["title"] = "もう一度登録をやりなおして下さい。";
    }
    if ($body === ""){
	  $errors["body"] = "もう一度登録をやりなおして下さい。";
    }
}

if (count($errors) === 0){//エラーがなかったら
  try{
    //本登録のテーブルに登録
    $sql="INSERT INTO post (user_id,title,body,created_at,post_id,user_name) VALUES (:user_id,:title,:body,now(),:post_id,:user_name)";	
    $statement = $pdo -> prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
    $statement->bindValue(':user_id', $userID, PDO::PARAM_STR);
    $statement->bindValue(':title',$title,PDO::PARAM_STR);
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
投稿完了しました。
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
<br>
<p><a href="http://localhost/php_project/views/front/php/post.php"><input type="button" value="投稿一覧"></a></p>
<p><a href="http://localhost/php_project/views/front/php/new_mypage.php">マイページへ</a></p>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){//配列を展開
  echo "".$value."";
}
?>
<?php endif; ?>
</body>
</html>