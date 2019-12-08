<?php
	if (!class_exists("LoginHelper")) {
		require 'login-helper.class.php';
		require 'mojang-api.class.php';
	}
	echo LoginHelper::CreateEntries();
?>