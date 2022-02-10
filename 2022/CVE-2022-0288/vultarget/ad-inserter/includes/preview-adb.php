<?php

function ai_media_buttons () {
  echo '<button type="button" id="add-p" class="button" style="width: 36px;" title="',  __('Add dummy paragraph', 'ad-inserter'),  '">+</button>';
  echo '<button type="button" id="remove-p" class="button" style="width: 36px;" title="',  __('Remove dummy paragraph', 'ad-inserter'),  '">-</button>';
  echo '<button type="button" id="use-button" class="button" style="width: 90px;" title="',  __('Use current settings', 'ad-inserter'),  '"> ',  _x('Use', 'Button', 'ad-inserter'),  ' </button>';
  echo '<button type="button" id="reset-button" class="button" style="width: 90px;" title="',  __('Reset to the saved settings', 'ad-inserter'), '"> ' . _x('Reset', 'Button', 'ad-inserter'),  ' </button>';
  echo '<button type="button" id="default-button" class="button" style="width: 90px;" title="',  __('Reset to the default settings', 'ad-inserter'),  '"> ' . _x('Default', 'Button', 'ad-inserter'),  ' </button>';
  echo '<button type="button" id="cancel-button" class="button" style="width: 90px;" title="',  __('Close preview window', 'ad-inserter'),  '"> ', _x('Cancel', 'Button ', 'ad-inserter'),  ' </button>';
}

function ai_mce_buttons ($buttons, $id) {
  $buttons = array_unique (array_merge ($buttons, array ('styleselect')));
  return $buttons;
}

function ai_mce_buttons_2 ($buttons, $id) {
  $buttons = array_unique (array_merge ($buttons, array ('forecolor', 'backcolor', 'hr', 'fontselect', 'fontsizeselect')));
  if (($key = array_search ('wp_help', $buttons)) !== false) {
    unset ($buttons [$key]);
  }
  return $buttons;
}

