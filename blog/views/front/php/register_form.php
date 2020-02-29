<?php
session_start();
header("Content-type: text/html; charset=utf-8");
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];//hiddenに埋め込む
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
?>

<!DOCTYPE html>

<html lang="ja">
  <head>
  <meta charset="utf-8" />
  <title>登録画面</title>
  <link rel="stylesheet" type="text/css" href="../../css/register_form.css" />
  <script type="text/javascript" src="../../js/form.js"></script>
  </head>
  <body>
  <div id="Wrap">
  <form action="http://localhost/php_project/views/front/php/register_check.php" method="POST">
  <div id="title">
  <h1>会員登録</h1>
  </div>
  <div id="Main_wrap">
  <div class="middle_wrap">
  <div class="small_wrap">ユーザーID(メールアドレス)</div>
  <div class="small_wrap">
  <input type="text" id="user_id" class="input" name="user_id" onblur="check();">
  <font color="red" size="2"><a id="come1"></a></font>  
  </div>
  </div>

  <div class="middle_wrap">
  <div class="small_wrap">ニックネーム(8文字以上)</div>
  <div class="small_wrap">
  <input type="text" id="user_name" class="input" name="user_name" onblur="check();">
  <font color="red" size="2"><a id="come2"></a></font>
  </div>
  </div>

  <div class="middle_wrap">
  <div class="small_wrap">パスワード(英数8文字以上)</div>
  <div class="small_wrap">
  <input type="text" id="pass" class="input" name="pass" onblur="check();">
  <font color="red" size="2"><a id="come3"></a></font>
  </div>
  </div>
  </div>
  <input type="hidden" name="token" value="<?=$token?>">
  <div id="btn"><input type="submit" value="登録" id="button" disabled></div>
  </form>
  </div>
  </body>
</html>