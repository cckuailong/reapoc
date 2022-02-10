var ai_adb_active = false;
var ai_adb_counter = 0;
var ai_adb_act_cookie_name = "aiADB";
var ai_adb_pgv_cookie_name = "aiADB_PV";
var ai_adb_page_redirection_cookie_name = "aiADB_PR";

var ai_adb_overlay = AI_ADB_OVERLAY_WINDOW;
var ai_adb_message_window = AI_ADB_MESSAGE_WINDOW;
var ai_adb_message_undismissible = AI_FUNCB_GET_UNDISMISSIBLE_MESSAGE;
var ai_adb_message_cookie_lifetime = AI_FUNCT_GET_NO_ACTION_PERIOD;
var ai_adb_action = AI_FUNC_GET_ADB_ACTION;
var ai_adb_page_views = "AI_FUNC_GET_DELAY_ACTION";
var ai_adb_selectors = "AI_ADB_SELECTORS";
var ai_adb_redirection_url = "AI_ADB_REDIRECTION_PAGE";

function ai_adb_process_content () {
  (function ($) {

    var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 1
//    var ai_adb_debugging = false;

    if (ai_adb_debugging) console.log ('');
    if (ai_adb_debugging) console.log ("AI AD BLOCKING CONTENT PROCESSING", ai_adb_active);

    $(".AI_ADB_CONTENT_CSS_BEGIN_CLASS").each (function () {
      var ai_adb_parent = $(this).parent ();

      if (ai_adb_debugging) console.log ("AI AD BLOCKING parent", ai_adb_parent.prop ("tagName"), "id=\""+ ai_adb_parent.attr ("id")+"\"", "class=\""+ ai_adb_parent.attr ("class")+"\"");

      var ai_adb_css = $(this).data ("css");
      if (typeof ai_adb_css == "undefined") ai_adb_css = "display: none !important;";

      var ai_adb_selectors = $(this).data ("selectors");
      if (typeof ai_adb_selectors == "undefined" || ai_adb_selectors == '') ai_adb_selectors = "p";

      if (ai_adb_debugging) console.log ('AI AD BLOCKING CSS, css=\'' + ai_adb_css +'\'', "selectors='" + ai_adb_selectors + "'");

      var ai_adb_action = false;
      $(ai_adb_parent).find ('.AI_ADB_CONTENT_CSS_BEGIN_CLASS, .AI_ADB_CONTENT_CSS_END_CLASS, ' + ai_adb_selectors).each (function () {
        if ($(this).hasClass ("AI_ADB_CONTENT_CSS_BEGIN_CLASS")) {$(this).remove (); ai_adb_action = true;}
        else if ($(this).hasClass ("AI_ADB_CONTENT_CSS_END_CLASS")) {$(this).remove (); ai_adb_action = false;}
        else if (ai_adb_action) {
          var ai_adb_style = $(this).attr ("style");
          if (typeof ai_adb_style == "undefined") ai_adb_style = "";
            else {
              ai_adb_style = ai_adb_style.trim ();
              if (ai_adb_style != '' && ai_adb_style [ai_adb_style.length - 1] != ';') {
                ai_adb_style = ai_adb_style + ';';
              }
            }

          if (ai_adb_debugging) console.log ("AI AD BLOCKING CSS:", $(this).prop ("tagName"), "id=\""+ $(this).attr ("id")+"\"", "class=\""+ $(this).attr ("class")+"\"");

          $(this).attr ("style", ai_adb_style + ' ' + ai_adb_css);
        }
      });
    });

    $(".AI_ADB_CONTENT_DELETE_BEGIN_CLASS").each (function () {
      var ai_adb_parent = $(this).parent ();

      if (ai_adb_debugging) console.log ("AI AD BLOCKING DELETE, parent", ai_adb_parent.prop ("tagName"), "id=\""+ ai_adb_parent.attr ("id")+"\"", "class=\""+ ai_adb_parent.attr ("class")+"\"");

      var ai_adb_selectors = $(this).data ("selectors");
      if (typeof ai_adb_selectors == "undefined" || ai_adb_selectors == '') ai_adb_selectors = "p";

      if (ai_adb_debugging) console.log ("AI AD BLOCKING DELETE, selectors='" + ai_adb_selectors + "'");

      var ai_adb_action = false;
      $(ai_adb_parent).find ('.AI_ADB_CONTENT_DELETE_BEGIN_CLASS, .AI_ADB_CONTENT_DELETE_END_CLASS, ' + ai_adb_selectors).each (function () {
        if ($(this).hasClass ("AI_ADB_CONTENT_DELETE_BEGIN_CLASS")) {$(this).remove (); ai_adb_action = true;}
        else if ($(this).hasClass ("AI_ADB_CONTENT_DELETE_END_CLASS")) {$(this).remove (); ai_adb_action = false;}
        else if (ai_adb_action) {
          if (ai_adb_debugging) console.log ("AI AD BLOCKING DELETE:", $(this).prop ("tagName"), "id=\""+ $(this).attr ("id")+"\"", "class=\""+ $(this).attr ("class")+"\"");

          $(this).remove ();
        }
      });

    });

    $(".AI_ADB_CONTENT_REPLACE_BEGIN_CLASS").each (function () {
      var ai_adb_parent = $(this).parent ();

      if (ai_adb_debugging) console.log ("AI AD BLOCKING REPLACE, parent", ai_adb_parent.prop ("tagName"), "id=\""+ ai_adb_parent.attr ("id")+"\"", "class=\""+ ai_adb_parent.attr ("class")+"\"");

      var ai_adb_text = $(this).data ("text");
      if (typeof ai_adb_text == "undefined") ai_adb_text = "";

      var ai_adb_css = $(this).data ("css");
      if (typeof ai_adb_css == "undefined") ai_adb_css = "";

      var ai_adb_selectors = $(this).data ("selectors");
      if (typeof ai_adb_selectors == "undefined" || ai_adb_selectors == '') ai_adb_selectors = "p";

      if (ai_adb_debugging) console.log ("AI AD BLOCKING REPLACE, text=\'" + ai_adb_text + '\'', 'css=\'' + ai_adb_css +'\'', "selectors='" + ai_adb_selectors + "'");

      var ai_adb_action = false;
      $(ai_adb_parent).find ('.AI_ADB_CONTENT_REPLACE_BEGIN_CLASS, .AI_ADB_CONTENT_REPLACE_END_CLASS, ' + ai_adb_selectors).each (function () {
        if ($(this).hasClass ("AI_ADB_CONTENT_REPLACE_BEGIN_CLASS")) {$(this).remove (); ai_adb_action = true;}
        else if ($(this).hasClass ("AI_ADB_CONTENT_REPLACE_END_CLASS")) {$(this).remove (); ai_adb_action = false;}
        else if (ai_adb_action) {
          if (ai_adb_text.length != 0) {
            var n = Math.round ($(this).text ().length / (ai_adb_text.length + 1));
            $(this).text (Array(n + 1).join(ai_adb_text + ' ').trim ());
          } else $(this).text ('');

          if (ai_adb_css != '') {
            var ai_adb_style = $(this).attr ("style");
            if (typeof ai_adb_style == "undefined") ai_adb_style = "";
              else {
                ai_adb_style = ai_adb_style.trim ();
                if (ai_adb_style != '' && ai_adb_style [ai_adb_style.length - 1] != ';') {
                  ai_adb_style = ai_adb_style + ';';
                }
              }
            if (ai_adb_css != '') {
              ai_adb_css = ' ' + ai_adb_css;
            }
            $(this).attr ("style", ai_adb_style + ai_adb_css);
          }

          if (ai_adb_debugging) console.log ("AI AD BLOCKING REPLACE:", $(this).prop ("tagName"), "id=\""+ $(this).attr ("id")+"\"", "class=\""+ $(this).attr ("class")+"\"");
        }
      });
    });

  }(jQuery));
}

