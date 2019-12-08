<div class="intro">
	<div class="bg"></div>
	<!--<img src="resources/images/logo.png" style="display: block; margin: 0 auto; padding-top: 10%;">-->
	<h1 style="display: block; margin-top: 12px; text-align: center; line-height: 32px; font-size: 5rem; font-weight: 400; color: rgb(32, 33, 36); position: relative; top: 30%; transform: translateY(-50%);">MCVanilla</h1>
	<h2 style="display: block; margin-top: 12px; text-align: center; font-size: 1.25rem; font-weight: 300; color: rgb(95, 99, 104); line-height: 1.5rem; position: relative; top: 35%; transform: translateY(-50%);">Classic Minecraft the way it's<br>meant to be played</h2>
	<button style="margin: 0 auto; position: relative; top: 35%; transform: translateY(-50%);" class="mdl-button mdl-js-button mdl-js-ripple-effect shown">Get Started</button>
</div>
<div class="content">
	<h1>Hassle-free Vanilla Minecraft, with no distractions.</h1>
	<div class="info-row">
		<div class="panel">
			<i class="material-icons">language</i>
			<h3>Web Control</h3>
			<p>The core of MCVanilla is synced through our web control panel. This enables the ability to monitor your in-game status without being near your computer.</p>
		</div>
		<div class="panel">
			<i class="material-icons">people</i>
			<h3>Trading</h3>
			<p>With MCVanilla's control panel, you can buy, sell or auction off in-game items. This can reduce the amount of time it takes you to trade items the classic way.</p>

		</div>
		<div class="panel">
			<i class="material-icons">thumb_up</i>
			<h3>Reliable</h3>
			<p>MCVanilla is hosted on our high-performance, high-security servers, that are always being monitored. No need to worry about interruptions, just log in and play!</p>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			if ($.cookie("mcvanilla_loggedin"))
				$(".login").css("display", "none");
		});

		$(".body .intro button").click(function() {
			if (!$.cookie("mcvanilla_loggedin"))
				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $("form button").position().top
					}, 250);
				}, 100);
			else
				setTimeout(function() {
					$(".sidebar").css("left", 0);
					$(".dimbg").css({"z-index": 2, "background-color": "rgba(128, 128, 128, 0.3)"});			
				}, 100);
		});

		$('.content form').on('submit', function(e) {
			e.preventDefault();
			document.activeElement.blur();
			$.ajax({
				type: "POST",
				url: "resources/php/doLogin.php",
				data: $(this).serialize(),
				success: function (data) {
					if ($.cookie("mcvanilla_loggedin") == 1) {
						$(".content form .mdl-textfield__label").each(function() {
							$(this).css("color", "");
						});
						$('.content h3[me="yes"]').each(function() {
							$(this).css("display", "none");
						});
						$(".sidebar .header").load("resources/php/doSidebar.php");
						$('html, body').animate({
							scrollTop: 0
						}, 500);
						setTimeout(function() {
							$(".sidebar").css("left", 0);
							$(".dimbg").css({"z-index": 2, "background-color": "rgba(128, 128, 128, 0.3)"});
							$(".login").css("display", "none");
						}, 1000);
					} else {
						$(".content form .mdl-textfield__label").each(function() {
							$(this).css("color", "#f44336");
						});
						$(".content h3").each(function() {
							$(this).css("display", "block");
						});
					}
				}
			});
		});
	</script>

	<?php

	if (!class_exists("LoginHelper")) {
		require 'login-helper.class.php';
		require 'mojang-api.class.php';
	}

	if (isset($_COOKIE["mcvanilla_loginid"]) && isset($_COOKIE["mcvanilla_loggedin"]))
		return;

	$loggedInUUID = LoginHelper::getUUIDFromLoginID($_COOKIE["mcvanilla_loginid"]);

	if (isset($loggedInUUID) && $loggedInUUID != "NULL") return;

		echo '<div class="login">
				<h2 class="title">Getting Started</h2>
				<h3>To continue, please login to your Mojang account:</h3>
				<form method="post" style="display: block !important;">
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="display: block; margin: 0 auto;">
						<input class="mdl-textfield__input" type="text" id="sample3" name="uname">
						<label class="mdl-textfield__label" for="sample3">Email / Username</label>
					</div>
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"style="display: block; margin: 0 auto;">
						<input class="mdl-textfield__input" type="password" id="sample3" name="pword">
						<label class="mdl-textfield__label" for="sample3">Password</label>
					</div>
					<button class="mdl-button mdl-js-button mdl-js-ripple-effect shown" type="submit" style="display: block; margin: 0 auto;">
					Login
					</button>
				</form>
				<h3 style="padding: 0; margin: 0 auto; text-align: center; color: #f44336; display: none; line-height: 0;" me="yes">Invalid login.</h3>
			</div>';

	?>
</div>