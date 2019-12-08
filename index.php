<?php include "resources/php/doLogin.php"; ?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="resources/css/minecraft-webfont.css" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		
		<link href="style.css" type="text/css" rel="stylesheet" />

		<script src="resources/js/jquery-3.3.1.min.js"></script>
		<script src="resources/js/jquery.cookie.js"></script>

		<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue-light_blue.min.css"> 
		<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<link rel="icon" type="image/png" href="resources/images/logo.png">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>MCVanilla.me</title>
	</head>
	<body>
		<header>
			<img src="resources/images/icons/menu_white.png" class="menu" />
			<!--<form action="" method="GET">
				<input name="username" type="text" placeholder="Search player..."  />
				<input type="submit" style="display: none" />
			</form>-->
		</header>

		<div class="dimbg"></div>
		<div class="sidebar">
			<div class="header">
				<?php include "resources/php/doSidebar.php"; ?>
			</div>
			<div class="tab mdl-button mdl-js-button mdl-js-ripple-effect"><i class="material-icons">homez</i><p>Home</p></div>
			<div class="tab mdl-button mdl-js-button mdl-js-ripple-effect"><i class="material-icons">personz</i><p>My Account</p></div>
			<div class="tab mdl-button mdl-js-button mdl-js-ripple-effect" onclick="show_chat()"><i class="material-icons">messagez</i><p>Chat</p></div>
			<div class="tab mdl-button mdl-js-button mdl-js-ripple-effect" style="border-top: 1px solid rgba(0, 0, 0, 0.05);" onclick="logout()"><i class="material-icons">exit_to_appz</i><p>Sign Out</p></div>
		</div>

		<div class="body">
			<!-- load contents here -->
		</div>	

		<div class="footer">
			© 2019 by MCVanilla
		</div>

		<script type="text/javascript">
			var currentPage = "home.php";
			$(document).ready(function() {
				var param = getUrlParameter("page");
				if ($.cookie("mcvanilla_loggedin") && param) {
					$(".body").prepend('<div class="progress"><div class="indeterminate"></div></div>');
					$.get("resources/php/" + param + ".php").done(function() {
						$(".body").load("resources/php/" + param + ".php", function() {
							document.title = "MCVanilla.me | " + param.charAt(0).toUpperCase() + param.slice(1);
							$(".body .progress").remove();
						});
						currentPage = param + ".php";
					}).fail(function() { 
						$(".body").load("resources/php/home.php", function() { $(".body .progress").remove(); } );
					});
				} else {
					var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
					window.history.pushState({path:newurl},'',newurl);
					$(".body").load("resources/php/home.php", function() { $(".body .progress").remove(); } );
				}
			});

			function toggleSidebar() {
				if ($(".sidebar").position().left == 0) {
					$(".sidebar").css("left", "");
					$(".dimbg").css({"z-index": "", "background-color": ""});
				} else {
					$(".sidebar").css("left", 0);
					$(".dimbg").css({"z-index": 2, "background-color": "rgba(128, 128, 128, 0.3)"});
				}
			}

			$("header img").click(function() {
				if ($.cookie("mcvanilla_loggedin"))
					toggleSidebar();
			});

			$("body").click(function(e) {
				if ($(e.target).closest(".sidebar").length <= 0 && $(".sidebar").position().left == 0) {
					toggleSidebar();
				}
			});

			function logout() {
				url = "resources/php/logout.php";
				$( location ).attr("href", url);
			}

			var getUrlParameter = function getUrlParameter(sParam) {
				var sPageURL = window.location.search.substring(1),
					sURLVariables = sPageURL.split('&'),
					sParameterName,
					i;

				for (i = 0; i < sURLVariables.length; i++) {
					sParameterName = sURLVariables[i].split('=');

					if (sParameterName[0] === sParam) {
						return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
					}
				}
			};

			$(".sidebar .tab").click(function(e) {
				var filename = ($(this).text().split("z")[1].toLowerCase()).replace(/\s/g,'');
				if (currentPage !== (filename + ".php")) {
					setTimeout(function() {
						toggleSidebar();
						var hasLoaded = false;
						setTimeout(function() {
							$(".body").children().fadeTo(200, 0, function() {
								if (!hasLoaded) {
									var newurl = "";
									if (filename === "home")
										newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
									else
										newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=' + filename;
									window.history.pushState({path:newurl}, '', newurl);
									$(".body").prepend('<div class="progress"><div class="indeterminate"></div></div>');
									$(".body").load("resources/php/" + filename + ".php", function() {
										currentPage = filename + ".php";
										document.title = "MCVanilla.me | " + parent.text();
										$(this).children().css("opacity", 0);
										$(this).children().fadeTo(200, 1);
										$(".body .progress").remove();
									});
									hasLoaded = true;
								}
							});
						}, 250);
					}, 250);
				} else
				setTimeout(function() {
					toggleSidebar();
				}, 250);
			});

			$(".sidebar .tab").hover(function() {
				$(this).css("background-color", "rgba(0, 0, 0, 0.1)");
			});

			$(".sidebar .tab").mouseleave(function() {
				$(this).css("background-color", "rgba(0, 0, 0, 0)");
			});
		</script>
	</body>
</html>