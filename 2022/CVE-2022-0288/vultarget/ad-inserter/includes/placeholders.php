<?php

function generate_placeholder_editor ($placeholder_url, $block) {
  global $wp_version;

  $placeholder_protocol = 'http://';
  $placeholder_domain   = 'via.placeholder.com';

  $base_placeholder_url = $placeholder_protocol . $placeholder_domain . '/';

  if (is_ssl()) {
    $base_placeholder_url = str_replace ('http://', 'https://', $base_placeholder_url);
  }


  $standard_placeholders = array (
    __('Custom', 'ad-inserter'),
    '300x250',
    '336x280',
    '728x90',
    '468x60',
    '250x250',
    '300x600',
    '120x600',
    '160x600',
  );

  $default_placeholder_size             = '300x250';
  $default_placeholder_background_color = 'cccccc';
  $default_placeholder_text_color       = '969696';

  $initial_placeholder_size             = $default_placeholder_size;
  $initial_placeholder_width            = '';
  $initial_placeholder_height           = '';
  $initial_placeholder_background_color = $default_placeholder_background_color;
  $initial_placeholder_text_color       = $default_placeholder_text_color;
  $initial_placeholder_text             = $default_placeholder_size;
  $initial_placeholder_url              = $base_placeholder_url . $initial_placeholder_size;

  $parameters = array ();
  if (strpos ($placeholder_url, $placeholder_domain) !== false) {
    $initial_placeholder_url = $placeholder_url;
    $url = str_replace (array ('http://', 'https://', $placeholder_domain . '/'), '', $placeholder_url);

    $query = '';
    if (strpos ($url, '?') !== false) {
      $query_array = explode ("?", $url);
      $url    = $query_array [0];
      $query  = $query_array [1];
    }

    if (strpos ($url, '.') !== false) {
      $url_array = explode (".", $url);
      $url = $url_array [0];
    }

    $parameters = explode ("/", $url);

    if (isset ($parameters [0])) {
      $import_error = true;
      if (is_int ($parameters [0])) {
        $placeholder_size = $parameters [0] . 'x' . $parameters [0];
         $initial_placeholder_text = $parameters [0] . ' x ' . $parameters [0];
        $import_error = false;
      } elseif (strpos ($parameters [0], 'x') !== false) {
          $size_array = explode ("x", $parameters [0]);
          if (is_numeric ($size_array [0]) && $size_array [0] > 0 && is_numeric ($size_array [1]) && $size_array [1] > 1) {
            $placeholder_size = $parameters [0];
            $initial_placeholder_width  = $size_array [0];
            $initial_placeholder_height = $size_array [1];
            $initial_placeholder_text = $placeholder_size;
            $import_error = false;
          }
        }
      if (!$import_error) {
        $initial_placeholder_size = $placeholder_size;
      }
    }

    if (isset ($parameters [1])) {
      $initial_placeholder_background_color = $parameters [1];

      if (isset ($parameters [2])) {
        $initial_placeholder_text_color = $parameters [2];
      }
    }

    if (strpos ($query, 'text=') === 0) {
      $initial_placeholder_text = trim (str_replace (array ('"', "\\'"), array ('&quot', '&#039'), urldecode (str_replace ('text=', '', $query))));
    }

  }

  $placeholder_selection = array_search ($initial_placeholder_size, $standard_placeholders);
  if (count ($parameters) == 0) $placeholder_selection = 1;
  elseif (($placeholder_selection) === false || count ($parameters) != 1) $placeholder_selection = 0;

?><html>
<head>
<title><?php echo AD_INSERTER_NAME; ?> <?php _e ('Placeholder Editor', 'ad-inserter'); ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<script type='text/javascript' src='<?php echo includes_url ('js/jquery/jquery.js'); ?>?ver=<?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<script type='text/javascript' src='<?php echo admin_url ('load-scripts.php?c=0&amp;load%5B%5D=jquery-core,jquery-migrate,utils,jquery-ui-core&amp;ver='); ?><?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<script type='text/javascript' src='<?php echo includes_url ('js/jquery/ui/effect.min.js'); ?>?ver=<?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<script type='text/javascript' src='<?php echo includes_url ('js/jquery/ui/widget.min.js'); ?>?ver=<?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<script type='text/javascript' src='<?php echo includes_url ('js/jquery/ui/button.min.js'); ?>?ver=<?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<script type='text/javascript' src='<?php echo includes_url ('js/jquery/ui/spinner.min.js'); ?>?ver=<?php echo $wp_version, '+', AD_INSERTER_VERSION; ?>'></script>
<link rel='stylesheet' href='<?php echo plugins_url ('css/jquery-ui-1.10.3.custom.min.css', AD_INSERTER_FILE); ?>?ver=<?php echo AD_INSERTER_VERSION; ?>' media='all' />

<script src='<?php echo plugins_url ('includes/colorpicker/js/bootstrap-colorpicker.min.js', AD_INSERTER_FILE); ?>'></script>
<link rel="stylesheet" href='<?php echo plugins_url ('includes/colorpicker/css/bootstrap-colorpicker.min.css', AD_INSERTER_FILE); ?>'>
<script>

  window.onkeydown = function( event ) {
    if (event.keyCode === 27 ) {
      window.close();
    }
  }

  jQuery(document).ready(function($) {
    var block = <?php echo $block; ?>;
    var base_placeholder_url = '<?php echo $base_placeholder_url; ?>';

    var default_placeholder_text_color = '<?php echo $default_placeholder_text_color; ?>';
    var default_placeholder_background_color = '<?php echo $default_placeholder_background_color; ?>';

    var placeholder_size = '<?php echo $initial_placeholder_size; ?>';
    var placeholder_text_color = '<?php echo $initial_placeholder_text_color; ?>';
    var placeholder_background_color = '<?php echo $initial_placeholder_background_color; ?>';
    var placeholder_text = "?php echo $initial_placeholder_text; ?>";
    var placeholder_url = '';

    var placeholder_width  = 1;
    var placeholder_height = 1;

    function load_from_settings () {

      if (window.opener != null && !window.opener.closed) {
        var settings = $(window.opener.document).contents();

        $("select#block-alignment").val (settings.find ("select#block-alignment-" + block + " option:selected").attr('value')).change();
        $("select#block-alignment option:selected").data ('alt-style', '1');
        $("#custom-css").val (settings.find ("#custom-css-" + block).val ());
        $("#block-name").text (settings.find ("#name-label-" + block).text ());

        process_display_elements ();
      }
    }

    function apply_to_settings () {
      if (window.opener != null && !window.opener.closed) {
        var settings = $(window.opener.document).contents ();

        settings.find ('#banner-image-url-' + block).val (placeholder_url).trigger ("input");

        window.opener.change_banner_image (block);
      }
    }

    function set_placeholder_data () {
      var selected_size = $("select#placeholder-size option:selected").attr('value');

      if (selected_size != 0) {
        placeholder_size             = $("select#placeholder-size option:selected").text ();
        placeholder_background_color = '<?php echo $default_placeholder_background_color; ?>';
        placeholder_text_color       = '<?php echo $default_placeholder_text_color; ?>';

        var sizes = placeholder_size.split ("x");
        placeholder_width  = sizes [0];
        placeholder_height = sizes [1];

        placeholder_text             = placeholder_width + ' x ' + placeholder_height;

        placeholder_url = base_placeholder_url + placeholder_size + '.png';
      } else {
          placeholder_width = parseInt ($("input#width").val ());
          if (isNaN (placeholder_width) || placeholder_width < 1) placeholder_width = 1;
          if (placeholder_width > 3000) placeholder_width = 3000

          placeholder_height = parseInt ($("input#height").val ());
          if (isNaN (placeholder_height) || placeholder_height < 1) placeholder_height = 1;
          if (placeholder_height > 3000) placeholder_height = 3000

          placeholder_size             = placeholder_width + 'x' + placeholder_height;
          placeholder_background_color = $("input#background").val ().trim ().replace ('#', '');
          placeholder_text_color       = $("input#text-color").val ().trim ().replace ('#', '');
          placeholder_text             = $("input#text").val ().trim();

          if (placeholder_text == '') placeholder_text = ' ';

          placeholder_url = base_placeholder_url + placeholder_size;

          if (placeholder_background_color != '') {
            placeholder_url = placeholder_url + '/' + placeholder_background_color;

            if (placeholder_text_color != '') {
              placeholder_url = placeholder_url + '/' + placeholder_text_color;
            }
          }

          placeholder_url = placeholder_url + '.png';

          if (placeholder_text != placeholder_size) {
            placeholder_url = placeholder_url + '?text=' + encodeURIComponent (placeholder_text);
          }
        }
    }

    function update_placeholder () {
      set_placeholder_data ();

      $("span#placeholder-name").text (placeholder_size, placeholder_text_color, placeholder_text);

      $("img#placeholder").attr ('src', placeholder_url);
    }

    $("button#use-button").button ({
    }).click (function () {
      apply_to_settings ();
      window.close();
    });

    $("button#edit-button").button ({
    }).click (function () {
      var selected_size = $("select#placeholder-size option:selected").attr('value');

      if (selected_size != 0) {
        $("input#width").val (placeholder_width);
        $("input#height").val (placeholder_height);
        $("input#background").val ('#' + placeholder_background_color).colorpicker('setValue', '#cccccd');
        $("input#text-color").val ('#' + placeholder_text_color).colorpicker ('setValue', '#969697');
        $("input#text").val (placeholder_text);

        $("select#placeholder-size").val (0).change ();
      }
    });

    $("button#cancel-button").button ({
    }).click (function () {
      window.close();
    });

    $("select#placeholder-size").change (function() {
      var selected_size = $("select#placeholder-size option:selected").attr('value');

      if (selected_size == 0) {
        var sizes = placeholder_size.split ("x");
        $("input#width").val (sizes [0]);
        $("input#height").val (sizes [1]);
        $('div.custom-placeholder-parameters').show ();
      } else $('div.custom-placeholder-parameters').hide ();

      update_placeholder ();
    });

    $("input#width").on ('input', function() {
      update_placeholder ();
    });

    $("input#height").on ('input', function() {
      update_placeholder ();
    });

    $("input#background").colorpicker ({useAlpha: false, useHashPrefix: true, format: 'hex'}).on('colorpickerChange colorpickerCreate colorpickerUpdate', function (e) {
      update_placeholder ();
    }).on ('input', function() {
      update_placeholder ();
    });

    $("input#text-color").colorpicker ({useAlpha: false, useHashPrefix: true, format: 'hex'}).on('colorpickerChange colorpickerCreate colorpickerUpdate', function (e) {
      update_placeholder ();
    }).on ('input', function() {
      update_placeholder ();
    });

    $("input#text").on ('input', function() {
      update_placeholder ();
    });

    update_placeholder ();
  });

</script>
<style>

a, img {
  border: 0;
  font: inherit;
  font-size: 100%;
  font-style: inherit;
  font-weight: inherit;
  margin: 0;
  outline: 0;
  padding: 0;
  vertical-align: baseline;
}

select, input {
  border-radius: 5px;
  padding: 2px 3px;
  border: 1px solid #ddd;
}

div#placeholder-parameters {
  margin: 10px 0;
/*  width: 620px;*/
}