function ai_adb_process_blocks (element) {
  (function ($) {
    var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 2
//    var ai_adb_debugging = false;

    if (typeof element == 'undefined') {
      element = $('body');
      if (ai_adb_debugging) console.log ('');
    }

    var ai_adb_data = $(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME);
    if (typeof ai_adb_data === "string") {
      var ai_adb_active = ai_adb_data == b64d ("bWFzaw==");
    } else {
        var ai_adb_active = null;
      }

    if (ai_adb_debugging) console.log ("AI AD BLOCKING block actions:", ai_adb_active, $(element).prop ("tagName") + '.' + $(element).attr ('class'));

    if (typeof ai_adb_data === "string" && typeof ai_adb_active === "boolean") {

      if (ai_adb_debugging) console.log ("AI AD BLOCKING block actions checking");

      if (ai_adb_active) {

        var code_inserted = false;

        do {
          var code_insertion = false;

          // Don't use data () as the value will be cached - wrong value for tracking
          $(".ai-adb-hide", element).each (function () {
            $(this).css ({"display": "none", "visibility": "hidden"});

            $(this).removeClass ('ai-adb-hide');

            // Disable tracking
            var wrapping_div = $(this).closest ('div[data-ai]');
            if (typeof wrapping_div.attr ("data-ai") != "undefined") {
              var data = JSON.parse (b64d (wrapping_div.attr ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                data [1] = "";

                if (ai_adb_debugging) console.log ("AI AD BLOCKING TRACKING ", b64d (wrapping_div.attr ("data-ai")), ' <= ', JSON.stringify (data));

                wrapping_div.attr ("data-ai", b64e (JSON.stringify (data)));
              }
            }

            ai_disable_processing ($(this));

            if (ai_adb_debugging) {
              var debug_info = $(this).data ("ai-debug");
              console.log ("AI AD BLOCKING HIDE", typeof debug_info != "undefined" ? debug_info : "");
            }
          });

          // after hide to update tracking data on replace
          // Don't use data () as the value will be cached - wrong value for tracking
          $(".ai-adb-show", element).each (function () {
            $(this).css ({"display": "block", "visibility": "visible"});

            $(this).removeClass ('ai-adb-show');

            if (typeof $(this).data ('code') != 'undefined') {
              var adb_code = b64d ($(this).data ('code'));

              if (ai_adb_debugging) console.log ('AI AD BLOCKING SHOW INSERT CODE');
              if (ai_adb_debugging) console.log ('');

              $(this).append (adb_code);

              code_insertion = true;
              code_inserted = true;

              // Process rotations to set versions before tracking data is set
              if (typeof ai_process_elements == 'function') {
                ai_process_elements ();
              }
            }

            var tracking_data = $(this).attr ('data-ai-tracking');
            if (typeof tracking_data != 'undefined') {
              var wrapping_div = $(this).closest ('div[data-ai]');
              if (typeof wrapping_div.attr ("data-ai") != "undefined") {
                if ($(this).hasClass ('ai-no-tracking')) {
                  var data = JSON.parse (b64d (wrapping_div.attr ("data-ai")));
                  if (typeof data !== "undefined" && data.constructor === Array) {
                    data [1] = "";
                    tracking_data = b64e (JSON.stringify (data));
                  }
                }

                if (ai_adb_debugging) console.log ("AI AD BLOCKING TRACKING ", b64d (wrapping_div.attr ("data-ai")), ' <= ', b64d (tracking_data));

                wrapping_div.attr ("data-ai", tracking_data);
              }
            }
            if (ai_adb_debugging) {
              var debug_info = $(this).data ("ai-debug");
              console.log ("AI AD BLOCKING SHOW", typeof debug_info != "undefined" ? debug_info : "");
            }
          });
        } while (code_insertion);

        setTimeout (function() {
          if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
            ai_process_impressions ();
          }
          if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
            ai_install_click_trackers ();
          }
        }, 15);

        setTimeout (ai_adb_process_content, 10);
    } else {
        // Prevent tracking if block was not displayed because of cookie
        $(".ai-adb-hide", element).each (function () {
          if (ai_adb_debugging) console.log ('AI ai-adb-hide', $(this), $(this).outerHeight (), $(this).closest ('.ai-adb-show').length);

          $(this).removeClass ('ai-adb-hide');

          if ($(this).outerHeight () == 0 && $(this).closest ('.ai-adb-show').length == 0) {
            // Top level (not nested) block
            var wrapper = $(this).closest ('div[data-ai]');
            if (typeof wrapper.attr ("data-ai") != "undefined") {
              var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                data [1] = "";

                if (ai_adb_debugging) console.log ("AI AD BLOCKING TRACKING DISABLED: ", b64d (wrapper.attr ("data-ai")), ' <= ', JSON.stringify (data));

                wrapper.attr ("data-ai", b64e (JSON.stringify (data)));

                // Hide block (wrapping div with margin)
                wrapper.addClass ('ai-viewport-0').css ("display", "none");
              }
            }

          }
        });

        $(".ai-adb-show", element).each (function () {
          ai_disable_processing ($(this));

          $(this).removeClass ('ai-adb-show');

          if (ai_adb_debugging) console.log ('AI AD BLOCKING SHOW disable processing', $(this).prop ("tagName") + '.' + $(this).attr ('class'));
        });
      }
    }

    if (ai_adb_debugging) console.log ("AI AD BLOCKING block actions END");
  }(jQuery));
}

