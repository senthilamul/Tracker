<?php
$client_user=array('joshua.simon@hpe.com','Rahul.Desai@hpe.com','Mohammed.Khalid@hpe.com','Abraham.Johnson@hpe.com','JohnJoy.Raj@hpe.com','Ajay.KV@hpe.com','amandeep.bhandal@hpe.com','beneesh.m@hpe.com');
session_start();
session_destroy();
unset($_SESSION['username']);
unset($_SESSION['email']);
if(in_array($_SESSION['username'],$client_user))
{
	header("Location:../aruba/login.php");
}else{
	require_once("../sso/sso_config.php");
	header("Location:".LOGOUTURL);
	//header("Location:../index.php");
	exit;
}

?>