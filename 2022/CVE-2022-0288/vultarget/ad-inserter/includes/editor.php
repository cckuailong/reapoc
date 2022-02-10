<?php

function ai_editor_media_buttons () {
  echo '<button type="button" id="use-button" class="button" style="width: 90px; padding: 0 10px;" title="Use current settings"> ', __('Use', 'ad-inserter'), ' </button>';
  echo '<button type="button" id="reset-button" class="button" style="width: 90px; padding: 0 10px;" title="Reset to the saved settings"> ', __('Reset', 'ad-inserter'), ' </button>';
  echo '<button type="button" id="cancel-button" class="button" style="width: 90px; padding: 0 10px;" title="Use current settings"> ', __('Cancel', 'ad-inserter'), ' </button>';
}

function ai_editor_mce_buttons ($buttons, $id) {
  $buttons = array_unique (array_merge ($buttons, array ('styleselect')));
  return $buttons;
}

function ai_editor_mce_buttons_2 ($buttons, $id) {
  $buttons = array_unique (array_merge ($buttons, array ('forecolor', 'backcolor', 'hr', 'fontselect', 'fontsizeselect')));
  if (($key = array_search ('wp_help', $buttons)) !== false) {
    unset ($buttons [$key]);
  }
  return $buttons;
}

function generate_code_editor ($block, $client_code, $process_php) {
  global $block_object, $ai_wp_data;

  $ai_wp_data [AI_WP_DEBUGGING] = 0;

  $obj = new ai_Block ($block);
  $obj->wp_options = $block_object [$block]->wp_options;

  $obj->wp_options [AI_OPTION_CODE]         = $client_code;
  $obj->wp_options [AI_OPTION_PROCESS_PHP]  = $process_php;

  $code = $obj->ai_getCode ();

  wp_enqueue_script ('ai-adb-js',   plugins_url ('includes/js/ad-inserter-check.js', AD_INSERTER_FILE), array (
    'jquery',
    'jquery-ui-tabs',
    'jquery-ui-button',
    'jquery-ui-tooltip',
    'jquery-ui-datepicker',
    'jquery-ui-dialog',
  ), AD_INSERTER_VERSION);

  wp_enqueue_style  ('ai-editor-css', plugins_url ('css/ai-settings.css', AD_INSERTER_FILE), array (), AD_INSERTER_VERSION);

  add_action ('media_buttons', 'ai_editor_media_buttons');

  add_filter ('mce_buttons',   'ai_editor_mce_buttons',   99999, 2);
  add_filter ('mce_buttons_2', 'ai_editor_mce_buttons_2', 99999, 2);
  add_filter ('wp_default_editor', 'ai_wp_default_editor');

  $editorSettings = array(
    'wpautop' => true,
    'media_buttons' => true,
    'textarea_rows' => 38,
    'tinymce'=> array (
      'menubar ' => false,
      'statusbar' => false,
//      'setup' => 'function (editor) {
//          editor.on("change keyup redo undo", function (e) {
//              update_message_preview (editor, e);
//          });
//      }',
      'protect' => '[/<\?php.*?\?'.'>/g]',
    ),
  );

  ob_start ();
  wp_head ();
  $head = ob_get_clean ();
  $head = preg_replace ('#<title>([^<]*)</title>#', '<title>' . AD_INSERTER_NAME . ' ' . __('Visual Code Editor', 'ad-inserter') . '</title>', $head);
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
      return tinyMCE.get(editor_id).getContent();
    } else {
        return jQuery('#'+textarea_id).val();
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

//  function update_message_preview (editor, e) {
//    if (e.type == 'keyup' && e.key == 'Escape') window.close();
//    jQuery('#code-preview').html (editor.getContent());
//  }

  jQuery(document).ready(function($) {

    function process_display_elements () {
      $('#code-preview').html (tmce_getContent ());
    }

    function initialize_preview () {

      var debug = <?php echo get_backend_javascript_debugging () ? 'true' : 'false'; ?>;

      function load_from_settings () {

        if (window.opener != null && !window.opener.closed) {
          var settings = $(window.opener.document).contents();

          tmce_setContent (b64d ("<?php echo base64_encode ($code); ?>"));

          process_display_elements ();
        }
      }

      function apply_to_settings () {
        if (window.opener != null && !window.opener.closed) {
          var settings = $(window.opener.document).contents ();

          window.opener.set_editor_text (<?php echo $block; ?>, tmce_getContent ())
        }
      }

      $("#use-button").button ({
      }).click (function () {
        apply_to_settings ();
        window.close();
      });

      $("#reset-button").button ({
      }).click (function () {
        load_from_settings ();
      });

      $("#cancel-button").button ({
      }).click (function () {
        window.close();
      });

      $('#ai-editor').bind ('input propertychange', function() {
        $('#code-preview').html ($('#ai-editor').val ());
      });

      setTimeout (load_from_settings, 300);
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

button,
input[type="button"] {
  width: initial;
}

.button {
  font-size: 14px!important;
}

/*#code-preview {*/
/*  min-height: 300px;*/
/*}*/

/*#text {*/
/*  position: relative;*/
/*}*/
</style>
</head>
<body style='font-family: arial; overflow-x: hidden;'>
  <div id="ai-data" style="display: none;" version="<?php echo AD_INSERTER_VERSION; ?>"></div>

  <div id="blocked-warning" class="warning-enabled" style="padding: 2px 8px 2px 8px; margin: 8px 0 8px 0; border: 1px solid rgb(221, 221, 221); border-radius: 5px;">
    <div style="float: right; text-align: right; margin: 20px 0px 0px 0;">
       <?php _e ('This page was not loaded properly. Please check browser, plugins and ad blockers.', 'ad-inserter'); ?>
    </div>
    <h3 style="color: red;" title="<?php _e ('Error loading page', 'ad-inserter'); ?>"><?php _e ('PAGE BLOCKED', 'ad-inserter'); ?></h3>

    <div style="clear: both;"></div>
  </div>

<!--  <div id="text">-->
<!--    <div id="code-preview"></div>-->
<!--  </div>-->

  <div style="width: 100%; min-height: 310px; margin: 8px 0;">
<?php
//  wp_editor ($code, 'ai-editor', $editorSettings);
  wp_editor ('', 'ai-editor', $editorSettings);

// To disable Notice: Trying to get property of non-object in /wp-content/plugins/tinymce-advanced/tinymce-advanced.php on line 271
  $error_reporting = error_reporting ();
  error_reporting ($error_reporting & ~E_NOTICE);

  _WP_Editors::enqueue_scripts();
  print_footer_scripts ();
  _WP_Editors::editor_js();

  error_reporting ($error_reporting);

?>
  </div>

<?php wp_footer (); ?>
</body>
</html>
<?php
}