ai_adb_detection_type_log = function (n) {
  var type = ai_adb_detection_type (n);
  var ai_adb_events = jQuery('#ai-adb-events');
  if (ai_adb_events.count != 0) {
    var message = ai_adb_events.text ();
    if (message != '') message = message + ', '; else message = message + ', EVENTS: ';
    message = message + n;
    ai_adb_events.text (message);
  }
  return type;
}

ai_adb_detection_type = function (n) {

  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 3
//  var ai_adb_debugging = false;

  if (ai_adb_debugging) {
    switch (n) {
      case 0:
        return "0 debugging";
        break;
      case 1:
        return "1 ads create element";
        break;
      case 2:
        return "2 sponsors window var";
        break;
      case 3:
        return "3 banner element";
        break;
      case 4:
        return "4 custom selectors";
        break;
      case 5:
        return "5 ga";
        break;
      case 6:
        return "6 media.net";
        break;
      case 7:
        return "7 adsense";
        break;
      case 8:
        return "8 doubleclick.net";
        break;
      case 9:
        return "9 fun adblock 3";
        break;
      case 10:
        return "10 fun adblock 4";
        break;
      case 11:
        return "11 banner js";
        break;
      case 12:
        return "12 300x250 js";
        break;
      default:
        return n;
        break;
    }
  } else return '';
}

