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
// 暗号化方式
$method = 'aes-256-cbc';

$password="aiueo";
// OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
$options = OPENSSL_RAW_DATA;
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name,iv,user_id FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $item["user_name"];
$user_iv=$item["iv"];
$mail = $item["user_id"];//登録者のメールアドレス

//メールアドレスの複合化
//復元したいデータとそのIVキーをデータベースから引き出してBase64でデコードする
$iv = base64_decode($user_iv);
$encrypted = base64_decode($mail);// 復号化
$decrypted = openssl_decrypt($encrypted, $method, $password, $options, $iv);

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

<p>ハロー、<?php echo $name?>さん。</p>

<p>[ユーザーID]：<?php echo $decrypted?></p>
<p>[ニックネーム]：<?php echo $name?><a href="http://localhost/php_project/views/front/php/change_name_form.php"><input type="button" value="変更"></a></p>
<p>[パスワード]:＊＊＊＊＊＊＊＊<a href="http://localhost/php_project/views/front/php/change_pass_form.php"><input type="button" value="変更"></a></p>

<a href="http://localhost/php_project/views/front/php/post.php?page_id=1"><input type="button" value="投稿一覧"></a>

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