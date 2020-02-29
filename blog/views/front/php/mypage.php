<?php

session_start();
header("Content-type: text/html; charset=utf-8");
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );
$conn = dbConnect();
$errors = array();

if(empty($_POST)) {//空であるかどうか確認
  
  header("Location: register_form.php");//URL(絶対パスでも相対パスでも)にページが飛ぶ
  exit();
  }else{//POSTされたデータを変数に入れる
  $user_id = isset($_POST["user_id"]) ? $_POST["user_id"]:NULL;
  $pass = isset($_POST["pass"]) ? $_POST["pass"]:NULL; 
  $user_name = isset($_POST["user_name"]) ? $_POST["user_name"]:NULL;

  $method = 'aes-256-cbc';// 暗号化方式
  $password="aiueo";
  $options = OPENSSL_RAW_DATA;// OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可

  //memberに入っているデータと一致するか
  $sql ="SELECT user_id,iv FROM member";
  $stmt = $pdo->query($sql);
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($items as $item) {
    $user_iv=$item["iv"];   
    $mail = $item["user_id"];//登録者のメールアドレス

    //メールアドレスの複合化
    $iv = base64_decode($user_iv);
    $encrypted = base64_decode($mail);// 復号化
    $decrypted = openssl_decrypt($encrypted, $method, $password, $options, $iv);

      $sql = "SELECT user_name, pass FROM member WHERE user_id = :user_id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(":user_id",$mail);
      $stmt->execute();
      $item = $stmt->fetch(PDO::FETCH_ASSOC);
      $name = $item["user_name"];
      $pass_word = $item["pass"];
     

      if ($user_id === ""){
        $errors["user_id"] = "メールが入力されていません。";
      }else{
        if(!$user_id === $decrypted){
          $errors['user_id_check'] = "このメールアドレスは登録されていません";
        }else{
          if(!$user_name === $name){
            $errors['user_name_check'] = "ニックネームが正しくありません";
          }else{
            //パスワードが一致するか
            if(!password_verify ($pass, $pass_word) === TRUE){
              $errors['pass_check'] = "パスワードが正しくありません";
            }else{
              // 初めてのユーザはセッションにユーザIDをセット
              $_SESSION["user_id"] = $mail;
              $userId= $_SESSION["user_id"];
              echo "ログインしました。";
              $errors = array();//エラーメッセージの初期化
              break;
            }
          }
        }
      }
    }
  }


?>



<!DOCTYPE html>
<html>
<head>
<title>マイページ</title>
<meta charset="utf-8">
</head>
<body>
<h1>マイページ</h1>
<?php if (count($errors) === 0): ?>
<h2>プロフィール</h2>
<p>ようこそ、<?php echo $user_name?>さん。</p>
<p>[ユーザーID]：<?php echo $user_id?></p>
<p>[ニックネーム]：<?php echo $user_name?><a href="http://localhost/php_project/views/front/php/change_name_form.php"><input type="button" value="変更"></a></p>
<p>[パスワード]:＊＊＊＊＊＊＊＊<a href="http://localhost/php_project/views/front/php/change_pass_form.php"><input type="button" value="変更"></a></p>
<a href="http://localhost/php_project/views/front/php/post.php"><input type="button" value="投稿一覧"></a>
<a href="http://localhost/php_project/views/front/php/login.php"><input type="button" value="ログアウト"></a>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){//配列を展開
	echo "<p>".$value."</p>";
}
?>
<?php endif; ?>
</body>
</html>