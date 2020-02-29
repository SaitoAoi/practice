<?php

session_start();
header("Content-type: text/html; charset=utf-8")
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name FROM member";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$items = $stmt->fetchALL(PDO::FETCH_ASSOC);
$new_name=$_POST["new_name"]? $_POST["new_name"]:NULL;
$new_name_mblen=mb_strlen($new_name);

foreach ($items as $item) {
  $name = $item["user_name"];
  if($new_name === ""){
    $errors["name"] = "新しいニックネームが入力されていません。";
  }else{
    if($new_name_mblen< 8){
      $errors["name"]="8文字以上で入力してください";
    }
    if ($name === $new_name){
      $errors["name"]="すでにこのニックネームは登録されています。";
      break;
    }else{
      $errors = array();
    }
  }
}


if (count($errors) === 0){//エラーがなかったら   
  try{
    $sql = "UPDATE member SET user_name = :user_name WHERE user_id = :user_id";//パスワードを更新
    $stmt = $pdo -> prepare($sql);
    $stmt->bindParam(':user_name', $new_name,PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_STR);
    $res = $stmt->execute();
    $pdo = null;
  }catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die(); 
  }  
}

$body = <<< EOM
    　　ニックネームを変更しました。
EOM;
?>

<!DOCTYPE html>
<html>
<head>
<title>ニックネーム変更完了</title>
<meta charset="utf-8">
</head>
<body>
<h1>ニックネーム変更完了</h1>
<?php if (count($errors) === 0): ?>
<?=$body?>
<a href="http://localhost/php_project/views/front/php/new_mypage.php"><input type="button" value="マイページへ"></a>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){
  echo "<p>".$value."</p>";
}
?>
<?php endif; ?>
 
</body>
</html>