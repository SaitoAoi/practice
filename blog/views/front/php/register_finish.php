<?php

session_start();
header("Content-type: text/html; charset=utf-8");
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
//エラーがあったら表示
ini_set( 'display_errors', 1 );

//データベース接続
//DB接続
require_once( "../../commons/php/dbconnect.php" );
$conn = dbConnect();//呼び出しは1回でOK
//エラーメッセージの初期化
$errors = array();

if(empty($_GET)) {//GETに値が入っているかどうか
  header("Location: registration_check.php");
  exit();
}else{
  //URLに?urltokenという風に付けた為GETメソッドで受け取れる。GETデータを変数に入れる
  $urltoken = isset($_GET["urltoken"]) ? $_GET["urltoken"] : NULL;//$_GET["urltoken"]が存在して、NULL以外の値を取ったらTRUE
	
  if ($urltoken === ""){
	$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
  }else{
    //flagが0の未登録者・仮登録日から24時間以内のuser_idを取り出す
    $sql_1 ="SELECT user_id,urltoken,user_name,pass,iv FROM pre_member WHERE flag IN (0) AND date > now() - interval 24 hour";
    $stmt = $pdo->query($sql_1);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);//セレクトで取ってきたカラムを連想配列に入れる

    foreach ($items as $item) {
      $user_urltoken = $item["urltoken"];
      $user_id = $item["user_id"];//登録者のメールアドレス
      $user_name=$item["user_name"];
      $pass=$item["pass"];
      $user_iv=$item["iv"];

      if($user_urltoken === $urltoken){//24時間以内に仮登録され、本登録されていないトークンの場合
        $errors = array();    // 暗号化方式
        $method = 'aes-256-cbc';    //パスワード
        $password="aiueo";
        // 方式に応じたIV(初期化ベクトル)に必要な長さを取得
        $ivLength = openssl_cipher_iv_length($method);
        // IV を自動生成
        $iv = openssl_random_pseudo_bytes($ivLength);
        // OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
        $options = OPENSSL_RAW_DATA;
        // 暗号化 第4引数に0を渡した場合、戻り値はbase64でエンコードされた暗号文になる。
        //復元したいデータとそのIVキーをデータベースから引き出してBase64でデコードする
        $iv = base64_decode($user_iv);
        $encrypted = base64_decode($user_id);   // 復号化
        $decrypted = openssl_decrypt($encrypted, $method, $password, $options, $iv);

        //DB保存用にIVキーと暗号化したデータをBace64でエンコードする
        $base64_iv = base64_encode($iv);
        $base64_encrypted = base64_encode($decrypted);
        break;

	    }else{//有効期限切れ・間違ったURL
	    $errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
      }    
    }//foreach終わり
  }
}

if (count($errors) === 0){//エラーがなかったら
  try{  
    //本登録のテーブルに登録
    $sql_4="INSERT INTO member (urltoken,user_id,user_name,pass,date,iv) VALUES (:urltoken,:user_id,:user_name,:pass,now(),:iv)";	
    $statement = $pdo -> prepare($sql_4);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
    $statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $statement->bindValue(':user_name',$user_name,PDO::PARAM_STR);
    $statement->bindValue(':pass',$pass,PDO::PARAM_STR);
    $statement->bindValue(':iv',$user_iv,PDO::PARAM_STR);
    $result= $statement->execute();

    //flag=1にする。
    $new_flag = 1;
    $sql = "UPDATE pre_member set flag=:flag WHERE urltoken = :urltoken";
    $stmt = $pdo -> prepare($sql);
    $stmt->bindParam(':flag', $new_flag,PDO::PARAM_STR);
    $stmt->bindParam(':urltoken', $urltoken, PDO::PARAM_STR);
    $res = $stmt->execute();
   
    //データベース接続切断
    $pdo = null;

  }catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
  }  
}

//メールの宛先
$mailTo = $decrypted;
//Return-Pathに指定するメールアドレス
$returnMail = 'saitoaoi3110@gmail.com'; 
$name = "PHP総合課題";
$mail = "saitoaoi3110@gmail.com";
$subject = "本登録完了のお知らせ"; 
$body = <<< EOM
　　本登録完了しました
EOM;
 
mb_language('ja');
mb_internal_encoding('UTF-8'); 
//Fromヘッダーを作成
$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';

if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {	
  $message = "本登録完了しました";	
} else {
  $errors['mail_error'] = "メールの送信に失敗しました。";
}	

 
?>


<!DOCTYPE html>
<html>
<head>
<title>本登録</title>
<meta charset="utf-8">
</head>
<body>
<h1>本登録</h1>
 

<?php if (count($errors) === 0): ?>
 
<p><?=$message?></p>
<a href="http://localhost/php_project/views/front/php/login.php"><input type="button" value="ログイン"></a>

<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){//配列を展開
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>