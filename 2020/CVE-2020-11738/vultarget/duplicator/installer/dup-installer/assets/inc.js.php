<?php defined('ABSPATH') || defined('DUPXABSPATH') || exit; ?>
<script>
	//Unique namespace
	DUPX = new Object();
	DUPX.Util = new Object();
    DUPX.Const = new Object();
	DUPX.GLB_DEBUG =  <?php echo ($_GET['debug'] || $GLOBALS['DEBUG_JS']) ? 'true' : 'false'; ?>;

	DUPX.parseJSON = function(mixData) {
		try {
			var parsed = JSON.parse(mixData);
			return parsed;
		} catch (e) {
			console.log("JSON parse failed - 1");
			console.log(mixData);
		}

		if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
			if (mixData.indexOf('{') < mixData.indexOf('[')) {
				var startBracket = '{';
				var endBracket = '}';
			} else {
				var startBracket = '[';
				var endBracket = ']';
			}
		} else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
			var startBracket = '[';
			var endBracket = ']';
		} else {
			var startBracket = '{';
			var endBracket = '}';
		}
		
		var jsonStartPos = mixData.indexOf(startBracket);
		var jsonLastPos = mixData.lastIndexOf(endBracket);
		if (jsonStartPos > -1 && jsonLastPos > -1) {
			var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
			try {
				var parsed = JSON.parse(expectedJsonStr);
				return parsed;
			} catch (e) {
				console.log("JSON parse failed - 2");
				console.log(mixData);
				throw e;
				return false;
			}
		}
		throw "could not parse the JSON";
		return false;
	}

	DUPX.showProgressBar = function ()
	{
		DUPX.animateProgressBar('progress-bar');
		$('#ajaxerr-area').hide();
		$('#progress-area').show();
	}

	DUPX.hideProgressBar = function ()
	{
		$('#progress-area').hide(100);
		$('#ajaxerr-area').fadeIn(400);
	}

	DUPX.animateProgressBar = function(id) {
		//Create Progress Bar
		var $mainbar   = $("#" + id);
		$mainbar.progressbar({ value: 100 });
		$mainbar.height(25);
		runAnimation($mainbar);

		function runAnimation($pb) {
			$pb.css({ "padding-left": "0%", "padding-right": "90%" });
			$pb.progressbar("option", "value", 100);
			$pb.animate({ paddingLeft: "90%", paddingRight: "0%" }, 3500, "linear", function () { runAnimation($pb); });
		}
	}

	DUPX.toggleAll = function(id) {
		$(id + " *[data-type='toggle']").each(function() {
			$(this).trigger('click');
		});
	}

	DUPX.toggleClick = function()
	{
		var src	   = 0;
		var id     = $(this).attr('data-target');
		var text   = $(this).text().replace(/\+|\-/, "");
		var icon   = $(this).find('i.fa');
		var target = $(id);
		var list   = new Array();

		var style = [
		{ open:   "fa-minus-square",
		  close:  "fa-plus-square"
		},
		{ open:   "fa-caret-down",
		  close:  "fa-caret-right"
		}];

		//Create src
		for (i = 0; i < style.length; i++) {
			if ($(icon).hasClass(style[i].open) || $(icon).hasClass(style[i].close)) {
				src = i;
				break;
			}
		}

		//Build remove list
		for (i = 0; i < style.length; i++) {
			list.push(style[i].open);
			list.push(style[i].close);
		}

		$(icon).removeClass(list.join(" "));
		if (target.is(':hidden') ) {
			(icon.length)
				? $(icon).addClass(style[src].open )
				: $(this).html("- " + text );
			target.show().removeClass('no-display');
		} else {
			(icon.length)
				? $(icon).addClass(style[src].close)
				: $(this).html("+ " + text );
			target.hide();
		}

	}

	DUPX.Util.formatBytes = function (bytes,decimals)
	{
		if(bytes == 0) return '0 Byte';
		var k = 1000;
		var dm = decimals + 1 || 3;
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		var i = Math.floor(Math.log(bytes) / Math.log(k));
		return (bytes / Math.pow(k, i)).toPrecision(dm) + ' ' + sizes[i];
	}

	$(document).ready(function()
    {
		<?php if ($GLOBALS['DUPX_DEBUG']) : ?>
			$("div.dupx-debug input[type=hidden], div.dupx-debug textarea").each(function() {
				var label = '<label>' + $(this).attr('name') + ':</label>';
				$(this).before(label);
				$(this).after('<br/>');
			 });
			 $("div.dupx-debug input[type=hidden]").each(function() {
				$(this).attr('type', 'text');
			 });

			 $("div.dupx-debug").prepend('<div class="dupx-debug-hdr">Debug View</div>');
		<?php endif; ?>

		DUPX.loadQtip = function()
		{
			//Look for tooltip data
			$('i[data-tooltip!=""]').qtip({
				content: {
					attr: 'data-tooltip',
					title: {
						text: function() { return  $(this).attr('data-tooltip-title'); }
					}
				},
				style: {
					classes: 'qtip-light qtip-rounded qtip-shadow',
					width: 500
				},
				 position: {
					my: 'top left',
					at: 'bottom center'
				}
			});
		}

		DUPX.loadQtip();

	});

</script>
<?php
DUPX_U_Html::js();