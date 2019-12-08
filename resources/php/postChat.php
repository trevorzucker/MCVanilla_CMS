<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if (!class_exists("LoginHelper")) {
		require 'login-helper.class.php';
		require 'mojang-api.class.php';
	}

	$conn = LoginHelper::getConnection();

	$str = mysqli_real_escape_string($conn, substr($_POST["str"], 0, 255));
	$strArgs = explode(" ", $str);
	$validation = str_replace(' ', '', $str);
	if ($validation == "" || $str == "")
		return;

	$loginID = $_POST["loginID"];
	$uuid = LoginHelper::getUUIDFromLoginID($loginID);
	$username = LoginHelper::getUsernameFromLoginID($loginID);
	$insert = "INSERT INTO chat (username, uuid, content, fromweb) VALUES ('" . $username . "', '" . $uuid . "', '" . $str . "', '1')";
	if (isCommand($strArgs)) {
		$args = parseCommand($strArgs);
		$targetuuid = MojangAPI::getUuid($args["target"]);
		$insert = "INSERT INTO chat (username, uuid, content, fromweb, target, targetuuid, command) VALUES ('" . $username . "', '" . $uuid . "', '" . $args["data"] . "', '1', '" . $args["target"] . "', '" . $targetuuid . "', '" . $args["cmd"] . "')";
	}
	$conn->query($insert);

	function isCommand($args) {
		if ($args[0][0] == '/') return true;
		return false;
	}

	function parseCommand($args) {
		$cmd = str_replace("/", "", $args[0]);
		if (isset($args[1])) $target = $args[1]; else $target = "";
		$data = "";
		for($i = 2; $i < sizeof($args); $i++) {
			$data = $data . $args[$i] . " ";
		}
		return array("cmd" => $cmd, "target" => $target, "data" => $data);
	}
?>