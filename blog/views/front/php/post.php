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
$userID = $_SESSION["user_id"];
$sql = "SELECT user_name FROM member WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
$stmt->bindParam(":user_id",$userID);
$stmt->execute();
$item = $stmt->fetch(PDO::FETCH_ASSOC);//セレクトで取ってきたカラムを連想配列に入れる
$name = $item["user_name"];
$sql="SELECT member.user_name AS user_name, post.title AS title, post.body AS body, post.created_at AS created_at, post.post_id AS post_id FROM member INNER JOIN post ON member.user_id = post.user_id";
$stmt = $pdo->prepare($sql);//pdoというクラスのprepareというメソッドに引数を渡すイメージ
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);//セレクトで取ってきたカラムを連想配列に入れる

$member=[];
$text="";

foreach ($items as $item) {
  $user_name=$item["user_name"];
  $title = $item["title"]; 
  $body =$item["body"];
  $created_at=$item["created_at"];
  $post_id=$item["post_id"];

  $text="
  <div id='wrap'>
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
  <div id='n8'>
  <form action='http://localhost/php_project/views/front/php/post_details.php' method='POST'>
  <input type='hidden' name='post_id' value='$post_id'> 
  <input type='submit' value='投稿詳細'>
  </form>
  </div>
  </div>
  </div>";

  array_push($member,$text);
}

//ページャー機能
define('MAX','10');//表示する件数
$post_num = count($member); // トータルデータ件数 
$max_page = ceil($post_num / MAX); // トータルページ数※ceilは小数点を切り捨てる関数
  if(!isset($_GET['page_id'])){ // $_GET['page_id'] はURLに渡された現在のページ数
    $now = 1; // 設定されてない場合は1ページ目にする
  }else{
    $now = $_GET['page_id'];
  }

$start_num = ($now - 1) * MAX; // 配列の何番目から取得すればよいか 
$post_data = array_slice($member, $start_num, MAX, true);// array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数

?>

<!DOCTYPE html>
<html>
<head>
<title>投稿一覧</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../css/post.css" />
</head>
<body>
<div id="main_wrap">
<p><?php echo $name?>さんがログイン中。</p>
<h1 id='title'>投稿一覧</h1>
<br>
<p><form action="post_search.php" method="post">
　記事を絞り込み検索
<br>
　<input type="text" name="serach_word"> 
<input type="submit" value="検索">
</form>
</p>
<p><?php 
foreach($post_data as $val){ // データ表示
    echo $val;
}
 
for($i = 1; $i <= $max_page; $i++){ // 最大ページ数分リンクを作成
    if ($i == $now) { // 現在表示中のページ数の場合はリンクを貼らない
        echo $now. ''; 
    } else {
        echo '<a href=\'http://localhost/php_project/views/front/php/post.php?page_id='. $i. '\')>'. $i. '</a>'. '　';
    }
}
?></p>
</div>
<br>
<a href="http://localhost/php_project/views/front/php/post_form.php"><input type="button" value="新規投稿"></a>
<a href="http://localhost/php_project/views/front/php/new_mypage.php"><input type="button" value="マイページ"></a>


</body>
</html>