<?php
class LoginHelper
{
	private static $storedUUIDs = null;
	private static $storedUsernames = null;
	public static function getUUIDFromLoginID($loginid) {
		if (!isset($storedUUIDs))
			$storedUUIDs = array();
		if (!isset($storedUUIDs[$loginid])) {
			$conn = static::getConnection();
			$findUUID = "SELECT uuid FROM playerdata WHERE weblogin_id = '$loginid'";
			$result = $conn->query($findUUID);
			$row = mysqli_fetch_assoc($result);
			$storedUUIDs[$loginid] = $row["uuid"];
			return $row["uuid"];
		} else {
			return $storedUUIDs[$loginid];
		}
	}

	public static function getUsernameFromUUID($uuid) {
		$conn = static::getConnection();
		$findUsername = "SELECT username FROM playerdata WHERE uuid = '$uuid'";
		$result = $conn->query($findUsername);
		$row = mysqli_fetch_assoc($result);
		return $row["username"];
	}

	public static function getUsernameFromLoginID($loginid) {
		if (!isset($storedUsernames))
			$storedUUIDs = array();
		if (!isset($storedUsernames[$loginid])) {
			$conn = new mysqli();
			$findUUID = "SELECT username FROM playerdata WHERE weblogin_id = '$loginid'";
			$result = $conn->query($findUUID);
			$row = mysqli_fetch_assoc($result);
			$storedUsernames[$loginid] = $row["username"];
			return $row["username"];
		} else {
			return $storedUUIDs[$loginid];
		}
	}

	public static function getConnection() {
		return new mysqli();
	}

	public static function CreateEntries() {

		echo '<div class="entry" style="background-color: #076097;">
				<img src="resources/images/logo-white.png">
				<span>
					Server Chat
				</span>
			</div>';

		$conn = static::getConnection();
		$loggedInUUID = static::getUUIDFromLoginID($_COOKIE["mcvanilla_loginid"]);

		$chatQuery = "SELECT * FROM chat WHERE (command IS NULL OR (uuid = '" . $loggedInUUID . "' OR targetuuid = '" . $loggedInUUID . "'))";

		$createdUsers = array();

		$res = $conn->query($chatQuery);
		$heads = array();
		while($result = mysqli_fetch_assoc($res)) {

			$fromUser = $result["uuid"];
			if ($fromUser == $loggedInUUID)
				$fromUser = $result["targetuuid"];

			if($createdUsers[$fromUser] || $fromUser == $loggedInUUID || static::getUsernameFromUUID($fromUser) == "")
				continue;

			if (!isset($heads[$fromUser])) {
				$heads[$fromUser] = MojangAPI::embedImage(MojangAPI::getPlayerHead($fromUser, "../images/playerskins/"));
			}
			$image = $heads[$fromUser];

			$result["datetime"] = strtotime($result["datetime"]);
			$datetime = date("Y-m-d H:i:s", $result["datetime"]);

			$time = date("g:i A", $result["datetime"]);
			$date = date("m/j/y", $result["datetime"]);

			$result["time"] = "Yesterday";

			if((time() - (60 * 60 * 24)) < strtotime($datetime)) {
				$result["time"] = $time;
			} elseif ((time() - (60 * 60 * 48)) < strtotime($datetime)) {
				$result["time"] = $date;
			}

			$fromUserReadable = static::getUsernameFromUUID($fromUser);

			echo '<div class="entry" data-user="$fromUser" data-username="$fromUserReadable">
					<img src="$image">
					<span>
						$fromUserReadable
					</span>
				</div>';
			$createdUsers[$fromUser] = true;
		}
	}
}
?>