var ai_adb_detected = function (n) {
  setTimeout (function() {
    ai_adb_detected_actions (n);
  }, 2);
}

var ai_disable_processing = function (element) {
  jQuery(element).find ('.ai-lazy').removeClass ('ai-lazy');                                    // Disable lazy loading
  jQuery(element).find ('.ai-manual').removeClass ('ai-manual');                                // Disable manual loading
  jQuery(element).find ('.ai-rotate').removeClass ('ai-unprocessed').removeAttr ('data-info');  // Disable rotations
  jQuery(element).find ('.ai-list-data').removeClass ('ai-list-data');                          // Disable lists
  jQuery(element).find ('.ai-ip-data').removeClass ('ai-ip-data');                              // Disable IP lists
  jQuery(element).find ('[data-code]').removeAttr ('data-code');                                // Disable insertions
}

var ai_adb_detected_actions = function(n) {

  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 4
//  var ai_adb_debugging = false;

  if (ai_adb_debugging && n == 0) console.log ('');
  if (ai_adb_debugging) console.log ("AI AD BLOCKING DETECTED", ai_adb_detection_type_log (n));

  if (!ai_adb_active) {
    ai_adb_active = true;

    jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME, b64d ("bWFzaw=="));

    (function ($) {

      $(window).ready(function () {
        ai_adb_process_blocks ();

//        if (code_inserted && typeof ai_process_elements == 'function') {
//          setTimeout (ai_process_elements, 20);
//        }
      });

      if (ai_adb_debugging) console.log ("AI AD BLOCKING action check");
//        AiCookies.remove (ai_adb_pgv_cookie_name, {path: "/"});

      // Disable action for bots
      if (typeof MobileDetect !== "undefined") {
        var md = new MobileDetect (window.navigator.userAgent);

        if (ai_adb_debugging) console.log ('AI AD BLOCKING IS BOT:', md.is ('bot'));

        if (md.is ('bot')) {
          ai_adb_action = 0;
        }
      }

      if (ai_adb_page_views != '') {
        if (ai_adb_debugging) console.log ("AI AD BLOCKING page views delay:", ai_adb_page_views);
        if (ai_adb_page_views.includes (',')) {
          var ai_adb_page_view_parts = ai_adb_page_views.split (',');

          var ai_adb_page_view_delay = parseInt (ai_adb_page_view_parts [0]);
          var ai_adb_page_view_repeat = parseInt (ai_adb_page_view_parts [1]);

          if (ai_adb_debugging) console.log ("AI AD BLOCKING page views delay:", ai_adb_page_view_delay, "repeat:", ai_adb_page_view_repeat);
        } else {
            var ai_adb_page_view_delay = parseInt (ai_adb_page_views);
            var ai_adb_page_view_repeat = 0

            if (ai_adb_debugging) console.log ("AI AD BLOCKING page views delay:", ai_adb_page_view_delay);
          }

        var ai_adb_page_view_counter = 1;
        var cookie = AiCookies.get (ai_adb_pgv_cookie_name);
        if (typeof cookie != "undefined") ai_adb_page_view_counter = parseInt (cookie) + 1;
        if (ai_adb_debugging) console.log ("AI AD BLOCKING page views cookie:", cookie, "- page view:", ai_adb_page_view_counter);
        if (ai_adb_page_view_counter <= ai_adb_page_view_delay) {
          if (ai_adb_debugging) console.log ("AI AD BLOCKING", ai_adb_page_view_delay, "page views not reached, no action");
          AiCookies.set (ai_adb_pgv_cookie_name, ai_adb_page_view_counter, {expires: 365, path: "/"});
          window.ai_d1 = ai_adb_page_view_counter;
          window.AI_ADB_STATUS_MESSAGE=1;
          return;
        }
        if (ai_adb_page_view_repeat != 0) {
          AiCookies.set (ai_adb_pgv_cookie_name, ai_adb_page_view_counter, {expires: 365, path: "/"});
          if ((ai_adb_page_view_counter - ai_adb_page_view_delay - 1) % ai_adb_page_view_repeat != 0) {
            if (ai_adb_debugging) console.log ("AI AD BLOCKING every", ai_adb_page_view_repeat, "page views, no action");
            window.ai_d1 = ai_adb_page_view_counter;
            window.AI_ADB_STATUS_MESSAGE=1;
            return;
          }
        }
      }

      if (ai_adb_message_cookie_lifetime != 0 && (ai_adb_action != 1 || !ai_adb_message_undismissible)) {

        var cookie = AiCookies.get (ai_adb_act_cookie_name);
        if (ai_adb_debugging) console.log ("AI AD BLOCKING cookie:", cookie);
        if (typeof cookie != "undefined" && cookie == "AI_CONST_AI_ADB_COOKIE_VALUE") {
          if (ai_adb_debugging) console.log ("AI AD BLOCKING valid cookie detected, no action");
          window.AI_ADB_STATUS_MESSAGE=2;
          return;
        }

        else if (ai_adb_debugging) console.log ("AI AD BLOCKING invalid cookie");
        AiCookies.set (ai_adb_act_cookie_name, "AI_CONST_AI_ADB_COOKIE_VALUE", {expires: ai_adb_message_cookie_lifetime, path: "/"});
      } else
          AiCookies.remove (ai_adb_act_cookie_name, {path: "/"});

      if (ai_adb_debugging) console.log ("AI AD BLOCKING action", ai_adb_action);

      if (ai_adb_action == 0) {
        ai_dummy = 16; // Do not remove - to prevent optimization
        window.AI_ADB_STATUS_MESSAGE=6;
        ai_dummy ++;   // Do not remove - to prevent optimization
      } else {
          window.AI_ADB_STATUS_MESSAGE=3;
          ai_dummy = 13; // Do not remove - to prevent optimization
        }

      switch (ai_adb_action) {
        case 1:
          if (!ai_adb_message_undismissible) {
            ai_adb_overlay.click (function () {
              $(this).remove();
              ai_adb_message_window.remove();
            });
            ai_adb_message_window.click (function () {
              $(this).remove();
              ai_adb_overlay.remove();
            });
            window.onkeydown = function( event ) {
              if (event.keyCode === 27 ) {
                ai_adb_overlay.click ();
                ai_adb_message_window.click ();
              }
            }

            if (ai_adb_debugging) console.log ("AI AD BLOCKING MESSAGE click detection installed");

          } else {
//              AiCookies.remove (ai_adb_act_cookie_name, {path: "/"});

              ai_adb_overlay.find ('[style*="cursor"]').css ("cursor", "no-drop");
              ai_adb_message_window.find ('[style*="cursor"]').css ("cursor", "no-drop");
            }

          if (ai_adb_debugging) console.log ("AI AD BLOCKING MESSAGE");

          var body_children = $(b64d ("Ym9keQ==")).children ();
          body_children.eq (Math.floor (Math.random() * body_children.length)).after (ai_adb_overlay);
          body_children.eq (Math.floor (Math.random() * body_children.length)).after (ai_adb_message_window);

          break;
        case 2:
          if (ai_adb_redirection_url != "") {
            if (ai_adb_debugging) console.log ("AI AD BLOCKING REDIRECTION to", ai_adb_redirection_url);

            var redirect = true;
            if (ai_adb_redirection_url.toLowerCase().substring (0, 4) == "http") {
              if (window.location.href == ai_adb_redirection_url) var redirect = false;
            } else {
                if (window.location.pathname == ai_adb_redirection_url) var redirect = false;
              }

            if (redirect) {
              var cookie = AiCookies.get (ai_adb_page_redirection_cookie_name);
              if (typeof cookie == "undefined") {
                var date = new Date();
                date.setTime (date.getTime() + (10 * 1000));
                AiCookies.set (ai_adb_page_redirection_cookie_name, window.location.href, {expires: date, path: "/"});

                window.location.replace (ai_adb_redirection_url)
              } else {
                  if (ai_adb_debugging) console.log ("AI AD BLOCKING no redirection, cookie:", cookie);

                }
            } else {
                if (ai_adb_debugging) console.log ("AI AD BLOCKING already on page", window.location.href);
                AiCookies.remove (ai_adb_page_redirection_cookie_name, {path: "/"});
              }
          }
          break;
      }

    }(jQuery));
  }
}