img#placeholder {
  float: left;
  margin: 0 8px 8px 0;
}
.custom-parameters-right {
  float: right;
  margin: 10px 0;
}
.float-left {
  float: left;
}

.float-right {
  float: right;
  margin-left: 10px;
}

@media(max-width: 650px) {
  .float-left {
    clear: both;
    float: none;
    margin: 10px 0;
  }
  .float-right {
    clear: both;
    float: none;
    margin: 10px 0;
  }
  .custom-parameters-right {
    clear: both;
    float: none;
    margin: 10px 0;
  }
}
</style>
</head>
<body style='font-family: arial; text-align: justify; overflow-x: hidden;'>
  <div id="ai-data" style="display: none;" version="<?php echo AD_INSERTER_VERSION; ?>"></div>

  <div style="float: right; width: 90px; margin-left: 20px;">
    <button id="use-button" type="button" style="margin: 0 0 10px 0; font-size: 12px; width: 90px; height: 35px; float: right;" title="<?php _e ('Select placeholder', 'ad-inserter'); ?>" ><?php _e ('Use', 'ad-inserter'); ?></button>
    <button id="edit-button"   type="button" style="margin: 0 0 10px 0; font-size: 12px; width: 90px; height: 35px; float: right;" title="<?php _e ('Edit placeholder size, colors and text', 'ad-inserter'); ?>"><?php _e ('Edit', 'ad-inserter'); ?></button>
    <button id="cancel-button" type="button" style="margin: 0 0 10px 0; font-size: 12px; width: 90px; height: 35px; float: right;" title="<?php _e ('Close placeholder editor', 'ad-inserter'); ?>" ><?php _e ('Cancel', 'ad-inserter'); ?></button>
  </div>

  <div style="float: left; margin-right: 20px">
    <h1 style="margin: 0;"><?php _e ('Placeholder', 'ad-inserter'); ?> <span id="placeholder-name"><?php echo $initial_placeholder_size; ?></span></h1>

  <div id="placeholder-parameters">

    <div style="float: left; display: inline-block; margin: 10px 0;">
      <?php _e ('Size', 'ad-inserter'); ?>
      <select id="placeholder-size" style="width: 80px; margin-right: 10px;" tabindex="1">

