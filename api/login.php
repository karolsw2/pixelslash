<?php
	session_start();
	include("../config.php");
	$user = mysqli_real_escape_string($a, $_GET['user']);
	
	$password = mysqli_real_escape_string($a, $_GET['password']);
	$password = md5($password);
	$query = mysqli_query($a, "select * from users where user='$user' AND password='$password'");

	if( mysqli_num_rows($query) > 0 ){
		$token = substr(md5(uniqid(mt_rand(), true)) , 0, 64);
		$_SESSION["user_logged"] = true;
		$_SESSION["user"] = $_POST['user'];
		$_SESSION["token"] = $token;
		mysqli_query($a, "update users set token='$token' WHERE user='$user'");
		
		// foward to logged_user_page.php
		echo '{"logged":true}';
	}else{
		echo '{"logged":false}';
	}

?>