var ai_adb_undetected = function (n) {
  setTimeout (function() {
    if (!ai_adb_active) {
      ai_adb_undetected_actions (n);
    }
  }, 200);
}


var ai_adb_undetected_actions = function (n) {
  ai_adb_counter ++;

  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 5
//  var ai_adb_debugging = false;

//  if (ai_adb_debugging && n == 1) console.log ('');
  if (ai_adb_debugging) console.log ("AI AD BLOCKING not detected:", '(' + ai_adb_counter + ')', ai_adb_detection_type (n));

  if (!ai_adb_active && ai_adb_counter == 4) {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING NOT DETECTED");

      jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME, b64d ("Y2xlYXI="));

      ai_dummy = 11; // Do not remove - to prevent optimization
      window.AI_ADB_STATUS_MESSAGE=4; // Check replacement code {}
      ai_dummy = 14; // Do not remove - to prevent optimization

//      // Prevent tracking if block was not displayed because of cookie
//      jQuery(".ai-adb-hide").each (function () {
//        if (ai_adb_debugging) console.log ('AI ai-adb-hide', jQuery(this), jQuery(this).outerHeight (), jQuery(this).closest ('.ai-adb-show').length);

//        if (jQuery(this).outerHeight () == 0 && jQuery(this).closest ('.ai-adb-show').length == 0) {
//          // Top level (not nested) block
//          var wrapper = jQuery(this).closest ('div[data-ai]');
//          if (typeof wrapper.attr ("data-ai") != "undefined") {
//            var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
//            if (typeof data !== "undefined" && data.constructor === Array) {
//              data [1] = "";

//              if (ai_adb_debugging) console.log ("AI AD BLOCKING TRACKING DISABLED: ", b64d (wrapper.attr ("data-ai")), ' <= ', JSON.stringify (data));

//              wrapper.attr ("data-ai", b64e (JSON.stringify (data)));

//              // Hide block (wrapping div with margin)
//              wrapper.addClass ('ai-viewport-0').css ("display", "none");
//            }
//          }

//        }
//      });

//      jQuery(".ai-adb-show").each (function () {
//        ai_disable_processing (jQuery (this));
//      });

        ai_adb_process_blocks ();

//      var redirected_page = false;
//      if (ai_adb_redirection_url.toLowerCase().substring (0, 4) == "http") {
//        if (window.location.href == ai_adb_redirection_url) var redirected_page = true;
//      } else {
//          if (window.location.pathname == ai_adb_redirection_url) var redirected_page = true;
//        }

//      if (redirected_page) {
//        //var cookie = jQuery.cookie (ai_adb_page_redirection_cookie_name);
//        var cookie = AiCookies.get (ai_adb_page_redirection_cookie_name);
//        if (typeof cookie != "undefined" && cookie.toLowerCase().substring (0, 4) == "http") {
//          if (ai_adb_debugging) console.log ("AI AD BLOCKING returning to", cookie);
//          //jQuery.removeCookie (ai_adb_page_redirection_cookie_name, {path: "/"});
//          AiCookies.remove (ai_adb_page_redirection_cookie_name, {path: "/"});
//          window.location.replace (cookie);
//        }
//      }

  }
}

