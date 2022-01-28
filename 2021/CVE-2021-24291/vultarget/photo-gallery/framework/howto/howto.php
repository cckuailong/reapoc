<?php
require_once BWG()->plugin_dir . '/framework/howto/data.php';
wp_print_scripts('jquery-ui-tabs');
?>
<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet">
<script>
  function wd_how_to_use() {
    jQuery('#wd_howto_container').toggleClass('hidden');
    jQuery('body').toggleClass('wd-howto-disable-scroll');
  }
  function wd_toggle_mobile_menu() {
    jQuery('.wd-howto-menu').toggleClass('wd-howto-menu-opened');
    jQuery('.wd-howto-menu-overlay').toggleClass('hidden');
  }
  jQuery(function() {
    jQuery('#wd_howto_wrap').tabs({
      activate: function() {
        if (jQuery('#wd_howto_wrap .wd-howto-menu').hasClass('wd-howto-menu-opened')) {
          wd_toggle_mobile_menu();
        }
      }
    });
    jQuery(document).keyup(function(e) {
      if ( e.key == 'Escape' && !jQuery('#wd_howto_container').hasClass('hidden') ) {
        wd_how_to_use();
      }
    });
  });
  jQuery(window).on('load', function () {
    wd_howto_src_change();
  });
  function wd_howto_src_change() {
      jQuery('.wd-howto-screenshot').each(function () {
        var that = jQuery(this);
        var src = that.attr('data-src');
        jQuery('<img src="' + src + '" />').on('load', function () {
          that.attr('src', src).removeClass('wd-howto-loading-image');
        });
      });
  }
