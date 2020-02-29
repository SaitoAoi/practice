<?php
session_start();//セッションスタート
header("Content-type: text/html; charset=utf-8");
if ($_POST['token'] != $_SESSION['token']){//受けとったトークンと、サーバー側のトークンを比べる
  echo "不正アクセスの可能性あり";
  exit();//終了
}
header('X-FRAME-OPTIONS: SAMEORIGIN');
ini_set( 'display_errors', 1 );
require_once( "../../commons/php/dbconnect.php" );
$conn = dbConnect();
$errors = array();

if(empty($_POST)) {//空であるかどうか確認
  header("Location: register_form.php");//URL(絶対パスでも相対パスでも)にページが飛ぶ
  exit();
}else{
  //POSTされたデータを変数に入れる
  $user_id = isset($_POST["user_id"]) ? $_POST["user_id"]:NULL;
  $pass = isset($_POST["pass"]) ? $_POST["pass"]:NULL;
  $user_name = isset($_POST["user_name"]) ? $_POST["user_name"]:NULL; 
  $method = 'aes-256-cbc';
  $password="aiueo";

  // 方式に応じたIV(初期化ベクトル)に必要な長さを取得
  $ivLength = openssl_cipher_iv_length($method);
  // IV を自動生成 
  $iv = openssl_random_pseudo_bytes($ivLength);
  // OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
  $options = OPENSSL_RAW_DATA;
  // 暗号化 第4引数に0を渡した場合、戻り値はbase64でエンコードされた暗号文になる。
  $encrypted = openssl_encrypt($user_id, $method, $password, $options, $iv);
  //DB保存用にIVキーと暗号化したデータをBace64でエンコードする
  $base64_iv = base64_encode($iv);
  $base64_encrypted = base64_encode($encrypted);
  //復元させるためにデータベースに保存する項目はIVキーと暗号化されたデータ
  //パスワードをハッシュ化
  $hash =  password_hash($pass, PASSWORD_DEFAULT);

  //文字数を取得
  $pass_mblen=mb_strlen($pass);
  $user_name_mblen=mb_strlen($user_name);	
  //データベースから仮登録情報を持ってくる
  $sql = "SELECT user_id, user_name, pass,iv FROM pre_member";
  $stmt = $pdo->query($sql);
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //メール入力判定
  if ($user_id === ""){
    $errors["user_id"] = "メールが入力されていません。";
  }else{
	  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user_id)){
	  $errors['user_id_check'] = "メールアドレスの形式が正しくありません。";
	}

	  foreach ($items as $item) {
	    $id = $item["user_id"];
	    $user_iv=$item["iv"];

	    //復元したいデータとそのIVキーをデータベースから引き出してBase64でデコードする
	    $iv = base64_decode($user_iv);
	    $encrypted = base64_decode($id);
	    // 復号化
	    $decrypted = openssl_decrypt($encrypted, $method, $password, $options, $iv);
	    //メールアドレスを複合化
	    if($user_id===$decrypted){
		    $errors["user_id_check"]="すでにこのメールアドレスは登録されています。";
	    }
	  } 
  }
    
    
  if($pass === ""){
    $errors["pass"] = "パスワードが入力されていません。";
  }else{
    if (!preg_match('/[a-zA-Z0-9]+$/', $pass)){
      $errors["pass_check"]="英数字8文字以上で入力してください";
    }else if($pass_mblen< 8){
      $errors["pass_check"]="英数字8文字以上で入力してください";
	}
		
	foreach ($items as $item) {
	  $password = $item['pass'];
	  if (password_verify ($pass, $password) === TRUE){
		$errors["pass_check"]="すでにこのパスワードは登録されています。";
	  }
  }

  }

  if($user_name === ""){
    $errors["user_name"] = "ニックネームが入力されていません。";
  }else{
    if ($user_name_mblen < 8){
      $errors["user_name_check"]="8文字以上で入力してください";
	}
		
  foreach ($items as $item) {
	$username = $item['user_name'];
	if($user_name===$username){
	  $errors["user_name_check"]="すでにこのニックネームは登録されています。";
	  }
    }
  }
}

if (count($errors) === 0){//エラーの配列の数が0だったら
  //URLに含めるトークンを生成し、URLにつなげる。
  //「?urltoken=」とすることでGETメソッドによりトークンを取得できるようになる。
  $urltoken = hash('sha256',uniqid(rand(),1));//トークンを生成
  $url = "localhost/php_project/views/front/php/register_finish.php"."?urltoken=".$urltoken;
	
  //ここでデータベースに登録する
  try{
    $sql="INSERT INTO pre_member (urltoken,user_id,user_name,pass,date,iv) VALUES (:urltoken,:user_id,:user_name,:pass,now(),:iv)";	
	  $statement = $pdo -> prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
	  $statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
	  $statement->bindValue(':user_id', $base64_encrypted, PDO::PARAM_STR);
    $statement->bindValue(':user_name',$user_name,PDO::PARAM_STR);
	  $statement->bindValue(':pass',$hash,PDO::PARAM_STR);
	  $statement->bindValue(':iv',$base64_iv,PDO::PARAM_STR);
    $result= $statement->execute();
	  //データベース接続切断
	  $pdo = null;	
  }catch (PDOException $e){//データベースに接続できなかった時のエラーメッセージ
	  print('Error:'.$e->getMessage());
	  die();
  }
    
  //メールの宛先
  $mailTo = $user_id;
  //Return-Pathに指定するメールアドレス
  $returnMail = 'saitoaoi3110@gmail.com';

  $name = "PHP総合課題";
  $mail = "saitoaoi3110@gmail.com";
  $subject = "仮会員登録完了のお知らせ";
 
$body = <<< EOM
　　24時間以内に下記のURLから本登録を行って下さい。
　　{$url}
EOM;
 
  mb_language('ja');
  mb_internal_encoding('UTF-8');
  //Fromヘッダーを作成
  $header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
	
 if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
   $message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。<br>";	
  } else {
	$errors['mail_error'] = "メールの送信に失敗しました。";
  }	
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>仮登録</title>
<meta charset="utf-8">
</head>
<body>
<h1>仮登録画面</h1>
<?php if (count($errors) === 0): ?>
<p><?=$message?></p>
<p>↓このURLが記載されたメールが届きます。</p>
<a href="<?=$url?>"><?=$url?></a>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){//配列を展開
	echo "<p>".$value."</p>";
}
?>
<input type="button" value="戻る" onClick="history.back()">
<?php endif; ?>
</body>
</html>



