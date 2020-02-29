<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );

$conn = dbConnect();
$errors = array();
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name,pass FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"];
$pass = $item["pass"];
$old_pass=$_POST["old_pass"]? $_POST["old_pass"]:NULL;//変更前のパスワード
$new_pass=$_POST["new_pass"]? $_POST["new_pass"]:NULL;//新しいパスワード
$new_pass_mblen=mb_strlen($new_pass);

if($new_pass=== ""){
  $errors["pass_check"] = "パスワードが入力されていません。";
}else if (!preg_match('/[a-zA-Z0-9]+$/', $new_pass)){
    $errors["pass_check"]="英数字8文字以上で入力してください";
}else if($new_pass_mblen< 8){
    $errors["pass_check"]="英数字8文字以上で入力してください";
}
    
if (!password_verify ($old_pass, $pass) === TRUE){
    $errors["pass_check"]="パスワードが正しくありません";
}else{
    if (password_verify ($new_pass, $pass) === TRUE){
      $errors["pass_check"]="すでにこのパスワードは登録されています。";
    }else{
      $errors = array();
    }
}



if (count($errors) === 0){//エラーがなかったら    
  try{
    $hash_new_pass =  password_hash($new_pass, PASSWORD_DEFAULT);
    $sql = "UPDATE member set pass=:pass WHERE user_id = :user_id";//パスワードを更新
    $stmt = $pdo -> prepare($sql);
    $stmt->bindParam(':pass', $hash_new_pass,PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_STR);
    $res = $stmt->execute();
    $pdo = null;
  }catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
  }  
}

$body = "<p>パスワードを変更しました。</p>"
?>

<!DOCTYPE html>
<html>
<head>
<title>パスワード変更結果</title>
<meta charset="utf-8">
</head>
<body>
<?php if (count($errors) === 0): ?>
<?=$body?>
<br>
<a href="http://localhost/php_project/views/front/php/new_mypage.php">マイページへ</a>
<?php elseif(count($errors) > 0): ?> 
<?php
foreach($errors as $value){
  echo "<p>".$value."</p>";
}
?>
<input type="button" value="戻る" onClick="history.back()">
<?php endif; ?>
</body>
</html>