if (AI_DBG_AI_DEBUG_AD_BLOCKING) jQuery (document).ready (function () {ai_adb_detected (0)});

jQuery (document).ready (function ($) {
  $(window).ready (function () {

    var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 6
//    var ai_adb_debugging = false;

//    var ai_debugging_active = typeof ai_adb_fe_dbg !== 'undefined';
    ai_debugging_active = typeof ai_adb_fe_dbg !== 'undefined';

    setTimeout (function() {
      $("#ai-adb-bar").click (function () {
        AiCookies.remove (ai_adb_act_cookie_name, {path: "/"});
        AiCookies.remove (ai_adb_pgv_cookie_name, {path: "/"});
        window.AI_ADB_STATUS_MESSAGE=5;
        ai_dummy = 15; // Do not remove - to prevent optimization
      });
    }, 2);

//    if (jQuery("#banner-advert-container").length) {
//      if ($("#banner-advert-container img").length > 0) {
//        if ($("#banner-advert-container img").outerHeight() === 0) {
//          if (!ai_adb_active || ai_debugging_active) ai_adb_detected (3);
//        } else ai_adb_undetected (3);
//        $("#banner-advert-container img").remove();
//      }
//    }

    if ((!ai_adb_active || ai_debugging_active) && ai_adb_selectors != "") {
      var ai_adb_el_counter = 0;
      var ai_adb_el_zero = 0;
      var ai_adb_selector = ai_adb_selectors.split (",");
      $.each (ai_adb_selector, function (i) {
        ai_adb_selector [i] = ai_adb_selector [i].trim ();

        if (ai_adb_debugging) console.log ("AI AD BLOCKING selector", ai_adb_selector [i]);

        if ($(ai_adb_selector [i]).length != 0) {
          $(ai_adb_selector [i]).each (function (n) {

            var outer_height = $(this).outerHeight ();

            if (ai_adb_debugging) console.log ("AI AD BLOCKING element id=\"" + $(this).attr ("id") + "\" class=\"" + $(this).attr ("class") + "\" heights:", $(this).outerHeight (), $(this).innerHeight (), $(this).height ());

            var ai_attributes = $(this).find ('.ai-attributes');
            if (ai_attributes.length) {
              ai_attributes.each (function (){
                if (ai_adb_debugging) console.log ("AI AD BLOCKING attributes height:", $(this).outerHeight ());
                if (outer_height >= $(this).outerHeight ()) {
                  outer_height -= $(this).outerHeight ();
                }
              });
            }

            if (ai_adb_debugging) console.log ("AI AD BLOCKING effective height:", outer_height);

            ai_adb_el_counter ++;
            if (outer_height === 0) {
              $ (document).ready (function () {if (!ai_adb_active || ai_debugging_active) ai_adb_detected (4)});
              ai_adb_el_zero ++;
              if (!ai_debugging_active) return false;
            }

          });

        }
      });
      if (ai_adb_el_counter != 0 && ai_adb_el_zero == 0) $(document).ready (function () {ai_adb_undetected (4)});
    }

  });
});

