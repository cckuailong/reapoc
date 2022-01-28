<?php
class BWGViewGalleryBox {

  private $model;

  public function __construct($model) {
    $this->model = $model;
  }

  public function display() {
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');

    $bwg = WDWLibrary::get('current_view', 0, 'intval');
    $current_url =  WDWLibrary::get('current_url', '', 'esc_url');
    $theme_id = WDWLibrary::get('theme_id', 0, 'intval');
    $current_image_id = WDWLibrary::esc_script('get', 'image_id', 0, 'int');
    $gallery_id = WDWLibrary::esc_script('get', 'gallery_id', 0, 'int');
    $tag = WDWLibrary::get('tag', 0, 'intval');

    $shortcode_id = WDWLibrary::get('shortcode_id', 0, 'intval');
    global $wpdb;
    $shortcode = $wpdb->get_var($wpdb->prepare("SELECT tagtext FROM " . $wpdb->prefix . "bwg_shortcode WHERE id='%d'", $shortcode_id));
    $data = array();
    if ( $shortcode ) {
      $shortcode_params = explode('" ', $shortcode);
      foreach ( $shortcode_params as $shortcode_param ) {
        $shortcode_param = str_replace('"', '', $shortcode_param);
        $shortcode_elem = explode('=', $shortcode_param);
        $data[str_replace(' ', '', $shortcode_elem[0])] = $shortcode_elem[1];
      }
    }

    $params = WDWLibrary::get_shortcode_option_params( $data );
    $params['sort_by'] = WDWLibrary::esc_script('get', 'sort_by', 'RAND()');
    $params['order_by'] = WDWLibrary::esc_script('get', 'order_by', 'asc');
    $params['watermark_position'] = explode('-', $params['watermark_position']);

    if ( !BWG()->is_pro ) {
      $params['popup_enable_filmstrip'] = FALSE;
      $params['open_comment'] = FALSE;
      $params['popup_enable_comment'] = FALSE;
      $params['popup_enable_facebook'] = FALSE;
      $params['popup_enable_twitter'] = FALSE;
      $params['popup_enable_ecommerce'] = FALSE;
      $params['popup_enable_pinterest'] = FALSE;
      $params['popup_enable_tumblr'] = FALSE;
      $params['popup_enable_email'] = FALSE;
      $params['popup_enable_captcha'] = FALSE;
      $params['gdpr_compliance'] = FALSE;
      $params['comment_moderation'] = FALSE;
      $params['enable_addthis'] = FALSE;
      $params['addthis_profile_id'] = FALSE;
    }

    $image_right_click =  isset(BWG()->options->image_right_click) ? BWG()->options->image_right_click : 0;

    require_once BWG()->plugin_dir . "/frontend/models/model.php";
	  $model_site = new BWGModelSite();
    $theme_row = $model_site->get_theme_row_data($theme_id);

    $filmstrip_direction = 'horizontal';
    if ($theme_row->lightbox_filmstrip_pos == 'right' || $theme_row->lightbox_filmstrip_pos == 'left') {
      $filmstrip_direction = 'vertical';
    }
	  $image_filmstrip_height = 0;
    $image_filmstrip_width = 0;
    if ( $params['popup_enable_filmstrip'] ) {
      if ( $filmstrip_direction == 'horizontal' ) {
        $image_filmstrip_height = (isset($params['popup_filmstrip_height']) ? (int) $params['popup_filmstrip_height'] : 20);
        $thumb_ratio = $params['thumb_width'] / $params['thumb_height'];
        $image_filmstrip_width = round($thumb_ratio * $image_filmstrip_height);
      }
      else {
        $image_filmstrip_width = (isset($params['popup_filmstrip_height']) ? (int) $params['popup_filmstrip_height'] : 50);
        $thumb_ratio = $params['thumb_height'] / $params['thumb_width'];
        $image_filmstrip_height = round($thumb_ratio * $image_filmstrip_width);
      }
    }
    $image_rows = $this->model->get_image_rows_data($gallery_id, $bwg, $params['sort_by'], $params['order_by'], $tag);
    $image_id = WDWLibrary::get('image_id', $current_image_id, 'intval', 'POST');
    $pricelist_id = 0;
    if ( function_exists('BWGEC') && $params['popup_enable_ecommerce'] == 1 ) {
      $image_pricelist = $this->model->get_image_pricelist($image_id);
      $pricelist_id = $image_pricelist ? $image_pricelist : 0;
    }
    $pricelist_data = $this->model->get_image_pricelists($pricelist_id);

    $params_array = array(
      'action' => 'GalleryBox',
      'image_id' => $image_id,
      'gallery_id' => $gallery_id,
      'tag' => $tag,
      'theme_id' => $theme_id,
    );
    if ($params['watermark_type'] != 'none') {
      $params_array['watermark_link'] = $params['watermark_link'];
      $params_array['watermark_opacity'] = $params['watermark_opacity'];
      $params_array['watermark_position'] = $params['watermark_position'];
    }
    if ($params['watermark_type'] == 'text') {
      $params_array['watermark_text'] = $params['watermark_text'];
      $params_array['watermark_font_size'] = $params['watermark_font_size'];
      $params_array['watermark_font'] = $params['watermark_font'];
      $params_array['watermark_color'] = $params['watermark_color'];
    }
    elseif ($params['watermark_type'] == 'image') {
      $params_array['watermark_url'] = $params['watermark_url'];
      $params_array['watermark_width'] = $params['watermark_width'];
      $params_array['watermark_height'] = $params['watermark_height'];
    }

    $popup_url = add_query_arg(array($params_array), admin_url('admin-ajax.php'));

    $filmstrip_thumb_margin = trim($theme_row->lightbox_filmstrip_thumb_margin);

    $margins_split = explode(" ", $filmstrip_thumb_margin);
    $filmstrip_thumb_margin_top = 0;
    $filmstrip_thumb_margin_right = 0;
    $filmstrip_thumb_margin_bottom = 0;
    $filmstrip_thumb_margin_left = 0;
    if ( count($margins_split) == 1 ) {
      $filmstrip_thumb_margin_top = (int) $margins_split[0];
      $filmstrip_thumb_margin_right = (int) $margins_split[0];
      $filmstrip_thumb_margin_bottom = (int) $margins_split[0];
      $filmstrip_thumb_margin_left = (int) $margins_split[0];
    }
    if ( count($margins_split) == 2 ) {
      $filmstrip_thumb_margin_top = (int) $margins_split[0];
      $filmstrip_thumb_margin_right = (int) $margins_split[1];
      $filmstrip_thumb_margin_bottom = (int) $margins_split[0];
      $filmstrip_thumb_margin_left = (int) $margins_split[1];
    }
    if ( count($margins_split) == 3 ) {
      $filmstrip_thumb_margin_top = (int) $margins_split[0];
      $filmstrip_thumb_margin_right = (int) $margins_split[1];
      $filmstrip_thumb_margin_bottom = (int) $margins_split[2];
      $filmstrip_thumb_margin_left = (int) $margins_split[1];
    }
    if ( count($margins_split) == 4 ) {
      $filmstrip_thumb_margin_top = (int) $margins_split[0];
      $filmstrip_thumb_margin_right = (int) $margins_split[1];
      $filmstrip_thumb_margin_bottom = (int) $margins_split[2];
      $filmstrip_thumb_margin_left = (int) $margins_split[3];
    }
    $filmstrip_thumb_top_bottom_space =  $filmstrip_thumb_margin_top + $filmstrip_thumb_margin_bottom;
    $filmstrip_thumb_right_left_space =  $filmstrip_thumb_margin_right + $filmstrip_thumb_margin_left;
    $all_images_top_bottom_space = count($image_rows) * $filmstrip_thumb_top_bottom_space;
    $all_images_right_left_space = count($image_rows) * $filmstrip_thumb_right_left_space;
    $rgb_bwg_image_info_bg_color = WDWLibrary::spider_hex2rgb($theme_row->lightbox_info_bg_color);
    $rgb_bwg_image_hit_bg_color = WDWLibrary::spider_hex2rgb($theme_row->lightbox_hit_bg_color);
    $rgb_lightbox_ctrl_cont_bg_color = WDWLibrary::spider_hex2rgb($theme_row->lightbox_ctrl_cont_bg_color);
    if (!$params['popup_enable_filmstrip']) {
      if ($theme_row->lightbox_filmstrip_pos == 'left') {
        $theme_row->lightbox_filmstrip_pos = 'top';
      }
      if ($theme_row->lightbox_filmstrip_pos == 'right') {
        $theme_row->lightbox_filmstrip_pos = 'bottom';
      }
    }
    $left_or_top = 'left';
    $width_or_height= 'width';
    $outerWidth_or_outerHeight = 'outerWidth';
    if (!($filmstrip_direction == 'horizontal')) {
      $left_or_top = 'top';
      $width_or_height = 'height';
      $outerWidth_or_outerHeight = 'outerHeight';
    }
    $lightbox_bg_transparent = (isset($theme_row->lightbox_bg_transparent)) ? $theme_row->lightbox_bg_transparent : 100;
    $lightbox_bg_color = WDWLibrary::spider_hex2rgb($theme_row->lightbox_bg_color);

    if (BWG()->is_pro && $params['enable_addthis'] && $params['addthis_profile_id']) {
      ?>
      <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $params['addthis_profile_id']; ?>" async="async"></script>
      <?php
    }
    ?>
    <style>
      .spider_popup_wrap .bwg-loading {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo number_format($theme_row->lightbox_overlay_bg_transparent / 100, 2, ".", ""); ?>;
      }
      .bwg_inst_play {
        background-image: url('<?php echo BWG()->plugin_url . '/images/play.png'; ?>');
      }
      .bwg_inst_play:hover {
          background: url(<?php echo BWG()->plugin_url . '/images/play_hover.png'; ?>) no-repeat;
      }
      .spider_popup_wrap {
        background-color: rgba(<?php echo $lightbox_bg_color['red']; ?>, <?php echo $lightbox_bg_color['green']; ?>, <?php echo $lightbox_bg_color['blue']; ?>, <?php echo number_format($lightbox_bg_transparent/ 100, 2, ".", ""); ?>);
      }
      .bwg_popup_image {
        max-width: <?php echo $params['popup_width'] - ($filmstrip_direction == 'vertical' ? $image_filmstrip_width : 0); ?>px;
        max-height: <?php echo $params['popup_height'] - ($filmstrip_direction == 'horizontal' ? $image_filmstrip_height : 0); ?>px;
      }
      .bwg_ctrl_btn {
        color: #<?php echo $theme_row->lightbox_ctrl_btn_color; ?>;
        font-size: <?php echo $theme_row->lightbox_ctrl_btn_height; ?>px;
        margin: <?php echo $theme_row->lightbox_ctrl_btn_margin_top; ?>px <?php echo $theme_row->lightbox_ctrl_btn_margin_left; ?>px;
        opacity: <?php echo number_format($theme_row->lightbox_ctrl_btn_transparent / 100, 2, ".", ""); ?>;
      }
      .bwg_toggle_btn {
        color: #<?php echo $theme_row->lightbox_ctrl_btn_color; ?>;
        font-size: <?php echo $theme_row->lightbox_toggle_btn_height; ?>px;
        opacity: <?php echo number_format($theme_row->lightbox_ctrl_btn_transparent / 100, 2, ".", ""); ?>;
      }
      .bwg_ctrl_btn_container {
        background-color: rgba(<?php echo $rgb_lightbox_ctrl_cont_bg_color['red']; ?>, <?php echo $rgb_lightbox_ctrl_cont_bg_color['green']; ?>, <?php echo $rgb_lightbox_ctrl_cont_bg_color['blue']; ?>, <?php echo number_format($theme_row->lightbox_ctrl_cont_transparent / 100, 2, ".", ""); ?>);
        /*background: none repeat scroll 0 0 #<?php echo $theme_row->lightbox_ctrl_cont_bg_color; ?>;*/
        <?php
        if ($theme_row->lightbox_ctrl_btn_pos == 'top') {
          ?>
          border-bottom-left-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          border-bottom-right-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          <?php
        }
        else {
          ?>
          bottom: 0;
          border-top-left-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          border-top-right-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          <?php
        }?>
        /*height: <?php /*echo $theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top;*/ ?>px;*/
        text-align: <?php echo $theme_row->lightbox_ctrl_btn_align; ?>;
      }
      .bwg_toggle_container {
        background: none repeat scroll 0 0 #<?php echo $theme_row->lightbox_ctrl_cont_bg_color; ?>;
        <?php
        if ($theme_row->lightbox_ctrl_btn_pos == 'top') {
          ?>
          border-bottom-left-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          border-bottom-right-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          /*top: <?php echo $theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top; ?>px;*/
          <?php
        }
        else {
          ?>
          border-top-left-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          border-top-right-radius: <?php echo $theme_row->lightbox_ctrl_cont_border_radius; ?>px;
          /*bottom: <?php echo $theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top; ?>px;*/
          <?php
        }?>
        margin-left: -<?php echo $theme_row->lightbox_toggle_btn_width / 2; ?>px;
        opacity: <?php echo number_format($theme_row->lightbox_ctrl_cont_transparent / 100, 2, ".", ""); ?>;
        width: <?php echo $theme_row->lightbox_toggle_btn_width; ?>px;
      }
      .bwg_close_btn {
        opacity: <?php echo number_format($theme_row->lightbox_close_btn_transparent / 100, 2, ".", ""); ?>;
      }
      .spider_popup_close {
        background-color: #<?php echo $theme_row->lightbox_close_btn_bg_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_close_btn_border_radius; ?>;
        border: <?php echo $theme_row->lightbox_close_btn_border_width; ?>px <?php echo $theme_row->lightbox_close_btn_border_style; ?> #<?php echo $theme_row->lightbox_close_btn_border_color; ?>;
        box-shadow: <?php echo $theme_row->lightbox_close_btn_box_shadow; ?>;
        color: #<?php echo $theme_row->lightbox_close_btn_color; ?>;
        height: <?php echo $theme_row->lightbox_close_btn_height; ?>px;
        font-size: <?php echo $theme_row->lightbox_close_btn_size; ?>px;
        right: <?php echo $theme_row->lightbox_close_btn_right; ?>px;
        top: <?php echo $theme_row->lightbox_close_btn_top; ?>px;
        width: <?php echo $theme_row->lightbox_close_btn_width; ?>px;
      }
      .spider_popup_close_fullscreen {
        color: #<?php echo $theme_row->lightbox_close_btn_full_color; ?>;
        font-size: <?php echo $theme_row->lightbox_close_btn_size; ?>px;
        right: 7px;
      }
      #spider_popup_left-ico,
      #spider_popup_right-ico {
        background-color: #<?php echo $theme_row->lightbox_rl_btn_bg_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_rl_btn_border_radius; ?>;
        border: <?php echo $theme_row->lightbox_rl_btn_border_width; ?>px <?php echo $theme_row->lightbox_rl_btn_border_style; ?> #<?php echo $theme_row->lightbox_rl_btn_border_color; ?>;
        box-shadow: <?php echo $theme_row->lightbox_rl_btn_box_shadow; ?>;
        color: #<?php echo $theme_row->lightbox_rl_btn_color; ?>;
        height: <?php echo $theme_row->lightbox_rl_btn_height; ?>px;
        font-size: <?php echo $theme_row->lightbox_rl_btn_size; ?>px;
        width: <?php echo $theme_row->lightbox_rl_btn_width; ?>px;
        opacity: <?php echo number_format($theme_row->lightbox_rl_btn_transparent / 100, 2, ".", ""); ?>;
      }
      #spider_popup_left-ico {
        padding-right: <?php echo ($theme_row->lightbox_rl_btn_width - $theme_row->lightbox_rl_btn_size) / 3; ?>px;
      }
      #spider_popup_right-ico {
        padding-left: <?php echo ($theme_row->lightbox_rl_btn_width - $theme_row->lightbox_rl_btn_size) / 3; ?>px;
      }
      <?php
      if($params['autohide_lightbox_navigation']){?>
      #spider_popup_left-ico{
        left: -9999px;
      }
      #spider_popup_right-ico{
        left: -9999px;
      }
      <?php }
      else { ?>
        #spider_popup_left-ico {
        left: 20px;
        }
        #spider_popup_right-ico {
          left: auto;
          right: 20px;
        }
      <?php } ?>
      .bwg_ctrl_btn:hover,
      .bwg_toggle_btn:hover,
      .spider_popup_close:hover,
      .spider_popup_close_fullscreen:hover,
      #spider_popup_left:hover #spider_popup_left-ico,
      #spider_popup_right:hover #spider_popup_right-ico {
        color: #<?php echo $theme_row->lightbox_close_rl_btn_hover_color; ?>;
        cursor: pointer;
      }
      .bwg_comment_container,  .bwg_ecommerce_container {
        background-color: #<?php echo $theme_row->lightbox_comment_bg_color; ?>;
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
        font-size: <?php echo $theme_row->lightbox_comment_font_size; ?>px;
        font-family: <?php echo $theme_row->lightbox_comment_font_style; ?>;
        <?php echo $theme_row->lightbox_comment_pos; ?>: -<?php echo $theme_row->lightbox_comment_width; ?>px;
        width: <?php echo $theme_row->lightbox_comment_width; ?>px;
      }
        .bwg_ecommerce_body  p, .bwg_ecommerce_body span, .bwg_ecommerce_body div {
          color:#<?php echo $theme_row->lightbox_comment_font_color; ?>!important;
        }
        .pge_tabs li{
          float:left;
          border-top: 1px solid #<?php echo $theme_row->lightbox_bg_color; ?>!important;
          border-left: 1px solid #<?php echo $theme_row->lightbox_bg_color; ?>!important;
          border-right: 1px solid #<?php echo $theme_row->lightbox_bg_color; ?>!important;
          margin-right: 1px !important;
          border-radius: <?php echo $theme_row->lightbox_comment_button_border_radius; ?> <?php echo $theme_row->lightbox_comment_button_border_radius; ?> 0 0;
          position:relative;
        }
       .pge_tabs li a{
          color:#<?php echo $theme_row->lightbox_comment_bg_color; ?>!important;
        }

      .pge_tabs li.pge_active a, .pge_tabs li a:hover {
          border-radius: <?php echo $theme_row->lightbox_comment_button_border_radius; ?>;

        }
      .pge_tabs li.pge_active a>span, .pge_tabs li a>span:hover {
        color:#<?php echo $theme_row->lightbox_comment_button_bg_color; ?> !important;
        border-bottom: 1px solid #<?php echo $theme_row->lightbox_comment_button_bg_color; ?>;
        padding-bottom: 2px;
      }
       .pge_tabs_container{
          border:1px solid #<?php echo $theme_row->lightbox_comment_font_color; ?>;
          border-radius: 0 0 <?php echo $theme_row->lightbox_comment_button_border_radius; ?> <?php echo $theme_row->lightbox_comment_button_border_radius; ?>;
       }

      .pge_pricelist {
        padding:0 !important;
        color:#<?php echo $theme_row->lightbox_comment_font_color; ?>!important;
      }

      .pge_add_to_cart a{
        border: 1px solid #<?php echo $theme_row->lightbox_comment_font_color; ?>!important;
        color:#<?php echo $theme_row->lightbox_comment_font_color; ?>!important;
        border-radius: <?php echo $theme_row->lightbox_comment_button_border_radius; ?>;
      }
      .bwg_comments , .bwg_ecommerce_panel{
        font-size: <?php echo $theme_row->lightbox_comment_font_size; ?>px;
        font-family: <?php echo $theme_row->lightbox_comment_font_style; ?>;
      }
      .bwg_comments input[type="submit"], .bwg_ecommerce_panel input[type="button"] {
        background: none repeat scroll 0 0 #<?php echo $theme_row->lightbox_comment_button_bg_color; ?>;
        border: <?php echo $theme_row->lightbox_comment_button_border_width; ?>px <?php echo $theme_row->lightbox_comment_button_border_style; ?> #<?php echo $theme_row->lightbox_comment_button_border_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_comment_button_border_radius; ?>;
        color: #<?php echo $theme_row->lightbox_comment_bg_color; ?>;
        padding: <?php echo $theme_row->lightbox_comment_button_padding; ?>;
      }
      .bwg_comments .bwg-submit-disabled:hover {
          padding: <?php echo $theme_row->lightbox_comment_button_padding; ?> !important;
          border-radius: <?php echo $theme_row->lightbox_comment_button_border_radius; ?> !important;
      }
      .bwg_comments input[type="text"],
      .bwg_comments textarea,
      .bwg_ecommerce_panel input[type="text"],
      .bwg_ecommerce_panel input[type="number"],
      .bwg_ecommerce_panel textarea , .bwg_ecommerce_panel select {
        background: none repeat scroll 0 0 #<?php echo $theme_row->lightbox_comment_input_bg_color; ?>;
        border: <?php echo $theme_row->lightbox_comment_input_border_width; ?>px <?php echo $theme_row->lightbox_comment_input_border_style; ?> #<?php echo $theme_row->lightbox_comment_input_border_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_comment_input_border_radius; ?>;
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
        font-size: 12px;
        padding: <?php echo $theme_row->lightbox_comment_input_padding; ?>;
        width: 100%;
      }
      .bwg_comment_header_p {
        border-top: <?php echo $theme_row->lightbox_comment_separator_width; ?>px <?php echo $theme_row->lightbox_comment_separator_style; ?> #<?php echo $theme_row->lightbox_comment_separator_color; ?>;
      }
      .bwg_comment_header {
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
        font-size: <?php echo $theme_row->lightbox_comment_author_font_size; ?>px;
      }
      .bwg_comment_date {
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
        float: right;
        font-size: <?php echo $theme_row->lightbox_comment_date_font_size; ?>px;
      }
      .bwg_comment_body {
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
        font-size: <?php echo $theme_row->lightbox_comment_body_font_size; ?>px;
      }
      .bwg_comments_close , .bwg_ecommerce_close{
        text-align: <?php echo (($theme_row->lightbox_comment_pos == 'left') ? 'right' : 'left'); ?>!important;
      }
      #bwg_rate_form .bwg_rate:hover {
        color: #<?php echo $theme_row->lightbox_rate_color; ?>;
      }
      .bwg_facebook,
      .bwg_twitter,
      .bwg_pinterest,
      .bwg_tumblr {
        color: #<?php echo $theme_row->lightbox_comment_share_button_color; ?>;
      }
      .bwg_image_container {
        <?php echo $theme_row->lightbox_filmstrip_pos; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $image_filmstrip_height : $image_filmstrip_width); ?>px;
      }
      .bwg_filmstrip_container {
        display: <?php echo ($filmstrip_direction == 'horizontal'? 'table' : 'block'); ?>;
        height: <?php echo ($filmstrip_direction == 'horizontal'? $image_filmstrip_height : $params['popup_height']); ?>px;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? $params['popup_width'] : $image_filmstrip_width); ?>px;
        <?php echo $theme_row->lightbox_filmstrip_pos; ?>: 0;
      }
      .bwg_filmstrip {
        <?php echo $left_or_top; ?>: <?php echo $theme_row->lightbox_filmstrip_rl_btn_size; ?>px;
        <?php echo $width_or_height; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $params['popup_width'] - 40 : $params['popup_height'] - 40); ?>px;
      }
      .bwg_filmstrip_thumbnails {
        height: <?php echo ($filmstrip_direction == 'horizontal' ? $image_filmstrip_height : ($image_filmstrip_height + $filmstrip_thumb_right_left_space) * count($image_rows)); ?>px;
        <?php echo $left_or_top; ?>: 0px;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? ($image_filmstrip_width + $filmstrip_thumb_right_left_space) * count($image_rows) : $image_filmstrip_width); ?>px;
      }
      .bwg_filmstrip_thumbnail {
        height: <?php echo $image_filmstrip_height; ?>px;
        width: <?php echo $image_filmstrip_width; ?>px;
        padding: <?php echo $theme_row->lightbox_filmstrip_thumb_margin; ?>;
      }
      .bwg_filmstrip_thumbnail .bwg_filmstrip_thumbnail_img_wrap {
        width:<?php echo $image_filmstrip_width - $filmstrip_thumb_right_left_space ?>px;
        height:<?php echo $image_filmstrip_height - $filmstrip_thumb_top_bottom_space;?>px;
        border: <?php echo $theme_row->lightbox_filmstrip_thumb_border_width; ?>px <?php echo $theme_row->lightbox_filmstrip_thumb_border_style; ?> #<?php echo $theme_row->lightbox_filmstrip_thumb_border_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_filmstrip_thumb_border_radius; ?>;
      }
      .bwg_thumb_active .bwg_filmstrip_thumbnail_img_wrap {
        border: <?php echo $theme_row->lightbox_filmstrip_thumb_active_border_width; ?>px solid #<?php echo $theme_row->lightbox_filmstrip_thumb_active_border_color; ?>;
      }
      .bwg_thumb_deactive {
        opacity: <?php echo number_format($theme_row->lightbox_filmstrip_thumb_deactive_transparent / 100, 2, ".", ""); ?>;
      }
      .bwg_filmstrip_left {
        background-color: #<?php echo $theme_row->lightbox_filmstrip_rl_bg_color; ?>;
        display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
        z-index: 99999;
        <?php echo $width_or_height; ?>: <?php echo $theme_row->lightbox_filmstrip_rl_btn_size; ?>px;
        <?php echo $left_or_top; ?>: 0;
        <?php echo ($filmstrip_direction == 'horizontal' ? 'position: relative;' : 'position: absolute;') ?>
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?>
      }
      .bwg_filmstrip_right {
        background-color: #<?php echo $theme_row->lightbox_filmstrip_rl_bg_color; ?>;
        <?php echo($filmstrip_direction == 'horizontal' ? 'right' : 'bottom') ?>: 0;
        z-index: 99999;
        <?php echo $width_or_height; ?>: <?php echo $theme_row->lightbox_filmstrip_rl_btn_size; ?>px;
        display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
        <?php echo ($filmstrip_direction == 'horizontal' ? 'position: relative;' : 'position: absolute;') ?>
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?>
      }
      .bwg_filmstrip_left i,
      .bwg_filmstrip_right i {
        color: #<?php echo $theme_row->lightbox_filmstrip_rl_btn_color; ?>;
        font-size: <?php echo $theme_row->lightbox_filmstrip_rl_btn_size; ?>px;
      }
      .bwg_watermark_spun {
        text-align: <?php echo $params['watermark_position'][1]; ?>;
        vertical-align: <?php echo $params['watermark_position'][0]; ?>;
        /*z-index: 10140;*/
      }
      .bwg_watermark_image {
        max-height: <?php echo $params['watermark_height']; ?>px;
        max-width: <?php echo $params['watermark_width']; ?>px;
        opacity: <?php echo number_format($params['watermark_opacity'] / 100, 2, ".", ""); ?>;
      }
      .bwg_watermark_text,
      .bwg_watermark_text:hover {
        font-size: <?php echo $params['watermark_font_size']; ?>px;
        font-family: <?php echo $params['watermark_font']; ?>;
        color: #<?php echo $params['watermark_color']; ?> !important;
        opacity: <?php echo number_format($params['watermark_opacity'] / 100, 2, ".", ""); ?>;
      }
      .bwg_image_info_container1 {
        display: <?php echo $params['popup_info_always_show'] ? 'table-cell' : 'none'; ?>;
      }
      .bwg_image_hit_container1 {
        display: <?php echo $params['popup_hit_counter'] ? 'table-cell' : 'none'; ?>;;
      }
      .bwg_image_info_spun {
        text-align: <?php echo $theme_row->lightbox_info_align; ?>;
        vertical-align: <?php echo $theme_row->lightbox_info_pos; ?>;
      }
      .bwg_image_hit_spun {
        text-align: <?php echo $theme_row->lightbox_hit_align; ?>;
        vertical-align: <?php echo $theme_row->lightbox_hit_pos; ?>;
      }
      .bwg_image_hit {
        background: rgba(<?php echo $rgb_bwg_image_hit_bg_color['red']; ?>, <?php echo $rgb_bwg_image_hit_bg_color['green']; ?>, <?php echo $rgb_bwg_image_hit_bg_color['blue']; ?>, <?php echo number_format($theme_row->lightbox_hit_bg_transparent / 100, 2, ".", ""); ?>);
        border: <?php echo $theme_row->lightbox_hit_border_width; ?>px <?php echo $theme_row->lightbox_hit_border_style; ?> #<?php echo $theme_row->lightbox_hit_border_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_info_border_radius; ?>;
        <?php echo ($theme_row->lightbox_ctrl_btn_pos == 'bottom' && $theme_row->lightbox_hit_pos == 'bottom') ? 'bottom: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
        margin: <?php echo $theme_row->lightbox_hit_margin; ?>;
        padding: <?php echo $theme_row->lightbox_hit_padding; ?>;
        <?php echo ($theme_row->lightbox_ctrl_btn_pos == 'top' && $theme_row->lightbox_hit_pos == 'top') ? 'top: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
      }
      .bwg_image_hits,
      .bwg_image_hits * {
        color: #<?php echo $theme_row->lightbox_hit_color; ?> !important;
        font-family: <?php echo $theme_row->lightbox_hit_font_style; ?>;
        font-size: <?php echo $theme_row->lightbox_hit_font_size; ?>px;
        font-weight: <?php echo $theme_row->lightbox_hit_font_weight; ?>;
      }
      .bwg_image_info {
        background: rgba(<?php echo $rgb_bwg_image_info_bg_color['red']; ?>, <?php echo $rgb_bwg_image_info_bg_color['green']; ?>, <?php echo $rgb_bwg_image_info_bg_color['blue']; ?>, <?php echo number_format($theme_row->lightbox_info_bg_transparent / 100, 2, ".", ""); ?>);
        border: <?php echo $theme_row->lightbox_info_border_width; ?>px <?php echo $theme_row->lightbox_info_border_style; ?> #<?php echo $theme_row->lightbox_info_border_color; ?>;
        border-radius: <?php echo $theme_row->lightbox_info_border_radius; ?>;
        <?php echo ((!$params['popup_enable_filmstrip'] || $theme_row->lightbox_filmstrip_pos != 'bottom') && $theme_row->lightbox_ctrl_btn_pos == 'bottom' && $theme_row->lightbox_info_pos == 'bottom') ? 'bottom: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
        <?php if ($params['popup_info_full_width']) { ?>
        width: 100%;
        <?php } else { ?>
        width: 33%;
        margin: <?php echo $theme_row->lightbox_info_margin; ?>;
        <?php } ?>
        padding: <?php echo $theme_row->lightbox_info_padding; ?>;
        <?php echo ((!$params['popup_enable_filmstrip'] || $theme_row->lightbox_filmstrip_pos != 'top') && $theme_row->lightbox_ctrl_btn_pos == 'top' && $theme_row->lightbox_info_pos == 'top') ? 'top: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
        word-break : break-word;
      }
      .bwg_image_title,
      .bwg_image_title * {
        color: #<?php echo $theme_row->lightbox_title_color; ?> !important;
        font-family: <?php echo $theme_row->lightbox_title_font_style; ?>;
        font-size: <?php echo $theme_row->lightbox_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->lightbox_title_font_weight; ?>;
        word-wrap: break-word;
      }
      .bwg_image_description,
      .bwg_image_description * {
        color: #<?php echo $theme_row->lightbox_description_color; ?> !important;
        font-family: <?php echo $theme_row->lightbox_description_font_style; ?>;
        font-size: <?php echo $theme_row->lightbox_description_font_size; ?>px;
        font-weight: <?php echo $theme_row->lightbox_description_font_weight; ?>;
        word-break: break-word;
      }
      .bwg_image_rate_spun {
        text-align: <?php echo $theme_row->lightbox_rate_align; ?>;
        vertical-align: <?php echo $theme_row->lightbox_rate_pos; ?>;
      }
      .bwg_image_rate {
        <?php echo ($theme_row->lightbox_ctrl_btn_pos == 'bottom' && $theme_row->lightbox_rate_pos == 'bottom') ? 'bottom: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
        padding: <?php echo $theme_row->lightbox_rate_padding; ?>;
        <?php echo ($theme_row->lightbox_ctrl_btn_pos == 'top' && $theme_row->lightbox_rate_pos == 'top') ? 'top: ' . ($theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top) . 'px;' : '' ?>
      }
      #bwg_rate_form .bwg_hint,
      #bwg_rate_form .bwg-icon-<?php echo $theme_row->lightbox_rate_icon; ?>,
      #bwg_rate_form .bwg-icon-<?php echo $theme_row->lightbox_rate_icon; ?>-half-o,
      #bwg_rate_form .bwg-icon-<?php echo $theme_row->lightbox_rate_icon; ?>-o,
      #bwg_rate_form .bwg-icon-minus-square-o {
        color: #<?php echo $theme_row->lightbox_rate_color; ?>;
        font-size: <?php echo $theme_row->lightbox_rate_size; ?>px;
      }
      .bwg_rate_hover {
        color: #<?php echo $theme_row->lightbox_rate_hover_color; ?> !important;
      }
      .bwg_rated {
        color: #<?php echo $theme_row->lightbox_rate_color; ?>;
        display: none;
        font-size: <?php echo $theme_row->lightbox_rate_size - 2; ?>px;
      }
      #bwg_comment_form label {
        color: #<?php echo $theme_row->lightbox_comment_font_color; ?>;
      }
    </style>
    <?php
    $image_id_exist = FALSE;
    $has_embed = FALSE;
    $data = array();
    foreach ($image_rows as $key => $image_row) {
      if ($image_row->id == $image_id) {
        $current_avg_rating = $image_row->avg_rating;
        $current_rate = $image_row->rate;
        $current_rate_count = $image_row->rate_count;
        $current_image_key = $key;
      }
      if ($image_row->id == $current_image_id) {
        $current_image_alt = $image_row->alt;
        $current_image_hit_count = $image_row->hit_count;
        $current_image_description = str_replace(array("\r\n", "\n", "\r"), esc_html('<br />'), preg_replace('/[\x01-\x09\x0B-\x0C\x0E-\x1F]+/', '', $image_row->description));
        $current_image_url = $image_row->pure_image_url;
        $current_thumb_url = $image_row->pure_thumb_url;
        $current_filetype = $image_row->filetype;
        $image_id_exist = TRUE;
      }
      $has_embed = $has_embed || preg_match('/EMBED/',$image_row->filetype) == 1;
      if ( BWG()->is_pro ) {
        $current_pricelist_id = $this->model->get_image_pricelist($image_row->id);
        $current_pricelist_id = $current_pricelist_id ? $current_pricelist_id : 0;
        $_pricelist = $pricelist_data["pricelist"];
      }

      $data[$key] = array();
      $data[$key]["number"] = $key + 1;
      $data[$key]["id"] = $image_row->id;
      $data[$key]["alt"] = esc_html(str_replace(array("\r\n", "\n", "\r"), esc_html('<br />'), $image_row->alt));
      $data[$key]["description"] = esc_html(str_replace(array("\r\n", "\n", "\r"), esc_html('<br />'), preg_replace('/[\x01-\x09\x0B-\x0C\x0E-\x1F]+/', '', $image_row->description)));

      $image_resolution = explode(' x ', $image_row->resolution);
      if (is_array($image_resolution)) {
        $instagram_post_width = $image_resolution[0];
        if (isset($image_resolution[1])) {
          $instagram_post_height = explode(' ', $image_resolution[1]);
        }
        $instagram_post_height = $instagram_post_height[0];
      }

      $data[$key]["image_width"] = $instagram_post_width;
      $data[$key]["image_height"] = $instagram_post_height;
      $data[$key]["pure_image_url"] = $image_row->pure_image_url;
      $data[$key]["pure_thumb_url"] = $image_row->pure_thumb_url;
      $data[$key]["image_url"] = $image_row->image_url;
      $data[$key]["thumb_url"] = $image_row->thumb_url;
      $data[$key]["date"] = $image_row->date;
      $data[$key]["comment_count"] = $image_row->comment_count;
      $data[$key]["filetype"] = $image_row->filetype;
      $data[$key]["filename"] = $image_row->filename;
      $data[$key]["avg_rating"] = $image_row->avg_rating;
      $data[$key]["rate"] = $image_row->rate;
      $data[$key]["rate_count"] = $image_row->rate_count;
      $data[$key]["hit_count"] = $image_row->hit_count;
      if ( BWG()->is_pro ) {
        $data[$key]["pricelist"] = $current_pricelist_id ? $current_pricelist_id : 0;
        $data[$key]["pricelist_manual_price"] = isset($_pricelist->price) ? $_pricelist->price : 0;
        $data[$key]["pricelist_sections"] = isset($_pricelist->sections) ? $_pricelist->sections : "";
      }
    }

    if (!$image_id_exist) {
      echo WDWLibrary::message(__('The image has been deleted.', BWG()->prefix), 'wd_error');
      die();
    }
    ?>
    <div class="bwg_image_wrap">
      <?php
      $current_pos = 0;
      if ( $params['popup_enable_filmstrip'] ) {
        ?>
        <div class="bwg_filmstrip_container" data-direction="<?php echo $filmstrip_direction; ?>">
          <div class="bwg_filmstrip_left"><i class="<?php echo ($filmstrip_direction == 'horizontal'? 'bwg-icon-angle-left-sm' : 'bwg-icon-angle-up-sm'); ?> "></i></div>
          <div class="bwg_filmstrip">
            <div class="bwg_filmstrip_thumbnails" data-all-images-right-left-space="<?php echo $all_images_right_left_space; ?>" data-all-images-top-bottom-space="<?php echo $all_images_top_bottom_space; ?>">
              <?php
              foreach ($image_rows as $key => $image_row) {
                if ($image_row->id == $current_image_id) {
                  $current_pos = $key * (($filmstrip_direction == 'horizontal' ? $image_filmstrip_width : $image_filmstrip_height) + $filmstrip_thumb_right_left_space);
                  $current_key = $key;
                }
                $thumb_dimansions = $image_row->resolution_thumb;
                $resolution_thumb = true;
                $is_embed = preg_match('/EMBED/',$image_row->filetype)==1 ? true : false;
                $is_embed_instagram = preg_match('/EMBED_OEMBED_INSTAGRAM/', $image_row->filetype ) == 1 ? true : false;
                if ( !$is_embed ) {
                  if($thumb_dimansions == "" || strpos($thumb_dimansions,'x') === false) {
                    $resolution_thumb = false;
                  }
                  if( $resolution_thumb ) {
                    $resolution_th = explode("x", $thumb_dimansions);
                    $image_thumb_width = $resolution_th[0];
                    $image_thumb_height = $resolution_th[1];
                  } else {
                    $image_thumb_width = 1;
                    $image_thumb_height = 1;
                  }
                }
                else {
                  if ($image_row->resolution != '') {
                    if (!$is_embed_instagram) {
                      $resolution_arr = explode(" ", $image_row->resolution);
                      $resolution_w = intval($resolution_arr[0]);
                      $resolution_h = intval($resolution_arr[2]);
                      if($resolution_w != 0 && $resolution_h != 0){
                        $scale = $scale = max($image_filmstrip_width / $resolution_w, $image_filmstrip_height / $resolution_h);
                        $image_thumb_width = $resolution_w * $scale;
                        $image_thumb_height = $resolution_h * $scale;
                      }
                      else{
                        $image_thumb_width = $image_filmstrip_width;
                        $image_thumb_height = $image_filmstrip_height;
                      }
                    }
                    else {
                      // this will be ok while instagram thumbnails width and height are the same
                      $image_thumb_width = min($image_filmstrip_width, $image_filmstrip_height);
                      $image_thumb_height = $image_thumb_width;
                    }
                  }
                  else {
                    $image_thumb_width = $image_filmstrip_width;
                    $image_thumb_height = $image_filmstrip_height;
                  }
                }
				        $_image_filmstrip_width  = $image_filmstrip_width - $filmstrip_thumb_right_left_space;
                $_image_filmstrip_height = $image_filmstrip_height - $filmstrip_thumb_top_bottom_space;
                $scale = max($image_filmstrip_width / $image_thumb_width, $image_filmstrip_height / $image_thumb_height);
                $image_thumb_width *= $scale;
                $image_thumb_height *= $scale;
				        $thumb_left = ($_image_filmstrip_width - $image_thumb_width) / 2;
                $thumb_top = ($_image_filmstrip_height - $image_thumb_height) / 2;
                ?>
                <div id="bwg_filmstrip_thumbnail_<?php echo $key; ?>" class="bwg_filmstrip_thumbnail <?php echo (($image_row->id == $current_image_id) ? 'bwg_thumb_active' : 'bwg_thumb_deactive'); ?>">
                  <div class="bwg_filmstrip_thumbnail_img_wrap">
                    <img <?php if( $is_embed || $resolution_thumb ) { ?>
                      style="width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" <?php } ?>
                      class="bwg_filmstrip_thumbnail_img bwg-hidden"
                      data-url="<?php echo ($is_embed ? "" : BWG()->upload_url) . urldecode($image_row->thumb_url); ?>"
                      src=""
                      onclick='bwg_change_image(parseInt(jQuery("#bwg_current_image_key").val()), "<?php echo $key; ?>")' ontouchend='bwg_change_image(parseInt(jQuery("#bwg_current_image_key").val()), "<?php echo $key; ?>")'
                      image_id="<?php echo $image_row->id; ?>"
                      image_key="<?php echo $key; ?>" alt="<?php echo $image_row->alt; ?>" />
                  </div>
                </div>
              <?php
              }
              ?>
            </div>
          </div>
          <div class="bwg_filmstrip_right"><i class="<?php echo ($filmstrip_direction == 'horizontal'? 'bwg-icon-angle-right-sm' : 'bwg-icon-angle-down-sm'); ?>"></i></div>
        </div>
        <?php
      }
      if ($params['watermark_type'] != 'none') {
      ?>
      <div class="bwg_image_container">
        <div class="bwg_watermark_container">
          <div>
            <span class="bwg_watermark_spun" id="bwg_watermark_container">
              <?php
              $params['watermark_link'] = urldecode($params['watermark_link']);
              if ($params['watermark_type'] == 'image') {
              ?>
              <a class="bwg-a" href="<?php echo esc_js($params['watermark_link']); ?>" target="_blank">
                <img class="bwg_watermark_image bwg_watermark" src="<?php echo $params['watermark_url']; ?>" />
              </a>
              <?php
              }
              elseif ($params['watermark_type'] == 'text') {
              ?>
              <a class="bwg_none_selectable bwg_watermark_text bwg_watermark" target="_blank" href="<?php echo esc_js($params['watermark_link']); ?>"><?php echo urldecode($params['watermark_text']); ?></a>
              <?php
              }
              ?>
            </span>
          </div>
        </div>
      </div>
      <?php
      }
      ?>
      <div id="bwg_image_container" class="bwg_image_container">
      <?php
		echo $this->loading();
		$share_url = '';
		if ($params['popup_enable_ctrl_btn']) {
			$share_url = add_query_arg(array('curr_url' => urlencode($current_url), 'image_id' => $current_image_id), WDWLibrary::get_share_page()) . '#bwg' . $gallery_id . '/' . $current_image_id;
      ?>
      <div class="bwg_btn_container">
        <div class="bwg_ctrl_btn_container">
					<?php
          if ($params['show_image_counts']) {
            ?>
            <span class="bwg_image_count_container bwg_ctrl_btn">
              <span class="bwg_image_count"><?php echo $current_image_key + 1; ?></span> /
              <span><?php echo count($image_rows); ?></span>
            </span>
            <?php
          }
					?>
          <i title="<?php echo __('Play', BWG()->prefix); ?>" class="bwg-icon-play bwg_ctrl_btn bwg_play_pause"></i>
          <?php if ($params['popup_enable_fullscreen']) {
                  if (!$params['popup_fullscreen']) {
          ?>
          <i title="<?php echo __('Maximize', BWG()->prefix); ?>" class="bwg-icon-expand bwg_ctrl_btn bwg_resize-full"></i>
          <?php
          }
          ?>
          <i title="<?php echo __('Fullscreen', BWG()->prefix); ?>" class="bwg-icon-arrows-out bwg_ctrl_btn bwg_fullscreen"></i>
          <?php } if ($params['popup_enable_info']) { ?>
          <i title="<?php echo __('Show info', BWG()->prefix); ?>" class="bwg-icon-info-circle bwg_ctrl_btn bwg_info"></i>
          <?php } if ($params['popup_enable_comment']) { ?>
          <i title="<?php echo __('Show comments', BWG()->prefix); ?>" class="bwg-icon-comment-square bwg_ctrl_btn bwg_comment"></i>
          <?php } if ($params['popup_enable_rate']) { ?>
          <i title="<?php echo __('Show rating', BWG()->prefix); ?>" class="bwg-icon-<?php echo $theme_row->lightbox_rate_icon; ?> bwg_ctrl_btn bwg_rate"></i>
          <?php }
          $is_embed = preg_match('/EMBED/', $current_filetype) == 1 ? TRUE : FALSE;
          $share_image_url = str_replace(array('%252F', '%25252F'), '%2F', urlencode( $is_embed ? $current_thumb_url : BWG()->upload_url . rawurlencode($current_image_url)));
          if ($params['popup_enable_facebook']) {
            ?>
            <a id="bwg_facebook_a" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Facebook', BWG()->prefix); ?>">
              <i title="<?php echo __('Share on Facebook', BWG()->prefix); ?>" class="bwg-icon-facebook-square bwg_ctrl_btn bwg_facebook"></i>
            </a>
            <?php
          }
          if ($params['popup_enable_twitter']) {
            ?>
            <a id="bwg_twitter_a" href="https://twitter.com/share?url=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Twitter', BWG()->prefix); ?>">
              <i title="<?php echo __('Share on Twitter', BWG()->prefix); ?>" class="bwg-icon-twitter-square bwg_ctrl_btn bwg_twitter"></i>
            </a>
            <?php
          }
          if ($params['popup_enable_pinterest']) {
            ?>
            <a id="bwg_pinterest_a" href="http://pinterest.com/pin/create/button/?s=100&url=<?php echo urlencode($share_url); ?>&media=<?php echo $share_image_url; ?>&description=<?php echo $current_image_alt . '%0A' . $current_image_description; ?>" target="_blank" title="<?php echo __('Share on Pinterest', BWG()->prefix); ?>">
              <i title="<?php echo __('Share on Pinterest', BWG()->prefix); ?>" class="bwg-icon-pinterest-square bwg_ctrl_btn bwg_pinterest"></i>
            </a>
            <?php
          }
          if ($params['popup_enable_tumblr']) {
            ?>
            <a id="bwg_tumblr_a" href="https://www.tumblr.com/share/photo?source=<?php echo $share_image_url; ?>&caption=<?php echo urlencode($current_image_alt); ?>&clickthru=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Tumblr', BWG()->prefix); ?>">
              <i title="<?php echo __('Share on Tumblr', BWG()->prefix); ?>" class="bwg-icon-tumblr-square bwg_ctrl_btn bwg_tumblr"></i>
            </a>
            <?php
          }
          if ($params['popup_enable_fullsize_image']) {
            ?>
            <a id="bwg_fullsize_image" href="<?php echo !$is_embed ? BWG()->upload_url . urldecode($current_image_url) : urldecode($current_image_url); ?>" target="_blank">
              <i title="<?php echo __('Open image in original size.', BWG()->prefix); ?>" class="bwg-icon-sign-out bwg_ctrl_btn"></i>
            </a>
            <?php
          }
          if ( $params['popup_enable_download'] ) {
            $style = 'none';
            $current_image_arr = explode('/', $current_image_url);
            if ( !$is_embed ) {
              $download_dir = BWG()->upload_dir . str_replace('/thumb/', '/.original/', urldecode($current_thumb_url));
              WDWLibrary::repair_image_original($download_dir);
              $download_href = BWG()->upload_url . str_replace('/thumb/', '/.original/', urldecode($current_thumb_url));
              $style = 'inline-block';
            }
              ?>
              <a id="bwg_download" <?php if ($is_embed) { ?> class="bwg-hidden" <?php } ?>  href="<?php echo $download_href; ?>" target="_blank" download="<?php echo end($current_image_arr); ?>">
                <i title="<?php echo __('Download original image', BWG()->prefix); ?>" class="bwg-icon-download bwg_ctrl_btn"></i>
              </a>
              <?php

          }
          if ( function_exists('BWGEC') && $params['popup_enable_ecommerce'] == 1 ) {
    		   ?>
				  <i title="<?php echo __('Ecommerce', BWG()->prefix); ?>" style="<?php echo $pricelist_id == 0 ? "display:none;": "";?>" class="bwg-icon-shopping-cart bwg_ctrl_btn bwg_ecommerce"></i>
		       <?php
		      }
          ?>
        </div>
        <div class="bwg_toggle_container">
          <i class="bwg_toggle_btn <?php echo (($theme_row->lightbox_ctrl_btn_pos == 'top') ? 'bwg-icon-caret-up' : 'bwg-icon-caret-down'); ?>"></i>
        </div>
      </div>
      <?php
      }?>
        <div class="bwg_image_info_container1">
          <div class="bwg_image_info_container2">
            <span class="bwg_image_info_spun">
              <div class="bwg_image_info" <?php if(trim($current_image_alt) == '' && trim($current_image_description) == '') { echo 'style="opacity: 0;"'; } ?>>
                <div class="bwg_image_title"><?php echo html_entity_decode($current_image_alt); ?></div>
                <div class="bwg_image_description"><?php echo html_entity_decode($current_image_description); ?></div>
              </div>
            </span>
          </div>
        </div>
        <div class="bwg_image_hit_container1">
          <div class="bwg_image_hit_container2">
            <span class="bwg_image_hit_spun">
              <div class="bwg_image_hit">
                <div class="bwg_image_hits"><?php echo __('Hits: ', BWG()->prefix); ?><span><?php echo $current_image_hit_count; ?></span></div>
              </div>
            </span>
          </div>
        </div>
        <?php
        $data_rated = array(
          'current_rate' => $current_rate,
          'current_rate_count' => $current_rate_count,
          'current_avg_rating' => $current_avg_rating,
          'current_image_key' => $current_image_key,
        );
        $data_rated = json_encode($data_rated);
        ?>
        <div class="bwg_image_rate_container1">
          <div class="bwg_image_rate_container2">
            <span class="bwg_image_rate_spun">
              <span class="bwg_image_rate">
                <form id="bwg_rate_form" method="post" action="<?php echo $popup_url; ?>">
                  <span id="bwg_star" class="bwg_star" data-score="<?php echo $current_avg_rating; ?>"></span>
                  <span id="bwg_rated" data-params='<?php echo $data_rated; ?>' class="bwg_rated"><?php echo __('Rated.', BWG()->prefix); ?></span>
                  <span id="bwg_hint" class="bwg_hint"></span>
                  <input id="rate_ajax_task" name="ajax_task" type="hidden" value="" />
                  <input id="rate_image_id" name="image_id" type="hidden" value="<?php echo $image_id; ?>" />
                </form>
              </span>
            </span>
          </div>
        </div>
        <div class="bwg_slide_container">
          <div class="bwg_slide_bg">
            <div class="bwg_slider">
          <?php
          $current_key = -6;
          foreach ( $image_rows as $key => $image_row ) {
            $is_embed = preg_match('/EMBED/',$image_row->filetype)==1 ? true :false;
            $is_embed_instagram_post = preg_match('/INSTAGRAM_POST/',$image_row->filetype)==1 ? true : false;
            $is_embed_instagram_video = preg_match('/INSTAGRAM_VIDEO/', $image_row->filetype) == 1 ? true : false;
            $is_ifrem = ( in_array($image_row->filetype, array('EMBED_OEMBED_YOUTUBE_VIDEO', 'EMBED_OEMBED_VIMEO_VIDEO', 'EMBED_OEMBED_FACEBOOK_VIDEO', 'EMBED_OEMBED_DAILYMOTION_VIDEO') ) ) ? true : false;
            if ($image_row->id == $current_image_id) {
              $current_key = $key;
              ?>
              <span class="bwg_popup_image_spun" id="bwg_popup_image" image_id="<?php echo $image_row->id; ?>">
                <span class="bwg_popup_image_spun1" style="display: <?php echo ( !$is_embed ? 'table' : 'block' ); ?>;">
                  <span class="bwg_popup_image_spun2" style="display: <?php echo ( !$is_embed ? 'table-cell' : 'block' ); ?>; ">
                    <?php
                      if ( !$is_embed ) {
                      ?>
                      <img class="bwg_popup_image bwg_popup_watermark" src="<?php echo BWG()->upload_url . $image_row->image_url; ?>" alt="<?php echo $image_row->alt; ?>" />
                      <?php
                      }
                      else { /*$is_embed*/ ?>
                        <span id="embed_conteiner" class="bwg_popup_embed bwg_popup_watermark" style="display: <?php echo ( $is_ifrem ? 'block' : 'table' ); ?>; ">
                        <?php echo $is_embed_instagram_video ? '<span class="bwg_inst_play_btn_cont" onclick="bwg_play_instagram_video(this)" ><span class="bwg_inst_play"></span></span>' : '';
                        if ($is_embed_instagram_post) {
                          $post_width = $params['popup_width'] - ($filmstrip_direction == 'vertical' ? $image_filmstrip_width : 0);
                          $post_height = $params['popup_height'] - ($filmstrip_direction == 'horizontal' ? $image_filmstrip_height : 0);
                          if ($post_height < $post_width + 132) {
                            $post_width = $post_height - 132;
                          }
                          else {
                           $post_height = $post_width + 132;
                          }

                          $instagram_post_width = $post_width;
                          $instagram_post_height = $post_height;
                          $image_resolution = explode(' x ', $image_row->resolution);
                          if (is_array($image_resolution)) {
                            $instagram_post_width = $image_resolution[0];
                            $instagram_post_height = explode(' ', $image_resolution[1]);
                            $instagram_post_height = $instagram_post_height[0];
                          }
                          WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array('class' => "bwg_embed_frame", 'data-width' => $instagram_post_width, 'data-height' => $instagram_post_height, 'frameborder' => "0", 'style' => "width:" . $post_width . "px; height:" . $post_height . "px; vertical-align:middle; display:inline-block; position:relative;"));
                        }
                        else{
                          WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array('class'=>"bwg_embed_frame", 'frameborder'=>"0", 'allowfullscreen'=>"allowfullscreen", 'style'=> "display: " . ( $is_ifrem ? 'block' : 'table-cell' ) . "; width:inherit; height:inherit; vertical-align:middle;"));
                        }
                        ?>
                      </span>
                      <?php
                      }
                    ?>
                  </span>
                </span>
              </span>
              <span class="bwg_popup_image_second_spun">
              </span>
              <input type="hidden" id="bwg_current_image_key" value="<?php echo $key; ?>" />
              <?php
              break;
            }
          }
          ?>
            </div>
          </div>
        </div>
        <a id="spider_popup_left" <?php echo ($params['enable_loop'] == 0 && $current_key == 0) ? 'style="display: none;"' : ''; ?>><span id="spider_popup_left-ico"><span><i class="bwg_prev_btn <?php echo $theme_row->lightbox_rl_btn_style; ?>-left"></i></span></span></a>
        <a id="spider_popup_right" <?php echo ($params['enable_loop'] == 0 && $current_key == count($image_rows) - 1) ? 'style="display: none;"' : ''; ?>><span id="spider_popup_right-ico"><span><i class="bwg_next_btn <?php echo $theme_row->lightbox_rl_btn_style; ?>-right"></i></span></span></a>
      </div>
    </div>
    <?php if ( $params['popup_enable_comment'] ) {
      $bwg_name = WDWLibrary::get('bwg_name');
      $bwg_email = WDWLibrary::get('bwg_email');
      ?>
    <div class="bwg_comment_wrap bwg_popup_sidebar_wrap">
      <div class="bwg_comment_container bwg_popup_sidebar_container bwg_close">
        <div id="ajax_loading">
          <div id="opacity_div" style="display:none;"></div>
          <span id="loading_div" class="bwg_spider_ajax_loading" style="display:none; background-image:url(<?php echo BWG()->plugin_url . '/images/ajax_loader.png'; ?>);">
          </span>
        </div>
        <div class="bwg_comments bwg_popup_sidebar">
            <div title="<?php echo __('Hide Comments', BWG()->prefix); ?>" class="bwg_comments_close bwg_popup_sidebar_close">
              <i class="bwg-icon-arrow-<?php echo $theme_row->lightbox_comment_pos; ?> bwg_comments_close_btn bwg_popup_sidebar_close_btn"></i>
            </div>
            <form id="bwg_comment_form" method="post" action="<?php echo $popup_url; ?>">
				<p><label for="bwg_name"><?php echo __('Name', BWG()->prefix); ?> </label></p>
				<p><input class="bwg-validate" type="text" name="bwg_name" id="bwg_name" <?php echo ((get_current_user_id() != 0) ? 'readonly="readonly"' : ''); ?>
                        value="<?php echo ((get_current_user_id() != 0) ? get_userdata(get_current_user_id())->display_name : $bwg_name); ?>" />
				</p>
				<p><span class="bwg_comment_error bwg_comment_name_error"></span></p>
              <?php if ($params['popup_enable_email']) { ?>
				<p><label for="bwg_email"><?php echo __('Email', BWG()->prefix); ?> </label></p>
				<p><input class="bwg-validate" type="text" name="bwg_email" id="bwg_email"
                        value="<?php echo ((get_current_user_id() != 0) ? get_userdata(get_current_user_id())->user_email : $bwg_email); ?>" /></p>
				<p><span class="bwg_comment_error bwg_comment_email_error"></span></p>
              <?php } ?>
				<p><label for="bwg_comment"><?php echo __('Comment', BWG()->prefix); ?> </label></p>
				<p><textarea class="bwg-validate bwg_comment_textarea" name="bwg_comment" id="bwg_comment"></textarea></p>
				<p><span class="bwg_comment_error bwg_comment_textarea_error"></span></p>

              <?php if ( $params['popup_enable_captcha'] && !$params['gdpr_compliance']) { ?>

				<p><label for="bwg_captcha_input"><?php echo __('Verification Code', BWG()->prefix); ?></label></p>
				<p>
					<input id="bwg_captcha_input" name="bwg_captcha_input" class="bwg_captcha_input" type="text" autocomplete="off">
					<img id="bwg_captcha_img" class="bwg_captcha_img" type="captcha" digit="6" src="<?php echo add_query_arg(array('action' => 'bwg_captcha', 'digit' => 6, 'i' => ''), admin_url('admin-ajax.php')); ?>" onclick="bwg_captcha_refresh('bwg_captcha')" ontouchend="bwg_captcha_refresh('bwg_captcha')" />
					<span id="bwg_captcha_refresh" class="bwg_captcha_refresh" onclick="bwg_captcha_refresh('bwg_captcha')" ontouchend="bwg_captcha_refresh('bwg_captcha')"></span>
				</p>
				<p><span class="bwg_comment_error bwg_comment_captcha_error"></span></p>
              <?php } ?>

			  <?php
			  $privacy_policy_url = false;
			  if ( WDWLibrary::get_privacy_policy_url() ) {
				  $privacy_policy_url = true;
			  ?>
			  <p class="bwg-privacy-policy-box">
				  <label for="bwg_comment_privacy_policy">
                  <input id="bwg_comment_privacy_policy"
						name="bwg_comment_privacy_policy"
						onclick="comment_check_privacy_policy()"
						ontouchend="comment_check_privacy_policy()"
						type="checkbox"
						value="1" <?php echo (WDWLibrary::get('bwg_comment_privacy_policy') ? 'checked' : ''); ?> />
				  <?php
					$privacy_policy_text = __('I consent collecting this data and processing it according to %s of this website.', BWG()->prefix);
					$privacy_policy_link = ' <a href="' . WDWLibrary::get_privacy_policy_url() . '" target="_blank">' . __('Privacy Policy', BWG()->prefix) . '</a>';
					echo sprintf($privacy_policy_text, $privacy_policy_link);
				  ?>
				  </label>
			  </p>
			  <p><span class="bwg_comment_error bwg_comment_privacy_policy_error"></span></p>
			  <?php } ?>
			  <p>
				<input <?php echo ($privacy_policy_url) ? 'disabled="disabled"' : ''; ?> onclick="bwg_add_comment(); return false;" ontouchend="bwg_add_comment(); return false;" class="bwg_submit <?php echo ($privacy_policy_url) ? 'bwg-submit-disabled' : ''; ?>" type="submit"
					 name="bwg_submit" id="bwg_submit" value="<?php echo __('Submit', BWG()->prefix); ?>" />
			  </p>
			  <p class="bwg_comment_waiting_message"><?php _e('Your comment is awaiting moderation', BWG()->prefix); ?></p>
			  <input id="ajax_task" name="ajax_task" type="hidden" value="" />
			  <input id="image_id"id="image_id" name="image_id" type="hidden" value="<?php echo $image_id; ?>" />
              <input id="comment_id" name="comment_id" type="hidden" value="" />
              <input type="hidden" value="<?php echo $params['comment_moderation'] ?>" id="bwg_comment_moderation">
              <input type="hidden" value="<?php echo ($params['gdpr_compliance']) ? 0 : $params['popup_enable_captcha']; ?>" id="bwg_popup_enable_captcha">
            </form>
          <div id="bwg_added_comments">
            <?php
            $comment_rows = $this->model->get_comment_rows_data($image_id);
            foreach ( $comment_rows as $comment_row ) {
				      echo $this->html_comments_block($comment_row);
            }
            ?>
          </div>
        </div>
      </div>
    </div>
    <?php }
    if ( function_exists('BWGEC') ) {
      $pricelist = $pricelist_data["pricelist"];
      $download_items = $pricelist_data["download_items"];
      $parameters = $pricelist_data["parameters"];
      $options = $pricelist_data["options"];
      $products_in_cart = $pricelist_data["products_in_cart"];
      $pricelist_sections = $pricelist->sections ? explode(",", $pricelist->sections) : array();
      ?>
			<div class="bwg_ecommerce_wrap bwg_popup_sidebar_wrap" id="bwg_ecommerce_wrap">
				<div class="bwg_ecommerce_container bwg_popup_sidebar_container bwg_close">
					<div id="ecommerce_ajax_loading">
						<div id="ecommerce_opacity_div"></div>
						<span id="ecommerce_loading_div" class="bwg_spider_ajax_loading" style="background-image:url(<?php echo BWG()->plugin_url . '/images/ajax_loader.png'; ?>);"></span>
					</div>
					<div class="bwg_ecommerce_panel bwg_popup_sidebar_panel bwg_popup_sidebar">
						<div id="bwg_ecommerce">
							<p title="<?php echo __('Hide Ecommerce', BWG()->prefix); ?>" class="bwg_ecommerce_close bwg_popup_sidebar_close" >
								<i class="bwg-icon-arrow-<?php echo $theme_row->lightbox_comment_pos; ?> bwg_ecommerce_close_btn bwg_popup_sidebar_close_btn"></i>
							</p>
							<form id="bwg_ecommerce_form" method="post" action="<?php echo $popup_url; ?>">
								<div class="pge_add_to_cart">
									<div>
										<span class="pge_add_to_cart_title"><?php echo (__('Add to cart', BWG()->prefix)); ?></span>
									</div>
									<div>
										<a href="<?php echo get_permalink($options->checkout_page);?>"><?php echo "<span class='products_in_cart'>".$products_in_cart ."</span> ". __('items', BWG()->prefix); ?></a>
									</div>
								</div>
								<div class="bwg_ecommerce_body">
									<ul class="pge_tabs" <?php if(count($pricelist_sections)<=1) echo "style='display:none;'"; ?>>
										<li id="manual_li" <?php if(!in_array("manual",$pricelist_sections)) { echo "style='display:none;'"; } ?> class="pge_active">
											<a href= "#manual">
												<span class="manualh4" >
													<?php echo __('Prints and products', BWG()->prefix); ?>
												</span>
											</a>
										</li>
										<li id="downloads_li" <?php if(!in_array("downloads",$pricelist_sections)) echo "style='display:none;'"; ?> >
											<a href= "#downloads">
											<span class="downloadsh4" >
												<?php echo __('Downloads', BWG()->prefix); ?>
											</span>
											</a>
										</li>
									</ul>
									<div class="pge_tabs_container" >
									<!-- manual -->
									<div class="manual pge_pricelist" id="manual" <?php if( count($pricelist_sections) == 2  || (count($pricelist_sections) == 1 && end($pricelist_sections) == "manual")) echo 'style="display: block;"'; else echo 'style="display: none;"'; ?>  >
										<div>
											<div class="product_manual_price_div">
												<p><?php echo $pricelist->manual_title ? __('Name', BWG()->prefix).': '.$pricelist->manual_title : "";?></p>
                                               <?php if ($pricelist->price) {
                                                 ?>
												<p>
													<span><?php echo __('Price', BWG()->prefix).': '.$options->currency_sign;?></span>
													<span class="_product_manual_price"><?php echo number_format((float)$pricelist->price,2)?></span>
												</p>
                                                  <?php
                                                }
                                              ?>
											</div>
                                          <?php if($pricelist->manual_description){
                                          ?>
											<div class="product_manual_desc_div">
												<p>
													<span><?php echo __('Description', BWG()->prefix);?>:</span>
													<span class="product_manual_desc"><?php echo $pricelist->manual_description;?></span>
												</p>
											</div>
											<?php
                                              }
                                              ?>
											<div class="image_count_div">
												<p>
													<?php echo __('Count', BWG()->prefix).': ';?>
													<input type="number" min="1" class="image_count" value="1" onchange="changeMenualTotal(this);">
												</p>
											</div>
											<?php if ( empty($parameters) == false ) { ?>
											<div class="image_parameters">
												<p><?php //echo __('Parameters', BWG()->prefix); ?></p>
												<?php
													$i = 0;
													foreach($parameters as $parameter_id => $parameter){
														echo '<div class="parameter_row">';
														switch($parameter["type"]){
															case "1" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo $parameter["title"].": <span class='parameter_single'>". $parameter["values"][0]["parameter_value"]."</span>";
																echo '</div>';
																break;
															case "2" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo '<label for="parameter_input">'.$parameter["title"].'</label>';
																echo '<input type="text" name="parameter_input'.$parameter_id.'" id="parameter_input"  value="'. $parameter["values"][0]["parameter_value"] .'">';
																echo '</div>';
																break;
															case "3" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo '<label for="parameter_textarea">'.$parameter["title"].'</label>';
																echo '<textarea  name="parameter_textarea'.$parameter_id.'" id="parameter_textarea"  >'. $parameter["values"][0]["parameter_value"] .'</textarea>';
																echo '</div>';
																break;
															case "4" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo '<label for="parameter_select">'.$parameter["title"].'</label>';
																echo '<select name="parameter_select'.$parameter_id.'" id="parameter_select" onchange="onSelectableParametersChange(this)">';
																echo '<option value="+*0*">-Select-</option>';
																foreach($parameter["values"] as $values){
                                                                    $price_addon = $values["parameter_value_price"] == "0" ? "" : ' ('.$values["parameter_value_price_sign"].$options->currency_sign.number_format((float)$values["parameter_value_price"],2).')';
																	echo '<option value="'.$values["parameter_value_price_sign"].'*'.$values["parameter_value_price"].'*'.$values["parameter_value"].'">'.$values["parameter_value"].$price_addon.'</option>';
																}
																echo '</select>';
																echo '<input type="hidden" class="already_selected_values">';
																echo '</div>';
																break;
															case "5" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo '<label>'.$parameter["title"].'</label>';
																foreach($parameter["values"] as $values){
                                                                    $price_addon = $values["parameter_value_price"] == "0"	? "" : 	' ('.$values["parameter_value_price_sign"].$options->currency_sign.number_format((float)$values["parameter_value_price"],2).')';
																	echo '<div>';
																	echo '<input type="radio" name="parameter_radio'.$parameter_id.'"  id="parameter_radio'.$i.'" value="'.$values["parameter_value_price_sign"].'*'.$values["parameter_value_price"].'*'.$values["parameter_value"].'"  onchange="onSelectableParametersChange(this)">';
																	echo '<label for="parameter_radio'.$i.'">'.$values["parameter_value"].$price_addon.'</label>';
																	echo '</div>';
																	$i++;
																}
																echo '<input type="hidden" class="already_selected_values">';
																echo '</div>';
																break;
															case "6" :
																echo '<div class="image_selected_parameter" data-parameter-id="'.$parameter_id.'" data-parameter-type = "'.$parameter["type"].'">';
																echo '<label>'.$parameter["title"].'</label>';
																foreach($parameter["values"] as $values){
                                                                    $price_addon = $values["parameter_value_price"] == "0" ? "" : ' ('.$values["parameter_value_price_sign"].$options->currency_sign.number_format((float)$values["parameter_value_price"],2).')';
																	echo '<div>';
																	echo '<input type="checkbox" name="parameter_checkbox'.$parameter_id.'" id="parameter_checkbox'.$i.'" value="'.$values["parameter_value_price_sign"].'*'.$values["parameter_value_price"].'*'.$values["parameter_value"].'"  onchange="onSelectableParametersChange(this)">';
																	echo '<label for="parameter_checkbox'.$i.'">'.$values["parameter_value"].$price_addon.'</label>';
																	echo '</div>';
																	$i++;
																}
																echo '<input type="hidden" class="already_selected_values">';
																echo '</div>';
																break;
															default:
																break;
														}
														echo '</div>';
													}
												?>

											</div>
											<?php } ?>
											<p>
												<span><b><?php echo __('Total', BWG()->prefix).': '.$options->currency_sign;?></b></span>
												<b><span class="product_manual_price" data-price="<?php echo $pricelist->price; ?>" data-actual-price="<?php echo $pricelist->price; ?>"><?php echo number_format((float)$pricelist->price,2)?></span></b>
											</p>
										</div>
									</div>
									<!-- downloads -->
									<div class="downloads pge_pricelist" id="downloads" <?php if( (count($pricelist_sections) == 1 && end($pricelist_sections) == "downloads")) echo 'style="display: block;"'; else echo 'style="display: none;"'; ?> >
										<table>
											<thead>
												<tr>
													<th><?php echo __('Name', BWG()->prefix); ?></th>
													<th><?php echo __('Dimensions', BWG()->prefix); ?></th>
													<th><?php echo __('Price', BWG()->prefix); ?></th>
												  <th><?php echo __('Choose', BWG()->prefix); ?></th>
												</tr>
											</thead>
											<tbody>
												<?php
													if(empty($download_items) === false){
														foreach($download_items as $download_item){
														?>
															<tr data-price="<?php echo $download_item->item_price; ?>" data-id="<?php echo $download_item->id; ?>">
																<td><?php echo $download_item->item_name; ?></td>
																<td><?php echo $download_item->item_longest_dimension.'px'; ?></td>
																<td class="item_price"><?php echo $options->currency_sign. number_format((float)$download_item->item_price, 2); ?></td>
																<?php if($options->show_digital_items_count == 0){
																  ?>
																  <td><input type="checkbox"  name="selected_download_item" value="<?php echo $download_item->id; ?>" onchange="changeDownloadsTotal(this);"></td>
																  <?php
																}
																else{
																  ?>
																  <td><input type="number" min="0" class="digital_image_count" value="0" onchange="changeDownloadsTotal(this);"></td>
																  <?php
																}
																?>
															  </tr>
														<?php
														}
													}
												?>
											</tbody>
										</table>
										<p>
											<span><b><?php echo __('Total', BWG()->prefix).': '.$options->currency_sign;?></b></span>
											<b><span class="product_downloads_price">0</span></b>
										</p>
									</div>
									</div>
								</div>
								<div style="margin-top:10px;">
									<input type="button" class="bwg_submit" value="<?php echo __('Add to cart', BWG()->prefix); ?>" onclick="onBtnClickAddToCart();">
									<input type="button" class="bwg_submit" value="<?php echo __('View cart', BWG()->prefix); ?>" onclick="onBtnViewCart()">
									&nbsp;<span class="add_to_cart_msg"></span>
								</div>
								<input id="ajax_task" name="ajax_task" type="hidden" value="" />
								<input id="ajax_url" type="hidden" value="<?php echo admin_url('admin-ajax.php'); ?>" />
								<input id="type" name="type" type="hidden" value="<?php echo isset($pricelist_sections[0]) ? $pricelist_sections[0] : ""  ?>" />
								<input id="image_id" name="image_id" type="hidden" value="<?php echo $image_id; ?>" />
								<div class="pge_options">
									<input type="hidden" name="option_checkout_page" value="<?php  echo get_permalink($options->checkout_page);?>">
									<input type="hidden" name="option_show_digital_items_count" value="<?php echo $options->show_digital_items_count;?>">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
	<?php
	}
    if ( BWG()->options->use_inline_stiles_and_scripts ) {
      if ( $has_embed ) {
        ?>
        <script language="javascript" type="text/javascript" src="<?php echo BWG()->plugin_url . '/js/bwg_embed.js?ver=' . BWG()->plugin_version; ?>"></script>
        <?php
      }
    }
	?>
    <a class="spider_popup_close" onclick="spider_destroypopup(1000); return false;" ontouchend="spider_destroypopup(1000); return false;"><span><i class="bwg-icon-times-sm bwg_close_btn"></i></span></a>
    <?php
    $bwg_gallery_box_params = array(
      'bwg'                                   => $bwg,
      'bwg_current_key'                       => $current_key,
      'enable_loop'                           => $params['enable_loop'],
      'ecommerceACtive'                       => (function_exists('BWGEC') ) == true ? 1 : 0,
      'enable_image_ecommerce'                => $params['popup_enable_ecommerce'],
      'lightbox_ctrl_btn_pos'                 => $theme_row->lightbox_ctrl_btn_pos,
      'lightbox_info_pos'                     => $theme_row->lightbox_info_pos,
      'lightbox_close_btn_top'                => $theme_row->lightbox_close_btn_top,
      'lightbox_close_btn_right'              => $theme_row->lightbox_close_btn_right,
      'popup_enable_rate'                     => $params['popup_enable_rate'],
      'lightbox_filmstrip_thumb_border_width' => $theme_row->lightbox_filmstrip_thumb_border_width,
      'width_or_height'                       => $width_or_height,
      'preload_images'                        => BWG()->options->preload_images,
      'preload_images_count'                  => (int) BWG()->options->preload_images_count,
      'bwg_image_effect'                      => $params['popup_effect'],
      'enable_image_filmstrip'                => $params['popup_enable_filmstrip'],
      'gallery_id'                            => $gallery_id,
      'site_url'                              => BWG()->upload_url,
      'lightbox_comment_width'                => $theme_row->lightbox_comment_width,
      'watermark_width'                       => $params['watermark_width'],
      'image_width'                           => $params['popup_width'],
      'image_height'                          => $params['popup_height'],
      'outerWidth_or_outerHeight'             => $outerWidth_or_outerHeight,
      'left_or_top'                           => $left_or_top,
      'lightbox_comment_pos'                  => $theme_row->lightbox_comment_pos,
      'filmstrip_direction'                   => $filmstrip_direction,
      'image_filmstrip_width'                 => $image_filmstrip_width,
      'image_filmstrip_height'                => $image_filmstrip_height,
      'lightbox_info_margin'                  => $theme_row->lightbox_info_margin,
      'bwg_share_url'                         => add_query_arg(array('curr_url' => urlencode($current_url), 'image_id' => ''), WDWLibrary::get_share_page()),
      'bwg_share_image_url'                   => urlencode(BWG()->upload_url),
      'slideshow_interval'                    => $params['popup_interval'],
      'open_with_fullscreen'                  => $params['popup_fullscreen'],
      'open_with_autoplay'                    => $params['popup_autoplay'],
      'event_stack'                           => array(),
      'bwg_playInterval'                      => 0,
      'data'                                  => $data,
      'is_pro'                                => BWG()->is_pro,
      'enable_addthis'                        => $params['enable_addthis'],
      'addthis_profile_id'                    => $params['addthis_profile_id'],
      'share_url'                             => $share_url,
      'current_pos'                           => $current_pos,
      'current_image_key'                     => $current_image_key,
      'slideshow_effect_duration'             => $params['popup_effect_duration'],
      'current_image_id'                      => $current_image_id,
      'lightbox_rate_stars_count'             => $theme_row->lightbox_rate_stars_count,
      'lightbox_rate_size'                    => $theme_row->lightbox_rate_size,
      'lightbox_rate_icon'                    => $theme_row->lightbox_rate_icon,
      'bwg_ctrl_btn_container_height'         => $theme_row->lightbox_ctrl_btn_height + 2 * $theme_row->lightbox_ctrl_btn_margin_top,
      'filmstrip_thumb_right_left_space'      => $filmstrip_thumb_right_left_space,
      'all_images_right_left_space'           => $all_images_right_left_space,
      'image_right_click'                     => $image_right_click,
      'open_comment'                          => isset($params['open_comment']) ? $params['open_comment'] : FALSE,
      'open_ecommerce'                        => isset($params['open_ecommerce']) ? $params['open_ecommerce'] : FALSE,
    );
    $gallery_box_data = json_encode( $bwg_gallery_box_params );
    ?>
    <script>var gallery_box_data = JSON.parse('<?php echo $gallery_box_data; ?>');</script>
    <?php
    die();
  }

  private function loading() {
    ?>
    <div class="bwg-loading bwg-hidden"></div>
    <?php
  }

  public function html_comments_block( $row = array() ) {
    ob_start();
	?>
	<div id="bwg_comment_block_<?php echo $row->id; ?>" class="bwg_single_comment">
		<p class="bwg_comment_header_p">
		  <span class="bwg_comment_header"><?php echo $row->name; ?></span>
			<?php if ( current_user_can('manage_options') ) { ?>
				<i onclick="bwg_remove_comment(<?php echo $row->id; ?>); return false;"
					ontouchend="bwg_remove_comment(<?php echo $row->id; ?>); return false;"
					title="<?php _e('Delete Comment', BWG()->prefix); ?>" class="bwg-icon-times bwg_comment_delete_btn"></i>
			<?php } ?>
		  <span class="bwg_comment_date"><?php echo $row->date; ?></span>
		</p>
		<div class="bwg_comment_body_p">
		  <span class="bwg_comment_body"><?php echo wpautop($row->comment); ?></span>
		</div>
  </div>
    <?php
    return ob_get_clean();
  }
}