</script>
<style>
  .wd-howto-disable-scroll {
    overflow: hidden;
  }
  #wd_howto_container * {
    box-sizing: border-box;
	font-size: 14px;
  }
  #wd_howto_container {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: #000000b3;
    z-index: 100500;
  }
  #wd_howto_container .wd-howto-overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
  }
  #wd_howto_container .wd-howto-wrap {
    background: #fff;
    font-size: 0;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    margin: auto;
    right: 0;
    left: 0;
    max-width: 955px;
    max-height: 600px;
  }
  #wd_howto_container .wd-howto-menu {
    display: inline-block;
    font-size: initial;
    background: #FBFCFD;
    border: #0000000d 1px solid;
    box-shadow: 0 1px 6px #0000000d;
    max-width: 310px;
    width: 33%;
    height: 100%;
    vertical-align: top;
    padding: 20px;
  }
  #wd_howto_container .wd-howto-content {
    display: inline-block;
    font-size: initial;
    background: #FFFFFF;
    border: #0000000d 1px solid;
    box-shadow: 0 1px 6px #0000000d;
    width: 67%;
    height: 100%;
    vertical-align: top;
    padding: 20px;
    overflow-x: hidden;
    overflow-y: scroll;
  }
  #wd_howto_container .wd-howto-title {
    color: #323A45;
    font-family: Ubuntu;
    font-size: 22px;
    font-weight: 500;
  }
  #wd_howto_container ul {
    list-style: none;
  }
  #wd_howto_container li {
    color: #323A45;
    font-family: Ubuntu;
    font-size: 14px;
    font-weight: 500;
    line-height: 28px;
  }
  #wd_howto_container li a {
    color: inherit;
    text-decoration: none;
  }
  #wd_howto_container li:focus,
  #wd_howto_container li a:focus {
    box-shadow: none;
    outline-width: 0;
  }
  #wd_howto_container li.ui-tabs-active {
    color: #2160B5;
  }
  #wd_howto_container .wd-howto-divider-horizontal {
    border: #0000000d 1px solid;
    margin-top: 20px;
    width: 100%;
  }
  #wd_howto_container .wd-howto-divider-vertical {
    border: #0000000d 1px solid;
    width: 0;
    height: calc(100% - 30px);
    margin: 10px 15px;
  }
  #wd_howto_container .wd-howto-content .wd-howto-description {
    color: #323A45;
    font-family: Ubuntu;
    font-size: 12px;
    font-weight: 300;
    line-height: 18px;
  }
  #wd_howto_container .wh-howto-attention {
    color: #FD3C31;
  }
  #wd_howto_container .wd-howto-numeration {
    color: #FFFFFF;
    display: inline-block;
    background: #29B311;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    line-height: 30px;
    font-family: Ubuntu;
    font-size: 15px;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
  }
  #wd_howto_container .wd-howto-content-wrap {
    margin-top: 20px;
    position: relative;
  }
  #wd_howto_container .wd-howto-content-title {
    display: inline-block;
    color: #323A45;
    line-height: 18px;
    font-family: Ubuntu;
    font-size: 16px;
    font-weight: 400;
    vertical-align: middle;
  }
  #wd_howto_container .wd-howto-content-content {
    color: #323A45;
    line-height: 18px;
    font-family: Ubuntu;
    font-size: 16px;
    font-weight: 300;
    margin-top: 10px;
  }
  #wd_howto_container .wd-howto-screenshot {
    margin: 20px 0;
    width: 100%;
  }
  #wd_howto_container .wd-howto-mobile {
    display: none;
  }
  #wd_howto_container .wd-howto-content-left,
  #wd_howto_container .wd-howto-content-right {
    display: inline-block;
    vertical-align: top;
  }
  #wd_howto_container .wd-howto-content-left {
    width: 40px;
    height: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
  }
  #wd_howto_container .wd-howto-content-right {
    padding-left: 40px;
    width: calc(100% - 40px);
    height: 100%;
  }
  #wd_howto_container .dashicons-no-alt {
    color: #0083B8;
    position: absolute;
    top: 0;
    right: -20px;
    font-size: 30px;
    cursor: pointer;
  }
  #wd_howto_container .wd-howto-loading-image {
    width: 50px;
  }
  @media (max-width:1000px) {
    #wd_howto_container .dashicons-no-alt {
      color: #414852;
      position: absolute;
      top: 18px;
      right: 20px;
      font-size: 25px;
      cursor: pointer;
    }
  }
  @media (max-width:768px) {
    #wd_howto_container .wd-howto-content {
      padding: 16px;
    }
    #wd_howto_container .wd-howto-divider-horizontal {
      display: none;
    }
    #wd_howto_container .wd-howto-mobile .wd-howto-divider-horizontal {
      width: 768px;
      margin-left: -16px;
      display: block;
    }
    #wd_howto_container .wd-howto-divider-vertical {
      height: calc(100% - 10px);
      margin: 5px 9px;
    }
    #wd_howto_container .wd-howto-menu {
      left: -310px;
      position: absolute;
      width: 310px;
      z-index: 100501;
      transition: left 0.5s;
    }
    #wd_howto_container .wd-howto-menu-opened {
      left: 0;
    }
    #wd_howto_container .wd-howto-content {
      width: 100%;
    }
    #wd_howto_container .wd-howto-wrap {
      width: 100%;
      height: 100%;
      max-width: none;
      max-height: none;
    }
    #wd_howto_container .wd-howto-mobile {
      display: initial;
      position: absolute;
      top: 0;
      padding: 16px 0 0 0;
      background-color: #FFFFFF;
      z-index: 100500;
    }
    #wd_howto_container .wd-howto-content {
      padding-top: 60px;
    }
    #wd_howto_container .wd-howto-desktop {
      display: none;
    }
    #wd_howto_container .dashicons-menu {
      color: #414852;
      cursor: pointer;
    }
    #wd_howto_container .wd-howto-title {
      font-size: 16px;
      line-height: 20px;
    }
    #wd_howto_container .wd-howto-title-desktop {
      margin-top: 14px;
      font-size: 14px;
    }
    #wd_howto_container .wd-howto-content-title {
      font-size: 14px;
    }
    #wd_howto_container .wd-howto-content-content {
      font-size: 12px;
    }
    #wd_howto_container .wd-howto-numeration {
      width: 20px;
      height: 20px;
      font-size: 12px;
      line-height: 20px;
    }
    span.wd-howto-title {
      padding-left: 14px;
    }
    #wd_howto_container li {
      font-size: 12px;
    }
    #wd_howto_container .wd-howto-menu-overlay {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      z-index: 100500;
    }
    #wd_howto_container .dashicons-no-alt {
      color: #414852;
      position: fixed;
      top: 16px;
      right: 16px;
      font-size: 25px;
      cursor: pointer;
    }
  }
  .bwg-howto-button:last-child {
    float: right;
  }
