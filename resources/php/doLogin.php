<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require 'mojang-api.class.php';
	require 'login-helper.class.php';
	$conn = LoginHelper::getConnection();

	if (isset($_POST["uname"]) && isset($_POST["pword"])) {
		$validLogin = MojangAPI::authenticate($_POST["uname"], $_POST["pword"]);
		if ($validLogin == TRUE) {
			$username = mysqli_real_escape_string($conn, $validLogin["name"]);
			$checkforid = "SELECT weblogin_id FROM playerdata WHERE username = '$username'";
			$result = $conn->query($checkforid);
			$row = mysqli_fetch_assoc($result);
			if ($result->num_rows == 0 || $row["weblogin_id"] == NULL) {
				$id = base64_encode(openssl_random_pseudo_bytes(16));
				$checkforuser = "SELECT username FROM playerdata WHERE username = '$username'";
				$usercheckresult = $conn->query($checkforid);
				if ($usercheckresult->num_rows > 0) {
					$update = "UPDATE playerdata SET weblogin_id = '$id' WHERE username = '$username'";
					$conn->query($update);
				} else {
					$uuid = MojangAPI::getUuid($username);
					$insert = "INSERT INTO playerdata (uuid, username, weblogin_id) VALUES ('$uuid', '$username', '$id')";
					$conn->query($insert);
				}
			}
			else {
				$id = $row["weblogin_id"];
			}
			setcookie("mcvanilla_loggedin", true, 0, "/");
			setcookie("mcvanilla_loginid", $id, 0, "/");
			header("Location: portal/index.php");
			die();
		}
	}

	if (isset($_COOKIE["mcvanilla_loggedin"]) && isset($_COOKIE["mcvanilla_loginid"])) {
		if (LoginHelper::getUUIDFromLoginID($_COOKIE["mcvanilla_loginid"]) == NULL) {
			    header('Location: resources/php/logout.php');
		}
	}
?>