<?php
  foreach ($standard_placeholders as $index => $standard_placeholder) {
?>
        <option value="<?php echo $index; ?>" <?php echo $index == $placeholder_selection ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $standard_placeholder; ?></option>
<?php
  }
?>

      </select>
    </div>

    <div class="custom-placeholder-parameters custom-parameters-right" style="<?php echo $placeholder_selection == 0 ? '' : 'display: none;'; ?>">
      <div class="float-right">
        <?php _e ('Background color', 'ad-inserter'); ?>
        <input id="background" type="text" value="<?php echo $initial_placeholder_background_color; ?>" size="7" maxlength="7" tabindex="4" />
      </div>

      <div class="float-right">
        <?php _e ('Height', 'ad-inserter'); ?>
        <input id="height" type="text" value="<?php echo $initial_placeholder_height; ?>" size="4" maxlength="4" tabindex="3" />
      </div>

      <div class="float-right">
        <?php _e ('Width', 'ad-inserter'); ?>
        <input id="width" type="text" value="<?php echo $initial_placeholder_width; ?>" size="4" maxlength="4" tabindex="2" />
      </div>
    </div>

    <div style="clear: both;"></div>

    <div class="custom-placeholder-parameters" style="margin: 10px 0;<?php echo $placeholder_selection == 0 ? '' : ' display: none;'; ?>">
      <div  class="float-left">
        <?php _e ('Text', 'ad-inserter'); ?>
        <input id="text" style="width: 215px;" type="text" value="<?php echo $initial_placeholder_text; ?>" size="30" maxlength="40" tabindex="5" />
      </div>

      <div class="float-right">
        <?php _e ('Text color', 'ad-inserter'); ?>
        <input id="text-color" type="text" value="<?php echo $initial_placeholder_text_color; ?>" size="7" maxlength="7" tabindex="6" />
      </div>
    </div>
  </div>

  </div>
  <div style="clear: both;"></div>

  <p id="p1"><?php _e ('Here you can create a universal placeholder image that can be used in place of ads when they are not available yet.
Placeholder images created here will behave as any other image. You can also save them to local computer or server.', 'ad-inserter'); ?></p>

  <img id="placeholder" src="<?php echo $initial_placeholder_url; ?>" />

  <p id="p2"><?php _e ('Choose between common ad sizes 300x250, 336x280, 728x90, 468x60, 250x250, 300x600 or define custom size.
Default placeholders are gray with size as placeholder text but you can use any color or text you want. Click on <strong>Edit</strong> button to edit placeholder size, color or text.
You can also create blank solid color rectangles by clearing placeholder text.', 'ad-inserter'); ?></p>

  <p id="p3"><?php _e ('<strong>Please note</strong>: if you have active rotation editor the code window shows only the code for the currently selected option.
Therefore, code generator will in such case import or generate code for the currently selected option.', 'ad-inserter'); ?></p>

  <p id="p4"><?php _e ('Code generator for banners and AdSense generates the code only when you click on the button Generate code.
It is a tool that can help you to create code for AdSense or banners with links. So if you are using rotation editor and switch between options, you need to (optionally) import and generate code for each rotation option.', 'ad-inserter'); ?></p>

  <p id="p5"><?php _e ('Ad Inserter has a simple code generator for banners and placeholders.
You can select banner image (or placeholder), optionally define link (web page address that will open when the banner will be clicked) and select whether to open link in a new tab.', 'ad-inserter'); ?></p>
</body>
</html>
<?php
}

