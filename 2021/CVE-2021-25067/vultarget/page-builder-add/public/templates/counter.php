<?php if ( ! defined( 'ABSPATH' ) ) exit;

 // 86400 = 1 day
$allowed = array();
$pluginOpsUserTimeZone = get_option('timezone_string');

if ($pluginOpsUserTimeZone == '') {
	$pluginOpsUserTimeZone = 'UTC';
}
date_default_timezone_set($pluginOpsUserTimeZone);
$todaysDate = date('d-m-Y');

if (! function_exists('plugOps_bot_detected')) {
	function plugOps_bot_detected() {

	  return (
	    isset($_SERVER['HTTP_USER_AGENT'])
	    && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
	  );
	}
}

if ( plugOps_bot_detected() ) {
} else{
	if (!isset($_COOKIE[ "ulpb_count".$current_pageID ]) && !is_user_logged_in()) {

		$current_count = get_post_meta($current_pageID,'ulpb_page_hit_counter',true);
		$Todays_count = get_post_meta($current_pageID,"ulpb_page_hit_counter_$todaysDate",true);
		$new_count = (int)$current_count + 1;
		$Todays_new_count = (int)$Todays_count + 1;	
		update_post_meta( $current_pageID, 'ulpb_page_hit_counter', wp_kses("$new_count", $allowed ) );
		update_post_meta( $current_pageID, "ulpb_page_hit_counter_$todaysDate", wp_kses("$Todays_new_count", $allowed ) );
		
		if ($widgetSubscribeFormWidget !== true) {
			ob_start();

		    ?>
		      <script>
		        /*!
		         * jQuery Cookie Plugin v1.4.1
		         * https://github.com/carhartl/jquery-cookie
		         *
		         * Copyright 2006, 2014 Klaus Hartl
		         * Released under the MIT license
		         */
		        (function (factory) {
		          if (typeof define === 'function' && define.amd) {
		            // AMD (Register as an anonymous module)
		            define(['jquery'], factory);
		          } else if (typeof exports === 'object') {
		            // Node/CommonJS
		            module.exports = factory(require('jquery'));
		          } else {
		            // Browser globals
		            factory(jQuery);
		          }
		        }(function ($) {

		          var pluses = /\+/g;

		          function encode(s) {
		            return config.raw ? s : encodeURIComponent(s);
		          }

		          function decode(s) {
		            return config.raw ? s : decodeURIComponent(s);
		          }

		          function stringifyCookieValue(value) {
		            return encode(config.json ? JSON.stringify(value) : String(value));
		          }

		          function parseCookieValue(s) {
		            if (s.indexOf('"') === 0) {
		              // This is a quoted cookie as according to RFC2068, unescape...
		              s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		            }

		            try {
		              // Replace server-side written pluses with spaces.
		              // If we can't decode the cookie, ignore it, it's unusable.
		              // If we can't parse the cookie, ignore it, it's unusable.
		              s = decodeURIComponent(s.replace(pluses, ' '));
		              return config.json ? JSON.parse(s) : s;
		            } catch(e) {}
		          }

		          function read(s, converter) {
		            var value = config.raw ? s : parseCookieValue(s);
		            return $.isFunction(converter) ? converter(value) : value;
		          }

		          var config = $.cookie = function (key, value, options) {

		            // Write

		            if (arguments.length > 1 && !$.isFunction(value)) {
		              options = $.extend({}, config.defaults, options);

		              if (typeof options.expires === 'number') {
		                var days = options.expires, t = options.expires = new Date();
		                t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
		              }

		              return (document.cookie = [
		                encode(key), '=', stringifyCookieValue(value),
		                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
		                options.path    ? '; path=' + options.path : '',
		                options.domain  ? '; domain=' + options.domain : '',
		                options.secure  ? '; secure' : ''
		              ].join(''));
		            }

		            // Read

		            var result = key ? undefined : {},
		              // To prevent the for loop in the first place assign an empty array
		              // in case there are no cookies at all. Also prevents odd result when
		              // calling $.cookie().
		              cookies = document.cookie ? document.cookie.split('; ') : [],
		              i = 0,
		              l = cookies.length;

		            for (; i < l; i++) {
		              var parts = cookies[i].split('='),
		                name = decode(parts.shift()),
		                cookie = parts.join('=');

		              if (key === name) {
		                // If second argument (value) is a function it's a converter...
		                result = read(cookie, value);
		                break;
		              }

		              // Prevent storing a cookie that we couldn't decode.
		              if (!key && (cookie = read(cookie)) !== undefined) {
		                result[name] = cookie;
		              }
		            }

		            return result;
		          };

		          config.defaults = {};

		          $.removeCookie = function (key, options) {
		            // Must not alter options, thus extending a fresh object...
		            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
		            return !$.cookie(key);
		          };

		        }));
		      </script>
		    <?php

		    $cookieScriptCounter = ob_get_contents();
		    ob_end_clean();

		    array_push($POPBallWidgetsScriptsArray, $cookieScriptCounter);
		    $widgetSubscribeFormWidget = true;
		}

		ob_start();

		?>
			<script type="text/javascript">
				( function( $ ) {
					if ($.cookie) {
						$.cookie("ulpb_count<?php echo $current_pageID; ?>", 'yes', {path: '/', expires : 30 });
					}
				})(jQuery);
			</script>
		<?php

		$countVisitCookieScript = ob_get_contents();
		ob_end_clean();

	    array_push($POPBallWidgetsScriptsArray, $countVisitCookieScript);

	}

	if (!is_user_logged_in()) {
		$current_view_count = get_post_meta($current_pageID,'ulpb_page_views_counter',true);
		$new_view_count = (int)$current_view_count + 1;
		update_post_meta( $current_pageID, 'ulpb_page_views_counter', wp_kses("$new_view_count", $allowed ) );
		$Todays_count = get_post_meta($current_pageID,"ulpb_page_views_counter_$todaysDate",true);
		$Todays_new_count = (int)$Todays_count + 1;
		update_post_meta( $current_pageID, "ulpb_page_views_counter_$todaysDate", wp_kses("$Todays_new_count", $allowed ) );
	}
}

	

?>