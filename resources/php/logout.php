<?php
	setcookie("mcvanilla_loggedin", "", time()-3600, "/");
	setcookie("mcvanilla_loginid", "", time()-3600, "/");
	//$url = $_SERVER["HTTP_REFERER"];
	//$url = preg_replace('/\?.*/', '', $url);
	header('Location: ../../');
?>