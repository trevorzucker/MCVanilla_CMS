<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if (!class_exists("LoginHelper")) {
		require 'login-helper.class.php';
		require 'mojang-api.class.php';
	}

	$conn = LoginHelper::getConnection();
	$loggedInUUID = LoginHelper::getUUIDFromLoginID($_COOKIE["mcvanilla_loginid"]);

	$chatQuery = "SELECT * FROM (SELECT * FROM chat WHERE command IS NULL ORDER BY uid DESC LIMIT 30) tmp ORDER BY tmp.uid ASC";
	if (isset($_GET["lastUID"]))
		$chatQuery = "SELECT * FROM chat WHERE command IS NULL AND uid > " . $_GET['lastUID'] . " ORDER BY uid ASC LIMIT 10";
	if(isset($_GET["involving"])) {
		$chatQuery = "SELECT * FROM (SELECT * FROM chat WHERE command IS NOT NULL AND ((uuid='" . $_GET["involving"] . "' AND targetuuid = '" . $loggedInUUID . "') OR (uuid='" . $loggedInUUID . "' AND targetuuid = '" . $_GET["involving"] . "')) ORDER BY uid DESC LIMIT 30) tmp ORDER BY tmp.uid ASC";
		if (isset($_GET["lastUID"]))
			$chatQuery = "SELECT * FROM chat WHERE command IS NOT NULL AND ((uuid='" . $_GET["involving"] . "' AND targetuuid = '" . $loggedInUUID . "') OR (uuid='" . $loggedInUUID . "' AND targetuuid = '" . $_GET["involving"] . "')) AND uid > " . $_GET['lastUID'] . " ORDER BY uid ASC LIMIT 10";
	}
	
	$res = $conn->query($chatQuery);
	$heads = array();
	while($result = mysqli_fetch_assoc($res)) {
		$command = "";
		$result["datetime"] = strtotime($result["datetime"]);
		$time = date("M j, g:i A", $result["datetime"]);

		$user = $result["uuid"];
		$content = htmlspecialchars($result["content"]);
		$linkRegex = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		if (preg_match($linkRegex, $content, $url)) {
			$content = preg_replace($linkRegex, "<a href=" . $url[0] . " target='_blank'>{$url[0]}</a> ", $content);
		}


		if (!isset($heads[$user])) {
			$heads[$user] = array();
			$heads[$user]["image"] = MojangAPI::embedImage(MojangAPI::getPlayerHead($user, "../images/playerskins/"));
			$heads[$user]["chatcolor"] = MojangAPI::getAverage($heads[$user]["image"]);
		}
		$image = $heads[$user]["image"];
		$color = $heads[$user]["chatcolor"];
		$username = $result["username"];
		$caster = "";

		if (isset($result["command"]) && $result["command"]) {
			$targetUUID = $result["target"];
			if ($result["uuid"] != $loggedInUUID)
				$caster = $username;
			else
				$caster = $targetUUID;
		}

		if ($result["fromweb"] == 1 && !isset($result["command"]))
			$username = $username . " (web)";

		$id = $result["uid"];

		$textcolor = "#fff";
		$col = explode(", ", explode("(", substr($color, 0, -1))[1]);
		if ($col[0] + $col[1] + $col[2] > 550) {
			$textcolor = "#000";
		}

		$my = "theirs";
		if ($result["uuid"] == $loggedInUUID) {
			$my = "my";
		}

		$clickable = "";
		if ($command != "") {
			if ($my == "theirs") {	
				$clickable = "reply";
				$command .= " (Click to reply...)";
			}
			$command .= "<br />";
		}
		$usr = $result["username"];

		echo <<< text
			<div class="entry fadein" style="color: $textcolor;" data-msgid="$id" data-username="$usr" data-message=$caster>
				<img src="$image" class="$my userpic"></img>
				<div class="$my carat" style="border-color: transparent $color transparent transparent;"></div>
				<div class="$my message $clickable" style='background-color: $color;'>
					$command$content
					<div class="namedate">$username $time</div>
				</div>
			</div>
		text;
	}
?>