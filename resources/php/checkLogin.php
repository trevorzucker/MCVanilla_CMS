<?php
	require 'mojang-api.class.php';
	$validLogin = MojangAPI::authenticate($_POST["uname"], $_POST["pword"]);
	return $validLogin;
?>