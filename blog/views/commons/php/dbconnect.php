<?php
	//PDO接続する関数
	
	function dbConnect(){
		global $pdo, $e;
		//開発環境と本番環境分岐
		if($_SERVER['SERVER_NAME'] === "localhost")//localhost
		{
			$host="localhost";
			$dbname="php_project";
			$dns = "mysql:host=" . $host .";dbname=".$dbname . ";charset=utf8";
			$user="root";
			$pass="Mgs9zSEH";
		}
	
		try
		{
			$pdo = new PDO($dns, $user, $pass);
		}
		catch (PDOException $e)
		{
			//var_dump($e->getMessage());
			echo $e->getMessage();
			exit;
		}
 
	}
?>