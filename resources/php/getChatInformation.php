<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if (!class_exists("LoginHelper")) {
		require 'login-helper.class.php';
	}

	$conn = LoginHelper::getConnection();
	$loggedInUUID = LoginHelper::getUUIDFromLoginID($_COOKIE["mcvanilla_loginid"]);

	if(isset($_GET["lastUID"]))
		$chatQuery = "SELECT * FROM chat WHERE (command IS NULL OR (uuid = '" . $loggedInUUID . "' OR targetuuid = '" . $loggedInUUID . "')) AND uid = " . $_GET["lastUID"];
	elseif(isset($_GET["nocmd"]))
		$chatQuery = "SELECT * FROM chat WHERE command IS NULL ORDER BY uid DESC LIMIT 1";
	elseif(isset($_GET["involving"]))
		$chatQuery = "SELECT * FROM chat WHERE (uuid = '" . $_GET["involving"] . "' AND targetuuid = '" . $loggedInUUID . "') OR (uuid = '" . $loggedInUUID . "' AND targetuuid = '" . $_GET["involving"] . "') ORDER BY uid DESC LIMIT 1";
	else
		$chatQuery = "SELECT * FROM chat WHERE (command IS NULL OR (uuid = '" . $loggedInUUID . "' OR targetuuid = '" . $loggedInUUID . "')) ORDER BY uid DESC LIMIT 1";
	$res = $conn->query($chatQuery);
	while($result = mysqli_fetch_assoc($res)) {
		$result["datetime"] = strtotime($result["datetime"]);
		$datetime = date("Y-m-d H:i:s", $result["datetime"]);

		$time = date("g:i A", $result["datetime"]);
		$date = date("m/j/y", $result["datetime"]);

		$result["time"] = $date;

		if ($result["uuid"] == $loggedInUUID)
			$result["content"] = "You: " . $result["content"];

		if((time() - (60 * 60 * 24)) < strtotime($datetime)) {
			$result["time"] = $time;
		} elseif ((time() - (60 * 60 * 48)) < strtotime($datetime)) {
			$result["time"] = "Yesterday";
		}

		echo json_encode($result);
	}
?>