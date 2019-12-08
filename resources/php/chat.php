<div class="chatcontainer">
	<div class="messages">
		<input name="username" type="text" placeholder="Search player..."  />
		<div class="seperator"></div>
		<?php
			require "login-helper.class.php";
			require 'mojang-api.class.php';
			LoginHelper::CreateEntries();
		?>
	</div>

	<div class="chatparent">
		<div class="header">
			<h2 style="padding: 0; margin: 0; margin-left: 3em; text-align: left; font-weight: 400; font-size: 18px; position: relative; top: 50%; transform: translateY(-50%);">
				Server Chat
			</h2>
		</div>
		<div class="chatholder">
			<div class="chat">
				<?php
					include("createChat.php");
				?>
			</div>
		</div>
		<div class="prediction"></div>
		<input class="textinput" type="text" id="msg" placeholder="Write a message..." maxlength="255">
		<div class="sendbtn material-icons">send</div>
	</div>

	<div class="playerlist">
		<h3>Online Players</h3>
		<div class="seperator"></div>

		<style>
			.demo-list-action {
				width: 300px;
			}
		</style>

		<div class="demo-list-action mdl-list" style="margin: 0 auto; min-height: 0; width: 80%;">
			<?php
				$arr = MojangAPI::ping("mcvanilla.me");
				$heads = array();
				if (!isset($arr["players"]))
					$arr["players"]["sample"] = array();
				foreach($arr["players"]["sample"] as $val) {
					$username = $val["name"];
					$uuid = $val["id"];
					if (!isset($heads[$val["name"]]))
						$heads[$val["name"]] = MojangAPI::embedImage(MojangAPI::getPlayerHead($uuid, "../images/playerskins/"));
					echo '
						<div class="mdl-list__item" style="margin: 0 !important; padding: 0 !important; min-height: 0;">
							<span class="mdl-list__item-primary-content">
								<span><img src=' . $heads[$val["name"]] . '>' . $val["name"] . '</span>
							</span>
							<button id="demo-menu-lower-right"
								class="mdl-button mdl-js-button mdl-button--icon">
								<i class="material-icons">more_vert</i>
							</button>

							<ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="demo-menu-lower-right">
								<li class="mdl-menu__item">Some Action</li>
								<li class="mdl-menu__item">Another Action</li>
								<li disabled class="mdl-menu__item">Disabled Action</li>
								<li class="mdl-menu__item">Yet Another Action</li>
							</ul>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>

<script>
	var interval = null;
	var initialW = 0;
	var lastPress = 0;
	var request = "";
	$(document).ready(function() {
		$(".chatcontainer").height($(".chatcontainer").height() - $(".footer").outerHeight(true));
		formatChat();
		$(window).resize(function() { formatChat(); });
		loadNew();
		interval = setInterval(function() { loadNew(); }, 5000);

		$(".chatparent input").on("keyup", function(e) {
			var code = e.keyCode || e.which;
			if(code == 13 && lastPress + 2 < new Date().getTime() / 1000) {
				lastPress = new Date().getTime() / 1000;
				postData(this);
			}
			updateChatGuess();
		});

		$(".sendbtn").click(function() {
			lastPress = new Date().getTime() / 1000;
			postData($(".chatparent input"));
		})

		$(".chatparent input").focus(function() {
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				$(".footer").css("display", "none");
				$("html, body").animate({ scrollTop: $(document).height() }, "slow");
			}
		});

		$(".chatparent input").focusout(function() {
			if(onMobile())
				$(".footer").css("display", "");
		});

		var elem = $(".messages input");

		elem.data('oldVal', elem.val());

		elem.bind("propertychange change click keyup input paste", function(event) {
			if (elem.data('oldVal') != elem.val()) {
				elem.data('oldVal', elem.val());
				$(".messages .entry").each(function() {
					var text = $(this).find("span").html().split("<")[0];
					if (text.toLowerCase().indexOf(elem.val().toLowerCase()) >= 0)
						$(this).css("display", "");
					else
						$(this).css("display", "none");
				})
			}
		});
	});

	function postData(obj) {
		var data = {"str": $(obj).val(), "loginID": $.cookie("mcvanilla_loginid") };
		if (selectedTabUUID !== undefined) {
			data = {"str": "/msg " + $.trim($(".chatparent .header").text()) + " " + $(obj).val(), "loginID": $.cookie("mcvanilla_loginid") };
		}
		$.post("resources/php/postChat.php", data, function() {
			clearInterval(interval);
			interval = setInterval(function() { loadNew(); }, 5000);
			loadNew();
		});
		$(obj).val("");
		if(onMobile()) {
			document.activeElement.blur();
		}
		window.scrollTo(0, 0);
	}

	var selectedTabUUID;

	function loadNew() {
		var highest = 0;
		$(".chatcontainer .chat .entry").each(function() {
			if ($(this).data("msgid") > 0)
				highest = $(this).data("msgid");
		});
		var args = "?lastUID=" + highest;
		if (selectedTabUUID !== undefined)
			args = "?involving=" + selectedTabUUID + "&lastUID=" + highest;

		request = $.get("resources/php/createChat.php" + args, function(data) {
			if (data && data != "") {
				$(".chatcontainer .chat").append(data);
				formatChat();
			}
		});

		$(".messages .entry").each(function() {

			var span = $(this).find("span");
			span.width($(this).width());
			if ($(this).data("user") !== undefined) {
				var name = $(this).data("username");
				var dat = 'involving=' + $(this).data("user");
				$.ajax({
					type: 'GET',
					url: 'resources/php/getChatInformation.php',
					data: dat,
					dataType: 'json',
					cache: false,
					success: function(result) {
						span.html(name + " <div class='minor' style='display: block; float: right;'>" + result["time"] + "</div><br /> <div class='minor'>" + result["content"] + "</div>");
						var content = span.find(".minor").eq(1);
						content.css("width", span.width());
					},
				});
			}
		});
	}

	function formatChat() {
		$(".messages").width("15%");
		var header = $(".chatparent .header");
		$(".sendbtn").css({"width": $(".chatcontainer").height() - $(".chatholder").height(), "border-radius": $(".sendbtn").width()});
		$(".textinput").width($(".chatholder").width() - $(".sendbtn").outerWidth(true) * 2);
		$(".textinput").height($(".chatparent").height() - $(".chatholder").height() - 2 - header.height());
		$(".textinput").offset({top: $(".chatparent").offset().top + $(".chatholder").height() + header.height()});
		$(".messages .seperator").offset({top: $(".chatparent .header").offset().top + $(".chatparent .header").height() - 1});
		initialW = $(".chatcontainer .chatholder").width();
		$(".chatcontainer .chat .entry").each(function() {

			$(".chatcontainer .chat").css("max-width", initialW);
			//if ($(this).data("message") != "" && !onMobile())
			//	$(this).remove();
			var pic = $(this).find("img");
			var text = $(this).find(".message");
			text.css("max-width", initialW / 1.8 - 2);
			pic.offset({top: text.offset().top});

			var rightSide = pic.hasClass("my");

			var carat = $(this).find(".carat");

			carat.offset({top: text.offset().top, left: text.offset().left - 8});
			if (rightSide) {
				pic.css({"margin-right": "1em", "margin-left": "0.5em"});
				text.css("max-width", initialW / 1.8 - 2);
				carat.offset({top: text.offset().top, left: text.offset().left + text.width() + 28});
				pic.offset({top: text.offset().top});
			}
			$(this).css({"height": text.height()});

			$(this).find(".reply").hover(function() {
				carat.css("filter", "brightness(120%)");
			})

			$(this).find(".reply").mouseout(function() {
				carat.css("filter", "");
			})

			var entry = $(this);

			$(this).find(".reply").click(function() {
				$(".chatparent input").focus();
				$(".chatparent input").val("/msg " + entry.data("username") + " ");
				updateChatGuess();
			})
		});
		$(".chatholder").scrollTop($(".chatholder")[0].scrollHeight);

		$(".chatholder").css("height", $(".chatparent").height() - $(".chatparent .textinput").height() - "1em");

		var i = 0;
		$(".messages .entry").each(function() {

			$(this).click(function() {
				if ($(this).data("user") == selectedTabUUID)
					return;
				$(this).parent().find(".entry").each(function() {
					$(this).css("background-color", "");
				})
				selectedTabUUID = $(this).data("user");
				request.abort();

				$(".chatcontainer .chat").empty();
				if (selectedTabUUID !== undefined) {
					request = $.get("resources/php/createChat.php?involving=" + selectedTabUUID, function(data) {
						if (data && data != "") {
							$(".chatcontainer .chat").append(data);
							formatChat();
						}
					});
				} else {
					request = $.get("resources/php/createChat.php", function(data) {
						if (data && data != "") {
							$(".chatcontainer .chat").append(data);
							formatChat();
						}
					});
				}

				clearInterval(interval);
				interval = setInterval(function() { loadNew(); }, 5000);

				$(this).css("background-color", "#076097");
				$(".chatparent .header h2").html($(this).find("span").html().split("<")[0]);
				$(".messages input").val("");
				$(".messages .entry").each(function() {
					$(this).css("display", "");
				});
			});

			i++;

		});
	}

	var commands = [["msg", "target", "message"]];

	function updateChatGuess() {
		var predictContainer = $(".prediction");
		predictContainer.text("");

		var text = $(".chatparent input").val();
		var args = text.split(" ");
		var command = args[0].substring(1);
		if (text.substring(0, 1) == "/") {
			var i;
			for(i = 0; i < commands.length; ++i) {
				var entry = commands[i];
				var cmd = entry[0];
				var arg = "";
				var j;
				for(j = 1; j < entry.length; ++j) {
					if (entry[j] != "")
						arg += "&lt;" + entry[j] + "&gt; ";
				}
				if (cmd.indexOf(command) >= 0) {
					predictContainer.append("/" + cmd + " " + arg + "<br>");
				}
			}
		}
		predictContainer.css({"top": $(".textinput").position().top - predictContainer.outerHeight(true), "left": $(".textinput").position().left});
	}

	function onMobile() {
		return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) );
	}

</script>