</style>
<div id="wd_howto_container" class="hidden">
  <div class="wd-howto-overlay" onclick="wd_how_to_use(); return false;"></div>
  <div id="wd_howto_wrap" class="wd-howto-wrap">
    <div class="wd-howto-menu-overlay hidden" onclick="wd_toggle_mobile_menu()"></div>
    <div class="wd-howto-menu">
      <div class="wd-howto-title"><?php _e('How to ...', BWG()->prefix); ?></div>
      <ul>
        <?php
        foreach ($howto as $item => $value) {
          ?>
          <li><a href="#wd-howto-<?php echo $item; ?>"><?php echo $value['title']; ?></a></li>
          <?php
        }
        ?>
      </ul>
    </div>
    <div class="wd-howto-content">
      <?php
      foreach ($howto as $item => $value) {
        ?>
        <div id="wd-howto-<?php echo $item; ?>">
          <div class="wd-howto-title wd-howto-mobile">
            <span class="dashicons dashicons-menu" onclick="wd_toggle_mobile_menu()"></span>
            <span class="wd-howto-title"><?php _e('How to ...', BWG()->prefix); ?></span>
            <span class="dashicons dashicons-no-alt" onclick="wd_how_to_use(); return false;"></span>
            <div class="wd-howto-divider-horizontal"></div>
          </div>
          <div class="wd-howto-title wd-howto-title-desktop"><?php echo $value['title']; ?></div>
          <span class="dashicons dashicons-no-alt" onclick="wd_how_to_use(); return false;"></span>
          <div class="wd-howto-divider-horizontal"></div>
          <?php
          if (isset($value[ 'description' ]) && $value[ 'description' ]) {
            ?>
            <p class="wd-howto-description"><?php echo $value[ 'description' ]; ?></p>
            <?php
          }
          ?>
          <?php
          foreach ($value['content'] as $i => $section) {
            ?>
            <div class="wd-howto-content-wrap">
              <div class="wd-howto-content-left">
                <div class="wd-howto-numeration"><?php echo $i; ?></div>
                <div class="wd-howto-divider-vertical"></div>
              </div>
              <div class="wd-howto-content-right">
                <div class="wd-howto-content-title">
                  <?php echo $section[ 'title' ]; ?>
                </div>
                <div class="wd-howto-content-content">
                  <?php echo $section[ 'content' ]; ?>
                </div>
                <?php
                if (isset($section[ 'screenshot' ]) && $section[ 'screenshot' ]) {
                  ?>
                  <img class="wd-howto-screenshot wd-howto-loading-image" src="<?php echo BWG()->plugin_url ?>/images/ajax_loader.png" data-src="<?php echo $section[ 'screenshot' ]; ?>" alt="" />
                  <?php
                }
                if (isset($section[ 'function' ]) && $section[ 'function' ]) {
                  $section[ 'function' ]($params);
                }
                ?>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
</div>
<button type="button" class="tw-button-secondary button-large bwg-howto-button" onclick="wd_how_to_use(); return false;">
  <?php _e('How to use', BWG()->prefix); ?>
</button>
<?php