function ai_adb_get_script (ai_adb_script, ai_adb_action) {
  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 7
//  var ai_adb_debugging = false;

  if (ai_adb_debugging) console.log ("AI AD BLOCKING loading script", ai_adb_script);

  var script = document.createElement ('script');
  var date = new Date();
  script.src = 'ai-adb-url' + ai_adb_script + '.js?ver=' + date.getTime();

  var head = document.getElementsByTagName ('head')[0],
      done = false;

  // Attach handlers for all browsers

  script.onerror = function () {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING error loading script", ai_adb_script);

    if (ai_adb_action) {
      ai_adb_action ();
    }
    script.onerror = null;
    head.removeChild (script);
  }

  script.onload = script.onreadystatechange = function () {
    if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
      done = true;

      if (ai_adb_debugging) console.log ("AI AD BLOCKING script loaded ", ai_adb_script);

      if (ai_adb_action) {
        ai_adb_action ();
      }

      script.onload = script.onreadystatechange = null;
      head.removeChild (script);
    };
  };

  head.appendChild (script);
};

jQuery (window).on ('load', function () {
  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 8
//  var ai_adb_debugging = false;

  if (ai_adb_debugging) console.log ("AI AD BLOCKING window load");

  function ai_adb_1 () {
    if (!document.getElementById ("AI_CONST_AI_ADB_1_NAME")){
      if (!ai_adb_active || ai_debugging_active) ai_adb_detected (1);
    } else {
        ai_adb_undetected (1);
    }
  }

  function ai_adb_2 () {
    if (typeof window.AI_CONST_AI_ADB_2_NAME == "undefined") {
      if (!ai_adb_active || ai_debugging_active) ai_adb_detected (2);
    } else {
        ai_adb_undetected (2);
      }
  }

  function ai_adb_11 () {
    if (typeof window.ad_banner == "undefined") {
      if (!ai_adb_active || ai_debugging_active) ai_adb_detected (11);
    } else {
        ai_adb_undetected (11);
      }
  }

  function ai_adb_12 () {
    if (typeof window.ad_300x250 == "undefined") {
      if (!ai_adb_active || ai_debugging_active) ai_adb_detected (12);
    } else {
        ai_adb_undetected (12);
      }
  }

  function ai_adb_external_scripts () {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING check external scripts");

    var element = jQuery (b64d ("I2FpLWFkYi1nYQ=="));
    if (element.length) {
      if (!!(element.width () * element.height ())) {
        ai_adb_undetected (5);
      } else {
          if (!ai_adb_active || ai_debugging_active) ai_adb_detected (5);
        }
    }

    var element = jQuery (b64d ("I2FpLWFkYi1tbg=="));
    if (element.length) {
      if (!!(element.width () * element.height ())) {
        ai_adb_undetected (6);
      } else {
          if (!ai_adb_active || ai_debugging_active) ai_adb_detected (6);
        }
    }

    var element = jQuery (b64d ("I2FpLWFkYi1kYmxjbGs="));
    if (element.length) {
      if (!!(element.width () * element.height ())) {
        ai_adb_undetected (8);
      } else {
          if (!ai_adb_active || ai_debugging_active) ai_adb_detected (8);
        }
    }
  }

  setTimeout (function() {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING delayed checks external scripts");

    ai_adb_external_scripts ();

    // Check again, result is delayed
    setTimeout (function() {
      if (!ai_adb_active) {
        setTimeout (function() {
          ai_adb_external_scripts ();
        }, 400);
      }
    }, 5);
  }, 1050);

  setTimeout (function() {
    var ai_debugging_active = typeof ai_adb_fe_dbg !== 'undefined';

    if (ai_adb_debugging) console.log ("AI AD BLOCKING delayed checks 1, 2, 3, 11, 12");

    if (jQuery(b64d ("I2FpLWFkYi1hZHM=")).length) {
      if (!document.getElementById ("AI_CONST_AI_ADB_1_NAME")) {
        ai_adb_get_script ('ads', ai_adb_1);
      } else ai_adb_1 ();
    }

    if (jQuery(b64d ("I2FpLWFkYi1zcG9uc29ycw==")).length) {
      if (typeof window.AI_CONST_AI_ADB_2_NAME == "undefined") {
        ai_adb_get_script ('sponsors', ai_adb_2);
      } else ai_adb_2 ();
    }

    var banner_advert_container = b64d ("I2Jhbm5lci1hZHZlcnQtY29udGFpbmVy");
    var banner_advert_container_img = b64d ("I2Jhbm5lci1hZHZlcnQtY29udGFpbmVyIGltZw==");
    if (jQuery(banner_advert_container).length) {
      if (jQuery(banner_advert_container_img).length > 0) {
        if (jQuery(banner_advert_container_img).outerHeight() === 0) {
          if (!ai_adb_active || ai_debugging_active) ai_adb_detected (3);
        } else ai_adb_undetected (3);
        jQuery(banner_advert_container_img).remove();
      }
    }

    if (jQuery(b64d ("I2FpLWFkYi1iYW5uZXI=")).length) {
      ai_adb_11 ();
    }

    if (jQuery(b64d ("I2FpLWFkYi0zMDB4MjUw")).length) {
      ai_adb_12 ();
    }
  }, 1150);
});

