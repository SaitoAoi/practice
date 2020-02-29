<?php
//HTML書き出し
$str = <<<EOD
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>PHPの学習</title>
<meta name="description" content="このページはPHPを学習するページです">
<meta name="keywords" content="PHP,学習,タグ,ページ">
</head>
<body>
<p id="p"></p>
<script type="text/javascript" src="../js/login.js"></script>
</body>
</html>






EOD;



echo $str;




?>