function generate_code_preview_adb ($client_code, $process_php, $code_only = false, $head = null, $adb_message = null, $footer = null) {
  global $block_object, $ai_wp_data;

  $ai_wp_data [AI_WP_DEBUGGING] = 0;

  if ($adb_message === null) {
    $obj = new ai_AdA ();
    $obj->wp_options = $block_object [AI_ADB_MESSAGE_OPTION_NAME]->wp_options;

    $obj->wp_options [AI_OPTION_CODE]         = $client_code;
    $obj->wp_options [AI_OPTION_PROCESS_PHP]  = $process_php;

    $adb_message = $obj->ai_getCode ();
  }

  wp_enqueue_script ('ai-adb-js',   plugins_url ('includes/js/ad-inserter-check.js', AD_INSERTER_FILE), array (
    'jquery',
    'jquery-ui-tabs',
    'jquery-ui-button',
    'jquery-ui-tooltip',
    'jquery-ui-datepicker',
    'jquery-ui-dialog',
  ), AD_INSERTER_VERSION);

  wp_enqueue_style  ('ai-adb-css', plugins_url ('css/ai-settings.css', AD_INSERTER_FILE), array (), AD_INSERTER_VERSION);

  add_action ('media_buttons', 'ai_media_buttons');

  add_filter ('mce_buttons',   'ai_mce_buttons',   99999, 2);
  add_filter ('mce_buttons_2', 'ai_mce_buttons_2', 99999, 2);

  if ($head === null) {
    ob_start ();
    wp_head ();
    $head = ob_get_clean ();
    $head = preg_replace ('#<title>([^<]*)</title>#', '<title>' . AD_INSERTER_NAME . ' ' . __('Ad Blocking Detected Message Preview', 'ad-inserter') . '</title>', $head);
  }

  if ($footer === null) {
    ob_start ();
    wp_footer ();
    $footer = ob_get_clean ();
  }

  if ($code_only) {
    return array ('head' => $head, 'message' => $adb_message, 'footer' => $footer);
  }

?>
<html>
<head>
<?php
  echo $head;
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<script>

//  initialize_preview ();

  window.onkeydown = function (event) {
    if (event.keyCode === 27 ) {
      window.close();
    }
  };

  function b64e (str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa (encodeURIComponent (str).replace (/%([0-9A-F]{2})/g,
      function toSolidBytes (match, p1) {
        return String.fromCharCode ('0x' + p1);
    }));
  }

  function b64d (str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent (atob (str).split ('').map (function(c) {
      return '%' + ('00' + c.charCodeAt (0).toString (16)).slice (-2);
    }).join (''));
  }


// https://gist.github.com/RadGH/523bed274f307830752c

// 0) If you are not using the default visual editor, make your own in PHP with a defined editor ID:
//    wp_editor( $content, 'tab-editor' );

// 1) Get contents of your editor in JavaScript:
//   tmce_getContent( 'tab-editor' )

// 2) Set content of the editor:
//   tmce_setContent( content, 'tab-editor' )

// Note: If you just want to use the default editor, you can leave the ID blank:
//   tmce_getContent()
//   tmce_setContent( content )

// Note: If using a custom textarea ID, different than the editor id, add an extra argument:
//   tmce_getContent( 'visual-id', 'textarea-id' )
//   tmce_getContent( content, 'visual-id', 'textarea-id')

// Note: An additional function to provide "focus" to the displayed editor:
//   tmce_focus( 'tab-editor' )

  function tmce_getContent (editor_id, textarea_id) {
    if (typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
    if (typeof textarea_id == 'undefined' ) textarea_id = editor_id;

    if (jQuery('#wp-' + editor_id + '-wrap').hasClass ('tmce-active') && tinyMCE.get (editor_id)) {
      return tinyMCE.get (editor_id).getContent ();
    } else {
        return jQuery ('#'+textarea_id).val();
      }
  }

  function tmce_setContent (content, editor_id, textarea_id) {
    if (typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
    if (typeof textarea_id == 'undefined' ) textarea_id = editor_id;

    if (jQuery('#wp-'+editor_id+'-wrap').hasClass ('tmce-active') && tinyMCE.get (editor_id)) {
      return tinyMCE.get (editor_id).setContent (content);
    } else {
        return jQuery('#'+textarea_id).val (content);
      }
  }

  function tmce_focus (editor_id, textarea_id) {
    if (typeof editor_id == 'undefined') editor_id = wpActiveEditor;
    if (typeof textarea_id == 'undefined') textarea_id = editor_id;

    if (jQuery('#wp-'+editor_id+'-wrap').hasClass ('tmce-active') && tinyMCE.get (editor_id)) {
      return tinyMCE.get (editor_id).focus();
    } else {
        return jQuery('#'+textarea_id).focus();
      }
  }

  function update_message_preview (editor, e) {
    if (e.type == 'keyup' && e.key == 'Escape') window.close();
    jQuery('#message').html (editor.getContent());
  }

  jQuery(document).ready(function($) {

    function process_display_elements () {
      $('#message').attr ('style', '<?php echo str_replace (array ("'", "\r", "\n"), array ("\'", '', ''), AI_BASIC_ADB_MESSAGE_CSS) ; ?>' + $("#message-css").val ()).css ({'position': 'absolute'});
      $('#overlay').attr ('style', '<?php echo str_replace (array ("'", "\r", "\n"), array ("\'", '', ''), AI_BASIC_ADB_OVERLAY_CSS) ; ?>' + $("#overlay-css").val ()).css ({'position': 'absolute'});
      $('#message').html (tmce_getContent ());
    }

    function initialize_preview () {

      var debug = <?php echo get_backend_javascript_debugging () ? 'true' : 'false'; ?>;

      function load_from_settings () {

        if (window.opener != null && !window.opener.closed) {
          var settings = $(window.opener.document).contents();

          tmce_setContent (b64d ("<?php echo base64_encode ($adb_message); ?>"));

          $("#message-css").val (settings.find ("#message-css").val ());
          $("#overlay-css").val (settings.find ("#overlay-css").val ());

          process_display_elements ();
        }
      }

      function apply_to_settings () {
        if (window.opener != null && !window.opener.closed) {
          var settings = $(window.opener.document).contents ();

          window.opener.set_editor_text ('a', tmce_getContent ())
          settings.find ("#message-css").val ($("#message-css").val ());
          settings.find ("#overlay-css").val ($("#overlay-css").val ());
        }
      }

      $("#use-button").button ({
      }).click (function () {
        apply_to_settings ();
        window.close();
      });

      $("#default-button").button ({
      }).click (function () {
        tmce_setContent ('<?php echo str_replace (array ("'", "\r", "\n"), array ("\'", '', ''), AI_DEFAULT_ADB_MESSAGE); ?>');
        $("#message-css").val ('<?php echo str_replace (array ("'", "\r", "\n"), array ("\'", '', ''), AI_DEFAULT_ADB_MESSAGE_CSS); ?>');
        $("#overlay-css").val ('<?php echo str_replace (array ("'", "\r", "\n"), array ("\'", '', ''), AI_DEFAULT_ADB_OVERLAY_CSS); ?>');
        process_display_elements ();
      });

      $("#reset-button").button ({
      }).click (function () {
        load_from_settings ();
      });

      $("#cancel-button").button ({
      }).click (function () {
        window.close();
      });

      $("#message-css").on ('input', function() {
        process_display_elements ();
      });

      $("#overlay-css").on ('input', function() {
        process_display_elements ();
      });

      $('#aiadb').bind('input propertychange', function() {
        $('#message').html ($('#aiadb').val ());
      });

      $('#add-p').click(function (e) {
        var paragraphs = $('#dummy-text').children ('p.dummy');
        if (paragraphs.length < 10) {
          paragraphs.last().clone().appendTo ($('#dummy-text'));
        }
      });

      $('#remove-p').click(function (e) {
        var paragraphs = $('#dummy-text').children ('p.dummy');
        if (paragraphs.length > 1) {
          paragraphs.last().remove();
        }
      });

      setTimeout (load_from_settings, 350);
    }

    initialize_preview ();

    setTimeout (show_blocked_warning, 400);
  });

  function show_blocked_warning () {
    jQuery("#blocked-warning.warning-enabled").show ();
  }

</script>
<style>
body {
  background: #fff;
  display: block;
  margin: 8px;
}

input[type="text"] {
  max-width: initial;
}

button,
input[type="button"] {
  width: initial;
}

input[type="text"] {
    margin: 0;
}

.button {
  font-size: 14px!important;
}

#text {
  position: relative;
}
</style>
</head>
<body style='font-family: arial; overflow-x: hidden;'>
  <div id="ai-data" style="display: none;" version="<?php echo AD_INSERTER_VERSION; ?>"></div>

  <div id="blocked-warning" class="warning-enabled" style="padding: 2px 8px 2px 8px; margin: 8px 0 8px 0; border: 1px solid rgb(221, 221, 221); border-radius: 5px;">
    <div style="float: right; text-align: right; margin: 20px 0px 0px 0;">
       <?php _e ('This page was not loaded properly. Please check browser, plugins and ad blockers.', 'ad-inserter'); ?>
    </div>
    <h3 style="color: red;" title="Error loading page"><?php _e ('PAGE BLOCKED', 'ad-inserter'); ?></h3>

    <div style="clear: both;"></div>
  </div>

  <div id="text">
    <div id="overlay"></div>
    <div id="message"></div>
    <div id="dummy-text">
      <p class='dummy'>Lorem ipsum dolor sit amet, quo ea quem munere, mea eu dicunt moderatius interesset. Eam ei saepe insolens, an wisi timeam vel, regione eruditi admodum in mei.
        Nam iusto definitiones id, an graeci reprimique usu, eum iusto eruditi ei. At sint elitr propriae pro.</p>
      <p class='dummy'>Clita periculis an eam, movet populo semper has an. Id quo unum justo affert, recusabo aliquando nam te, mei aeque soluta voluptaria no. Tantas pertinax ei eos, vim ipsum reformidans ne, lucilius mediocrem explicari cu cum. Eum integre definitionem vituperatoribus te.
        His veri legere assentior ei, vis ferri detraxit cu. No quidam aliquip efficiantur sed, nusquam efficiendi dissentiunt pri ea.</p>
      <p class='dummy'>Exerci aliquando ius ne, nostro timeam in sed, quaeque moderatius his at. At consul iudicabit nam, vel ei legere disputationi. Ea ius quidam sententiae.
        Diam elit no sit, facete democritum referrentur est at. Quo et accusata dissentias, vis eligendi interpretaris ex.</p>
      <p class='dummy'>In choro eleifend his. Qui no ignota mucius labore, dicta eruditi usu ea. Usu id insolens conceptam definitionem.
        Mei quot fastidii pericula ex. Ut etiam delicata aliquando sea, aliquam senserit theophrastus et sit. Dolores torquatos mel ut, alia deserunt eu mea.</p>
    </div>
  </div>

  <div style="width: 100%; min-height: 310px; margin: 8px 0;">
<?php

  $editorSettings = array(
    'wpautop' => false,
    'media_buttons' => true,
    'textarea_rows' => 10,
    'tinymce'=> array (
      'menubar ' => false,
      'statusbar' => false,
      'setup' => 'function (editor) {
          editor.on("change keyup redo undo", function (e) {
              update_message_preview (editor, e);
          });
      }',
      'protect' => '[/<\?php.*?\?'.'>/g]',
    ),
  );

  add_filter ('wp_default_editor', 'ai_wp_default_editor');
//  wp_editor ($adb_message, 'aiadb', $editorSettings);
  wp_editor ('', 'aiadb', $editorSettings);

// To disable Notice: Trying to get property of non-object in /wp-content/plugins/tinymce-advanced/tinymce-advanced.php on line 271
  $error_reporting = error_reporting ();
  error_reporting ($error_reporting & ~E_NOTICE);

  _WP_Editors::enqueue_scripts();
  print_footer_scripts ();
  _WP_Editors::editor_js();

  error_reporting ($error_reporting);

?>
  </div>

  <div class="max-input" style="margin: 8px 0;">
    <span style="display: table-cell; width: 90px; white-space: nowrap; font-size: 13px;"><?php _e ('Message CSS', 'ad-inserter'); ?></span>
    <input style="display: table-cell; border-radius: 5px; width: 100%; padding-left: 5px;" type="text" id="message-css" value="" size="50" maxlength="200" />
  </div>

  <div class="max-input" style="margin: 8px 0;">
    <span style="display: table-cell; width: 90px; white-space: nowrap; font-size: 13px;"><?php _e ('Overlay CSS', 'ad-inserter'); ?></span>
    <input style="display: table-cell; border-radius: 5px; width: 100%; padding-left: 5px;" type="text" id="overlay-css" value="" size="50" maxlength="200" />
  </div>

<?php echo $footer; ?>
</body>
</html>
<?php
}

