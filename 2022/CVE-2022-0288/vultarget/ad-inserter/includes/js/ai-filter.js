jQuery (function ($) {

  function ai_random_parameter () {
    var current_time = new Date ().getTime ();
    return '&ver=' + current_time + '-' + Math.round (Math.random () * 100000);
  }

  function process_filter_hook_data (ai_filter_hook_blocks) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    ai_filter_hook_blocks.removeClass ('ai-filter-check');

    var enable_block = false;

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ("AI FILTER HOOK DATA: " + ai_filter_hook_data);

    if (ai_filter_hook_data == '') {
      if (ai_debug) console.log ('AI FILTER HOOK DATA EMPTY');
      return;
    }
    try {
      var filter_hook_data_array = JSON.parse (ai_filter_hook_data);

    } catch (error) {
        if (ai_debug) console.log ('AI FILTER HOOK DATA JSON ERROR');
        return;
    }

    if (filter_hook_data_array != null) ai_filter_hook_blocks.each (function () {

      var block_wrapping_div = $(this).closest ('div.AI_FUNCT_GET_BLOCK_CLASS_NAME');
      var block = parseInt ($(this).data ('block'));

      if (ai_debug) console.log ('AI FILTER HOOK BLOCK', block_wrapping_div.attr ('class'));

      enable_block = false;

      if (typeof filter_hook_data_array !== 'undefined') {
        if (filter_hook_data_array.includes ('*')) {
          enable_block = true;
          if (filter_hook_data_array.includes (- block)) {
            enable_block = false;
          }
        }
        else if (filter_hook_data_array.includes (block)) enable_block = true;
      }

      if (ai_debug) console.log ('AI FILTER HOOK BLOCK', block, enable_block ? 'ENABLED' : 'DISABLED');

      $(this).css ({"visibility": "", "position": "", "width": "", "height": "", "z-index": ""});

      var comments = '';
      var comments_decoded = JSON.parse (ai_filter_hook_comments);
      if (typeof comments_decoded == 'string') {
        comments = comments_decoded;
      }
      else if (typeof comments_decoded == 'object') {
        comments = '';
        for (const [key, value] of Object.entries (comments_decoded)) {
          comments = comments + `${key}: ${value}\n`;
        }
      }
      else comments = ai_filter_hook_comments;

      var debug_bar = $(this).prev ('.ai-debug-bar');
      debug_bar.find ('.ai-status').text (enable_block ? ai_front.visible : ai_front.hidden);
      debug_bar.find ('.ai-filter-data').attr ('title', comments);

      if (!enable_block) {
        $(this).hide (); // .ai-filter-check

        if (!block_wrapping_div.find ('.ai-debug-block').length) {
          block_wrapping_div.hide ();
        }

        block_wrapping_div.removeAttr ('data-ai');

        if (block_wrapping_div.find ('.ai-debug-block')) {
          block_wrapping_div.css ({"visibility": ""}).removeClass ('ai-close');
          if (block_wrapping_div.hasClass ('ai-remove-position')) {
            block_wrapping_div.css ({"position": ""});
          }

          // In case client-side insert is used and lists will not be processed
          if (typeof $(this).data ('code') != 'undefined') {
            // Remove ai-list-block to show debug info
            block_wrapping_div.removeClass ('ai-list-block');
            block_wrapping_div.removeClass ('ai-list-block-ip');

            // Remove also 'NOT LOADED' bar if it is there
            if (block_wrapping_div.prev ().hasClass ('ai-debug-info')) {
              block_wrapping_div.prev ().remove ();
            }
          }

        } else block_wrapping_div.hide ();
      } else {
          block_wrapping_div.css ({"visibility": ""});
          if (block_wrapping_div.hasClass ('ai-remove-position')) {
            block_wrapping_div.css ({"position": ""});
          }

          if (typeof $(this).data ('code') != 'undefined') {
            var block_code = b64d ($(this).data ('code'));

            if ($(this).closest ('head').length != 0) {
              $(this).after (block_code);
              if (!ai_debug) $(this).remove ();
            } else $(this).append (block_code);

//                if (!ai_debug)
            $(this).attr ('data-code', '');

            if (ai_debug) console.log ('AI INSERT CODE', $(block_wrapping_div).attr ('class'));
            if (ai_debug) console.log ('');

            ai_process_element (this);
          }
        }

      block_wrapping_div.removeClass ('ai-list-block-filter');
    });
  }

  ai_process_filter_hooks = function (ai_filter_hook_blocks) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//    var ai_debug = false;

    if (ai_filter_hook_blocks == null) {
      ai_filter_hook_blocks = $("div.ai-filter-check, meta.ai-filter-check");
    } else {
        ai_filter_hook_blocks = ai_filter_hook_blocks.filter ('.ai-filter-check');
      }

    if (!ai_filter_hook_blocks.length) return;

    if (ai_debug) console.log ("AI PROCESSING FILTER HOOK:", ai_filter_hook_blocks.length, "blocks");

    if (typeof ai_filter_hook_data != 'undefined') {
      if (ai_debug) console.log ("SAVED FILTER HOOK DATA:", ai_filter_hook_data);
      process_filter_hook_data (ai_filter_hook_blocks);
      return;
    }

    if (typeof ai_filter_hook_data_requested != 'undefined') {
      if (ai_debug) console.log ("FILTER HOOK DATA ALREADY REQUESTED, STILL WAITING...");
      return;
    }

    var user_agent = window.navigator.userAgent;
    var language = navigator.language;

    if (ai_debug) console.log ("REQUESTING FILTER HOOK DATA");
    if (ai_debug) console.log ("USER AGENT:", user_agent);
    if (ai_debug) console.log ("LANGUAGE:", language);

    ai_filter_hook_data_requested = true;

    var ai_data_id = "AI_NONCE";
    var site_url = "AI_SITE_URL";
    var page = site_url+"/wp-admin/admin-ajax.php?action=ai_ajax&filter-hook-data=all&ai_check=" + ai_data_id + '&http_user_agent=' + encodeURIComponent (user_agent) + '&http_accept_language=' + encodeURIComponent (language) + ai_random_parameter ();

    $.get (page, function (filter_hook_data) {

      if (filter_hook_data == '') {
        var error_message = 'Ajax request returned empty data, filter hook checks not processed';
        console.error (error_message);

        if (typeof ai_js_errors != 'undefined') {
          ai_js_errors.push ([error_message, page, 0]);
        }
      } else {
          try {
            var filter_hook_data_test = JSON.parse (filter_hook_data);
          } catch (error) {
            var error_message = 'Ajax call returned invalid data, filter hook checks not processed';
            console.error (error_message, filter_hook_data);

            if (typeof ai_js_errors != 'undefined') {
              ai_js_errors.push ([error_message, page, 0]);
            }
          }
        }

      ai_filter_hook_data = JSON.stringify (filter_hook_data_test ['blocks']);
      ai_filter_hook_comments = JSON.stringify (filter_hook_data_test ['comments']);

      if (ai_debug) console.log ('');
      if (ai_debug) console.log ("AI FILTER HOOK RETURNED DATA:", ai_filter_hook_data);
      if (ai_debug) console.log ("AI FILTER HOOK RETURNED COMMENTS:", filter_hook_data_test ['comments']);

      // Check blocks again - some blocks might get inserted after the filte hook data was requested
      ai_filter_hook_blocks = $("div.ai-filter-check, meta.ai-filter-check");

      if (ai_debug) console.log ("AI FILTER HOOK BLOCKS:", ai_filter_hook_blocks.length);

      process_filter_hook_data (ai_filter_hook_blocks);
    }).fail (function(jqXHR, status, err) {
      if (ai_debug) console.log ("Ajax call failed, Status: " + status + ", Error: " + err);
      $("div.ai-filter-check").each (function () {
        $(this).css ({"display": "none", "visibility": "", "position": "", "width": "", "height": "", "z-index": ""}).removeClass ('ai-filter-check').hide ();
      });
    });
  }

  $(document).ready (function($) {
    setTimeout (function () {ai_process_filter_hooks ()}, 3);
  });
});

function ai_process_element (element) {
  setTimeout (function() {
    if (typeof ai_process_rotations_in_element == 'function') {
      ai_process_rotations_in_element (element);
    }

    if (typeof ai_process_lists == 'function') {
      ai_process_lists (jQuery (".ai-list-data", element));
    }

    if (typeof ai_process_ip_addresses == 'function') {
      ai_process_ip_addresses (jQuery (".ai-ip-data", element));
    }

    if (typeof ai_process_filter_hooks == 'function') {
      ai_process_filter_hooks (jQuery (".ai-filter-check", element));
    }

    if (typeof ai_adb_process_blocks == 'function') {
      ai_adb_process_blocks (element);
    }

    if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
      ai_process_impressions ();
    }
    if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
      ai_install_click_trackers ();
    }

    if (typeof ai_install_close_buttons == 'function') {
      ai_install_close_buttons (document);
    }
  }, 5);
}

