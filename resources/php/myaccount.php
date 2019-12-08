<div class="inventory">
	<h2 class="title" style="color: #212529; padding: 0; margin: 0; font-style: 32px; line-height: 48px;">Inventory</h2>
	<!--<div class="armorcontainer">
		<div class="inventoryslot"></div>
		<div class="inventoryslot"></div>
		<div class="inventoryslot"></div>
		<div class="inventoryslot"></div>
	</div>-->
	<!--<div class="armordisplay"></div>-->
	<!--<div class="container"></div>
	<div class="hotbar"></div>-->
	<table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp" style="margin: 0 auto;">
		<thead>
			<tr>
				<th class="mdl-data-table__cell--non-numeric">Item</th>
				<th>Quantity</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="mdl-data-table__cell--non-numeric">Wooden Planks</td>
				<td>25</td>
				<!-- Right aligned menu below button -->
			</tr>
			<tr>
				<td class="mdl-data-table__cell--non-numeric">Stone</td>
				<td>50</td>
			</tr>
			<tr>
				<td class="mdl-data-table__cell--non-numeric">Bedrock</td>
				<td>10</td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		reload_js("https://code.getmdl.io/1.3.0/material.min.js");
		$(".inventory table tbody tr").each(function() {
			var html = `
				<td>
					<button id='demo-menu-lower-right' class='mdl-button mdl-js-button mdl-button--icon'>
						<i class='material-icons'>more_vert</i>
					</button>

					<ul class='mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect' for='demo-menu-lower-right'>
						<li class='mdl-menu__item'>Some Action</li>
						<li class='mdl-menu__item'>Another Action</li>
						<li disabled class='mdl-menu__item'>Disabled Action</li>
						<li class='mdl-menu__item'>Yet Another Action</li>
					</ul>
				</td>
			`;
			$(this).append(html);
		});
		/*var height = $(".armorcontainer .inventoryslot").outerHeight(true) * 4;
		$(".armorcontainer .inventoryslot").each(function() {
			$(this).css({"width": $(this).width(), "height": $(this).width()});
		});
		$(".body .inventory .armorcontainer").css({"height": height, "width": "auto"});
		$(".inventory .armordisplay").css("height", height - 3);

		var i;
		for (i = 0; i < 27; i++) {
			var ele = $(".container").append('<div class="inventoryslot" style="width: ' + $(".armorcontainer .inventoryslot").width() + ';"></div>');
			ele.data( "foo", 52 );
		}

		$(".container").css({"width": height / 4 * 9});
		
		i = 0;
		for (i = 0; i < 9; i++) {
			$(".hotbar").append('<div class="inventoryslot" style="width: ' + $(".armorcontainer .inventoryslot").width() + ';"></div>');
		}

		$(".hotbar").css({"width": height / 4 * 9});

		$(".inventory").css("width", $(".inventory .container").outerWidth(true) - 1 + 12);

		$(".inventory .armorcontainer").remove();*/
		function reload_js(src) {
			$('script[src="' + src + '"]').remove();
			$('<script>').attr('src', src).appendTo('head');
		}
	});
</script>