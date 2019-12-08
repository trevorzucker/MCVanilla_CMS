<?php
	if (!class_exists("MojangAPI")) {	
		require 'mojang-api.class.php';
		require 'login-helper.class.php';
	}
	if (isset($_COOKIE["mcvanilla_loggedin"])) {
		$loggedin = $_COOKIE["mcvanilla_loggedin"];
		$loginID = $_COOKIE["mcvanilla_loginid"];
		if ($loggedin) {
			$uuid = LoginHelper::getUUIDFromLoginID($loginID);
			$img = '<img src="' . MojangAPI::embedImage(MojangAPI::getPlayerHead($uuid)) . '" />';
			echo $img;
			echo '<p>' . MojangAPI::getUsername($uuid) . '</p>';
		}
	}
	else {
		echo "";
	}
?>