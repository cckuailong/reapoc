jQuery (function ($) {

  var ai_rotation_triggers = new Array ();

  ai_process_rotation = function (rotation_block) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

//    if (ai_debug) console.log ('#', $(rotation_block).hasClass ('ai-unprocessed'));

    if (!$(rotation_block).hasClass ('ai-unprocessed') && !$(rotation_block).hasClass ('ai-timer')) return;
    $(rotation_block).removeClass ('ai-unprocessed').removeClass ('ai-timer');

    if (ai_debug) console.log ('');

    var ai_rotation_triggers_found = false;
    if (typeof $(rotation_block).data ('info') != 'undefined') {
      var block_info = JSON.parse (atob ($(rotation_block).data ('info')));
      var rotation_id = block_info [0];
      var rotation_selector = "div.ai-rotate.ai-" + rotation_id;

      if (ai_rotation_triggers.includes (rotation_selector)) {
        ai_rotation_triggers.splice (ai_rotation_triggers.indexOf (rotation_selector), 1);
        ai_rotation_triggers_found = true;

        if (ai_debug) console.log ('AI TIMED ROTATION TRIGGERS', ai_rotation_triggers);
      }
    }

    if (typeof rotation_block.length == 'number') {
      if (ai_debug) console.log ('AI ROTATE process rotation:', rotation_block.length, 'rotation blocks');
      for (var index = 0; index < rotation_block.length; index ++) {
        if (ai_debug) console.log ('AI ROTATE process rotation block index:', index);

        if (ai_debug) console.log ('AI ROTATE process rotation block:', rotation_block [index]);

        if (index == 0) ai_process_single_rotation (rotation_block [index], true); else ai_process_single_rotation (rotation_block [index], false);
      }
    } else {
        if (ai_debug) console.log ('AI ROTATE process rotation: 1 rotation block');

        ai_process_single_rotation (rotation_block, !ai_rotation_triggers_found);
      }
  }

  ai_process_single_rotation = function (rotation_block, trigger_rotation) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//    var ai_debug = false;

    var rotate_options = $(rotation_block).children (".ai-rotate-option");

    if (rotate_options.length == 0) return;

    if (ai_debug) {
      console.log ('AI ROTATE process single rotation, trigger rotation', trigger_rotation);
      console.log ('AI ROTATE', 'block', $(rotation_block).attr ('class') + ',', rotate_options.length, 'options');
    }

    rotate_options.hide ();

//    rotate_options.css ({"visibility": "hidden"});

//    rotate_options.animate ({
//        opacity: 0,
//      }, 500, function() {
//    });

    if (typeof $(rotation_block).data ('next') == 'undefined') {
      if (typeof $(rotate_options [0]).data ('group') != 'undefined') {
        var random_index = - 1;
        var all_ai_groups = $('span[data-ai-groups]');
        var ai_groups = [];

        all_ai_groups.each (function (index) {
          var visible = !!($(this)[0].offsetWidth || $(this)[0].offsetHeight || $(this)[0].getClientRects().length);
          if (visible) {
            ai_groups.push (this);
          }
        });

        if (ai_debug) console.log ('AI ROTATE GROUPS:', ai_groups.length, 'group markers found');

        if (ai_groups.length >= 1) {
          var groups = JSON.parse (b64d ($(ai_groups).first ().data ('ai-groups')));

          if (ai_debug) console.log ('AI ROTATE GROUPS:', groups);

          groups.forEach (function (group, index) {
            if (random_index == - 1)
              rotate_options.each (function (index) {
                var option_group = b64d ($(this).data ('group'));
                if (option_group == group) {
                  random_index = index;
                  return false;
                }
              });
          });
        }
      } else {
          var thresholds_data = $(rotation_block).data ('shares');
          if (typeof thresholds_data === 'string') {
            var thresholds = JSON.parse (atob (thresholds_data));
            var random_threshold = Math.round (Math.random () * 100);
            for (var index = 0; index < thresholds.length; index ++) {
              var random_index = index;
              if (thresholds [index] < 0) continue;
              if (random_threshold <= thresholds [index]) break;
            }
          } else {
              var unique = $(rotation_block).hasClass ('ai-unique');
              var d = new Date();

              if (unique) {
                 if (typeof ai_rotation_seed != 'number') {
                   ai_rotation_seed = (Math.floor (Math.random () * 1000) + d.getMilliseconds()) % rotate_options.length;
                 }

                 var block_counter = $(rotation_block).data ('counter');

                 if (ai_debug) console.log ('AI ROTATE SEED:', ai_rotation_seed, ' COUNTER:', block_counter);

                 var random_index = ai_rotation_seed + block_counter;
                 if (random_index >= rotate_options.length) random_index -= rotate_options.length;
              } else {
                  var random_index = Math.floor (Math.random () * rotate_options.length);
                  var n = d.getMilliseconds();
                  if (n % 2) random_index = rotate_options.length - random_index - 1;
                }
            }
        }
    } else {
        var random_index = parseInt ($(rotation_block).attr ('data-next'));

        if (ai_debug) console.log ('AI TIMED ROTATION next index:', random_index);

        var option = $(rotate_options [random_index]);

        if (typeof option.data ('code') != 'undefined') {
          option = $(b64d (option.data ('code')));
        }

        var group_markers = option.find ('span[data-ai-groups]').addBack ('span[data-ai-groups]');
        if (group_markers.length != 0) {
          if (ai_debug) {
            var next_groups = JSON.parse (b64d (group_markers.first ().data ('ai-groups')));
            console.log ('AI TIMED ROTATION next option sets groups', next_groups);
          }

          var group_rotations = $('.ai-rotation-groups');
          if (group_rotations.length != 0) {
            setTimeout (function() {ai_process_group_rotations ();}, 5);
          }
        }
      }

    if ($(rotation_block).hasClass ('ai-rotation-scheduling')) {
      random_index = - 1;
//      var gmt = $(rotation_block).data ('gmt');

//      if (ai_debug) console.log ('AI SCHEDULED ROTATION, GMT:', gmt / 1000);

      for (var option_index = 0; option_index < rotate_options.length; option_index ++) {
        var option = $(rotate_options [option_index]);
        var option_data = option.data ('scheduling');
        if (typeof option_data != 'undefined') {
          var scheduling_data = b64d (option_data);

          var result = true;
          if (scheduling_data.indexOf ('^') == 0) {
            result = false;
            scheduling_data = scheduling_data.substring (1);
          }

          var scheduling_data_array = scheduling_data.split ('=');

          if (scheduling_data.indexOf ('%') != -1) {
            var scheduling_data_time = scheduling_data_array [0].split ('%');
          } else var scheduling_data_time = [scheduling_data_array [0]];

          var time_unit = scheduling_data_time [0].trim ().toLowerCase ();

          var time_division = typeof scheduling_data_time [1] != 'undefined' ? scheduling_data_time [1].trim () : 0;
          var scheduling_time_option = scheduling_data_array [1].replace (' ', '');

          if (ai_debug) console.log ('');
          if (ai_debug) console.log ('AI SCHEDULED ROTATION OPTION', option_index + (!result ? ' INVERTED' : '') + ':', time_unit + (time_division != 0 ? '%' + time_division : '') + '=' + scheduling_time_option);

          var current_time = new Date ().getTime ();
          var date = new Date (current_time);

          var time_value = 0;
          switch (time_unit) {
            case 's':
              time_value = date.getSeconds ();
              break;
            case 'i':
              time_value = date.getMinutes ();
              break;
            case 'h':
              time_value = date.getHours ();
              break;
            case 'd':
              time_value = date.getDate ();
              break;
            case 'm':
              time_value = date.getMonth ();
              break;
            case 'y':
              time_value = date.getFullYear ();
              break;
            case 'w':
              time_value = date.getDay ();
              if (time_value == 0) time_value = 6; else time_value = time_value - 1;
          }

          var time_modulo = time_division != 0 ? time_value % time_division : time_value;

          if (ai_debug) {
            if (time_division != 0) {
              console.log ('AI SCHEDULED ROTATION TIME VALUE:', time_value, '%', time_division, '=', time_modulo);
            } else console.log ('AI SCHEDULED ROTATION TIME VALUE:', time_value);
          }

          var scheduling_time_options = scheduling_time_option.split (',');

          var option_selected = !result;

          for (var time_option_index = 0; time_option_index < scheduling_time_options.length; time_option_index ++) {
            var time_option = scheduling_time_options [time_option_index];

            if (ai_debug) console.log ('AI SCHEDULED ROTATION TIME ITEM', time_option);

            if (time_option.indexOf ('-') != - 1) {
              var time_limits = time_option.split ('-');

              if (ai_debug) console.log ('AI SCHEDULED ROTATION TIME ITEM LIMITS', time_limits [0], '-', time_limits [1]);

              if (time_modulo >= time_limits [0] && time_modulo <= time_limits [1]) {
                option_selected = result;
                break
              }
            } else
            if (time_modulo == time_option) {
              option_selected = result;
              break
            }
          }

          if (option_selected) {
            random_index = option_index;

            if (ai_debug) console.log ('AI SCHEDULED ROTATION OPTION', random_index , 'SELECTED');

            break;
          }
        }
      }
    }

    if (random_index < 0 || random_index >= rotate_options.length) {
      if (ai_debug) console.log ('AI ROTATE no option selected');
      return;
    }

    var option = $(rotate_options [random_index]);
    var option_time_text = '';

    if (typeof option.data ('time') != 'undefined') {
      var rotation_time = atob (option.data ('time'));

      if (ai_debug) {
        var option_name = b64d (option.data ('name'));
        console.log ('AI TIMED ROTATION index:', random_index + ',', 'name:', '"'+option_name+'",', 'time:', rotation_time);
      }

      if (rotation_time == 0 && rotate_options.length > 1) {
        var next_random_index = random_index;
        do {
          next_random_index++;
          if (next_random_index >= rotate_options.length) next_random_index = 0;

          var next_option = $(rotate_options [next_random_index]);
          if (typeof next_option.data ('time') == 'undefined') {
            random_index = next_random_index;
            option = $(rotate_options [random_index]);
            rotation_time = 0;

            if (ai_debug) console.log ('AI TIMED ROTATION next option has no time: ', next_random_index);

            break;
          }
          var next_rotation_time = atob (next_option.data ('time'));

          if (ai_debug) console.log ('AI TIMED ROTATION check:', next_random_index, 'time:', next_rotation_time);
        } while (next_rotation_time == 0 && next_random_index != random_index);

        if (rotation_time != 0) {
          random_index = next_random_index;
          option = $(rotate_options [random_index]);
          rotation_time = atob (option.data ('time'));
        }

        if (ai_debug) console.log ('AI TIMED ROTATION index:', random_index, 'time:', rotation_time);
      }

      if (rotation_time > 0) {
        var next_random_index = random_index + 1;
        if (next_random_index >= rotate_options.length) next_random_index = 0;

        if (typeof $(rotation_block).data ('info') != 'undefined') {
          var block_info = JSON.parse (atob ($(rotation_block).data ('info')));
          var rotation_id = block_info [0];

          $(rotation_block).attr ('data-next', next_random_index);
          var rotation_selector = "div.ai-rotate.ai-" + rotation_id;

          if (ai_rotation_triggers.includes (rotation_selector)) {
            var trigger_rotation = false;
          }

          if (trigger_rotation) {
            ai_rotation_triggers.push (rotation_selector);

            setTimeout (function() {$(rotation_selector).addClass ('ai-timer'); ai_process_rotation ($(rotation_selector));}, rotation_time * 1000);
//            setTimeout (function() {ai_process_rotation ($(rotation_block));}, rotation_time * 1000);
          }
          option_time_text = ' (' + rotation_time + ' s)';
        }
      }
    }
    else if (typeof option.data ('group') != 'undefined') {
      if (ai_debug) {
        var option_name = b64d (option.data ('name'));
        console.log ('AI ROTATE GROUP', '"' + option_name + '",', 'index:', random_index);
      }
    }
    else {
      // Remove unused options
      if (!ai_debug) {
        rotate_options.each (function (index) {
          if (index != random_index) $(this).remove ();
        });
      }

      if (ai_debug) console.log ('AI ROTATE no time');
      if (ai_debug) console.log ('AI ROTATE index:', random_index);
    }


    option.css ({"display": "", "visibility": "", "position": "", "width": "", "height": "", "top": "", "left": ""}).removeClass ('ai-rotate-hidden').removeClass ('ai-rotate-hidden-2');
//    option.css ({"visibility": "", "position": "", "width": "", "height": "", "top": "", "left": ""}).removeClass ('ai-rotate-hidden').removeClass ('ai-rotate-hidden-2');
//    $(rotation_block).css ({"position": ""}).removeClass ('ai-rotate');
    $(rotation_block).css ({"position": ""});

//    option.css ({"visibility": "visible"});

//    option.stop ().animate ({
//        opacity: 1,
//      }, 500, function() {
//    });

    if (typeof option.data ('code') != 'undefined') {
      rotate_options.empty();

      var option_code = b64d (option.data ('code'));

      if (ai_debug) console.log ('AI ROTATE CODE');

      option.append (option_code);

      ai_process_elements ();
    }

    var option_name = b64d (option.data ('name'));
    var debug_block_frame = $(rotation_block).closest ('.ai-debug-block');
    if (debug_block_frame.length != 0) {
      var name_tag = debug_block_frame.find ('kbd.ai-option-name');
      // Do not set option name in nested debug blocks
      var nested_debug_block = debug_block_frame.find ('.ai-debug-block');
      if (typeof nested_debug_block != 'undefined') {
        var name_tag2 = nested_debug_block.find ('kbd.ai-option-name');
        name_tag = name_tag.slice (0, name_tag.length - name_tag2.length);
      }
      if (typeof name_tag != 'undefined') {
        var separator = name_tag.first ().data ('separator');
        if (typeof separator == 'undefined') separator = '';
        name_tag.html (separator + option_name + option_time_text);
      }
    }

    var tracking_updated = false;
    var adb_show_wrapping_div = $(rotation_block).closest ('.ai-adb-show');
    if (adb_show_wrapping_div.length != 0) {
      if (adb_show_wrapping_div.attr ("data-ai-tracking")) {
        var data = JSON.parse (b64d (adb_show_wrapping_div.attr ("data-ai-tracking")));
        if (typeof data !== "undefined" && data.constructor === Array) {
          data [1] = random_index + 1;
          data [3] = option_name ;

          if (ai_debug) console.log ('AI ROTATE TRACKING DATA ', b64d (adb_show_wrapping_div.attr ("data-ai-tracking")), ' <= ', JSON.stringify (data));

          adb_show_wrapping_div.attr ("data-ai-tracking", b64e (JSON.stringify (data)))

          // Inserted code may need click trackers
          adb_show_wrapping_div.addClass ('ai-track');

          tracking_updated = true;
        }
      }
    }

    if (!tracking_updated) {
      var wrapping_div = $(rotation_block).closest ('div[data-ai]');
      if (typeof wrapping_div.attr ("data-ai") != "undefined") {
        var data = JSON.parse (b64d (wrapping_div.attr ("data-ai")));
        if (typeof data !== "undefined" && data.constructor === Array) {
          data [1] = random_index + 1;
          data [3] = option_name;
          wrapping_div.attr ("data-ai", b64e (JSON.stringify (data)))

          // Inserted code may need click trackers
          wrapping_div.addClass ('ai-track');

          if (ai_debug) console.log ('AI ROTATE TRACKING DATA ', b64d (wrapping_div.attr ("data-ai")));

        }
      }
    }
  }

  ai_process_rotations = function () {
    $("div.ai-rotate").each (function (index, element) {
      ai_process_rotation (this);
    });
  }

  function ai_process_group_rotations () {
    $("div.ai-rotate.ai-rotation-groups").each (function (index, element) {
      $(this).addClass ('ai-timer');
      ai_process_rotation (this);
    });
  }

  ai_process_rotations_in_element = function (el) {
    $("div.ai-rotate", el).each (function (index, element) {
      ai_process_rotation (this);
    });
  }

  $(document).ready (function($) {
    setTimeout (function() {ai_process_rotations ();}, 10);
  });

});

ai_process_elements_active = false;

function ai_process_elements () {
  if (!ai_process_elements_active)
    setTimeout (function() {
      ai_process_elements_active = false;

      if (typeof ai_process_rotations == 'function') {
        ai_process_rotations ();
      }

      if (typeof ai_process_lists == 'function') {
        ai_process_lists (jQuery (".ai-list-data"));
      }

      if (typeof ai_process_ip_addresses == 'function') {
        ai_process_ip_addresses (jQuery (".ai-ip-data"));
      }

      if (typeof ai_process_filter_hooks == 'function') {
        ai_process_filter_hooks (jQuery (".ai-filter-check"));
      }

      if (typeof ai_adb_process_blocks == 'function') {
        ai_adb_process_blocks ();
      }

      //?? duplicate down
//      if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
//        ai_install_click_trackers ();
//      }

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
  ai_process_elements_active = true;
}

