<?php
class BWGViewAlbum_compact_preview extends BWGViewSite {

  private $gallery_view = FALSE;

  public function display($params = array(), $bwg = 0) {
    /* Gallery view class.*/
	$gallery_type = 'Thumbnails';
	if ( $params['gallery_view_type'] == 'masonry' ) {
      $gallery_type = 'Thumbnails_masonry';
    }
    elseif ( $params['gallery_view_type'] == 'mosaic' ) {
      $gallery_type = 'Thumbnails_mosaic';
    }
	elseif ( $params['gallery_view_type'] == 'slideshow' ) {
      $gallery_type = 'Slideshow';
    }
	elseif ( $params['gallery_view_type'] == 'image_browser' ) {
      $gallery_type = 'Image_browser';
    }
	elseif ( $params['gallery_view_type'] == 'blog_style' ) {
      $gallery_type = 'Blog_style';
    }
    elseif ( $params['gallery_view_type'] == 'carousel' ) {
      $gallery_type = 'Carousel';
    }
    require_once BWG()->plugin_dir . '/frontend/views/BWGView' . $gallery_type . '.php';
    $view_class = 'BWGView' . $gallery_type;
    $this->gallery_view = new $view_class();
	$theme_row = $params['theme_row'];

	$from = (isset($params['from']) ? esc_html($params['from']) : 0);
    $breadcrumb_arr = array(
		0 => array(
				'id' => $params['album_gallery_id'],
				'page' => WDWLibrary::get('page_number_' . $bwg, 1, 'intval')
			)
		);
    $breadcrumb = WDWLibrary::get('bwg_album_breadcrumb_' . $bwg);
	$breadcrumb = !empty($breadcrumb) ? $breadcrumb : json_encode($breadcrumb_arr);
    $params['breadcrumb_arr'] = json_decode($breadcrumb);

    /* Set theme parameters for Gallery/Gallery group title/description.*/
    $theme_row->thumb_gal_title_font_size = $theme_row->album_compact_gal_title_font_size;
    $theme_row->thumb_gal_title_font_color = $theme_row->album_compact_gal_title_font_color;
    $theme_row->thumb_gal_title_font_style = $theme_row->album_compact_gal_title_font_style;
    $theme_row->thumb_gal_title_font_weight = $theme_row->album_compact_gal_title_font_weight;
    $theme_row->thumb_gal_title_shadow = $theme_row->album_compact_gal_title_shadow;
    $theme_row->thumb_gal_title_margin = $theme_row->album_compact_gal_title_margin;
    $theme_row->thumb_gal_title_align = $theme_row->album_compact_gal_title_align;
    $inline_style = $this->inline_styles($bwg, $theme_row, $params);
    $lazyload = BWG()->options->lazyload_images;
    if ( !WDWLibrary::elementor_is_active() ) {
      if ( !$params['ajax'] ) {
        if ( BWG()->options->use_inline_stiles_and_scripts ) {
			wp_add_inline_style('bwg_frontend', $inline_style);
        }
        else {
			echo '<style id="bwg-style-' . $bwg . '">' . $inline_style . '</style>';
        }
      }
    }
    else {
      echo '<style id="bwg-style-' . $bwg . '">' . $inline_style . '</style>';
    }
    ob_start();

    if ( $params['album_view_type'] != 'gallery' ) {
      ?>
    <div data-max-count="<?php echo $params['items_col_num']; ?>"
         data-thumbnail-width="<?php echo $params['compuct_album_thumb_width']; ?>"
         data-bwg="<?php echo $bwg; ?>"
         id="<?php echo $params['container_id']; ?>"
         class="bwg-thumbnails bwg-container bwg-container-<?php echo $bwg; ?> bwg-album-thumbnails <?php echo $params['album_gallery_div_class']; ?>">
      <?php
      if ( !$params['album_gallery_rows']['page_nav']['total'] ) {
        echo WDWLibrary::message(__('No results found.', BWG()->prefix), 'wd_error');
      }
      foreach ( $params['album_gallery_rows']['rows'] as $row ) {
        $href = add_query_arg(array(
                                "type_" . $bwg => $row->def_type,
                                "album_gallery_id_" . $bwg => (($params['album_gallery_id'] != 0) ? $row->alb_gal_id : $row->id),
                              ), $_SERVER['REQUEST_URI']);
        $href = $this->http_strip_query_param($href, 'bwg_search_' . $bwg);
        $href = $this->http_strip_query_param($href, 'page_number_' . $bwg);
        $title = '<div class="bwg-title1"><div class="bwg-title2">' . ($row->name ? htmlspecialchars_decode($row->name, ENT_COMPAT | ENT_QUOTES) : '&nbsp;') . '</div></div>';
        $resolution_thumb = $row->resolution_thumb;
        $image_thumb_width = '';
        $image_thumb_height = '';
        if ( $resolution_thumb != "" && strpos($resolution_thumb, 'x') !== FALSE ) {
          $resolution_th = explode("x", $resolution_thumb);
          $image_thumb_width = $resolution_th[0];
          $image_thumb_height = $resolution_th[1];
        }
        $enable_seo = (int) BWG()->options->enable_seo;
        $enable_dynamic_url = (int) BWG()->options->front_ajax;
        ?>
        <div class="bwg-item">
          <a class="bwg-a <?php echo $from !== "widget" ? 'bwg-album ' : ''; ?>bwg_album_<?php echo $bwg; ?>"
             <?php echo ( ($enable_seo || $enable_dynamic_url) && $from !== "widget" ? "href='" . esc_url($href) . "'" : ""); ?>
             <?php echo $from === "widget" ? 'href="' . $row->permalink . '"' : ''; ?>
             data-container_id="<?php echo $params['container_id']; ?>"
             data-def_type="<?php echo $row->def_type; ?>"
             data-album_gallery_id="<?php echo $params['album_gallery_id']; ?>"
             data-alb_gal_id="<?php echo (($params['album_gallery_id'] != 0) ? $row->alb_gal_id : $row->id); ?>"
             data-title="<?php echo htmlspecialchars(addslashes($row->name)); ?>"
             data-bwg="<?php echo $bwg; ?>">
            <?php if ( $params['compuct_album_title'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'top' ) { echo $title; } ?>
            <div class="bwg-item0 <?php echo ($lazyload) ? 'lazy_loader': ''; ?>">
              <div class="bwg-item1 <?php echo $theme_row->album_compact_thumb_hover_effect == 'zoom' && $params['compuct_album_title'] == 'hover' ? 'bwg-zoom-effect' : ''; ?>">
                <div class="bwg-item2">
                  <img class="skip-lazy <?php if( $lazyload ) { ?> bwg_lazyload <?php } ?>"
                       data-width="<?php echo $image_thumb_width; ?>"
                       data-height="<?php echo $image_thumb_height; ?>"
                       data-original="<?php echo $row->preview_image; ?>"
                       src="<?php if( !$lazyload ) { echo $row->preview_image; } else { echo BWG()->plugin_url."/images/lazy_placeholder.gif"; } ?>"
                       alt="<?php echo $row->name; ?>" />
                </div>
                <div class="<?php echo $theme_row->album_compact_thumb_hover_effect == 'zoom' && $params['compuct_album_title'] == 'hover' ? 'bwg-zoom-effect-overlay' : ''; ?>">
                  <?php if ( $params['compuct_album_title'] == 'hover' ) { echo $title; } ?>
                </div>
              </div>
            </div>
            <?php if ( $params['compuct_album_title'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'bottom' ) { echo $title; } ?>
          </a>
        </div>
        <?php
      }
      ?>
    </div>
      <?php
    }
    elseif ( $params['album_view_type'] == 'gallery' ) {
      $theme_row->thumb_title_pos = $theme_row->album_compact_thumb_title_pos;
      if ( $this->gallery_view && method_exists($this->gallery_view, 'display') ) {
        $this->gallery_view->display($params, $bwg, TRUE);
      }
    }
    ?>
    <input type="hidden" id="bwg_album_breadcrumb_<?php echo $bwg; ?>" name="bwg_album_breadcrumb_<?php echo $bwg; ?>" value='<?php echo esc_attr($breadcrumb); ?>' />
    <?php
    $content = ob_get_clean();
    if ( $params['ajax'] ) {/* Ajax response after ajax call for filters and pagination.*/
      if ( $params['album_view_type'] != 'gallery' ) {
        parent::ajax_content($params, $bwg, $content);
      }
      else {
        echo $content;
      }
    }
    else {
      parent::container($params, $bwg, $content);
    }
  }

  private function inline_styles($bwg, $theme_row, $params) {
    ob_start();
    $rgb_album_compact_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_compact_thumbs_bg_color);
    ?>
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails {
    width: <?php echo ($params['items_col_num'] * $params['compuct_album_thumb_width']) + ($theme_row->compact_container_margin ? $theme_row->album_compact_thumb_margin : 0); ?>px;
    justify-content: <?php echo $theme_row->album_compact_thumb_align; ?>;
    <?php
    if ( $theme_row->album_compact_thumb_align == 'center' ) {
      ?>
        justify-content: center;
        margin:0 auto !important;
      <?php
    }
    elseif ( $theme_row->album_compact_thumb_align == 'left') {
      ?>
        justify-content: flex-start;
        margin-right:auto;
      <?php
    }
    else {
      ?>
        justify-content: flex-end;
        margin-left:auto;
      <?php
    }
    ?>
    background-color: rgba(<?php echo $rgb_album_compact_thumbs_bg_color['red']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['green']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['blue']; ?>, <?php echo number_format($theme_row->album_compact_thumb_bg_transparent / 100, 2, ".", ""); ?>);
    <?php
    if ( $theme_row->compact_container_margin ) {
      ?>
      padding-left: <?php echo $theme_row->album_compact_thumb_margin; ?>px;
      padding-top: <?php echo $theme_row->album_compact_thumb_margin; ?>px;
      max-width: 100%;
      <?php
    }
    else {
      ?>
      margin-right: -<?php echo $theme_row->album_compact_thumb_margin; ?>px;
      max-width: calc(100% + <?php echo $theme_row->album_compact_thumb_margin; ?>px);
      <?php
    }
    ?>
    }
    <?php
    if (!$theme_row->compact_container_margin && $theme_row->album_compact_thumb_margin) {
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-background-<?php echo $bwg; ?>.bwg-album-thumbnails {
      overflow: hidden;
      }
      <?php
    }
    ?>
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item {
    justify-content: <?php echo $theme_row->album_compact_thumb_title_pos == 'top'? 'flex-end' : 'flex-start'; ?>;
    max-width: <?php echo $params['compuct_album_thumb_width']; ?>px;
    <?php if ( !BWG()->options->resizable_thumbnails ) { ?>
      width: <?php echo $params['compuct_album_thumb_width']; ?>px !important;
    <?php } ?>
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item > a {
      margin-right: <?php echo $theme_row->album_compact_thumb_margin; ?>px;
      margin-bottom: <?php echo $theme_row->album_compact_thumb_margin; ?>px;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item0 {
      padding: <?php echo $theme_row->album_compact_thumb_padding; ?>px;
      <?php $thumb_bg_color = WDWLibrary::spider_hex2rgb( $theme_row->album_compact_thumb_bg_color ); ?>
      background-color:rgba(<?php echo $thumb_bg_color['red'] .','. $thumb_bg_color['green'] . ',' . $thumb_bg_color['blue'] . ', '.number_format($theme_row->album_compact_thumb_bg_transparency / 100, 2, ".", ""); ?>);
      border: <?php echo $theme_row->album_compact_thumb_border_width; ?>px <?php echo $theme_row->album_compact_thumb_border_style; ?> #<?php echo $theme_row->album_compact_thumb_border_color; ?>;
      opacity: <?php echo number_format($theme_row->album_compact_thumb_transparent / 100, 2, ".", ""); ?>;
      border-radius: <?php echo $theme_row->album_compact_thumb_border_radius; ?>;
      box-shadow: <?php echo $theme_row->album_compact_thumb_box_shadow; ?>;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item1 img {
      max-height: none;
      max-width: none;
      padding: 0 !important;
    }
    <?php if ( $theme_row->album_compact_thumb_hover_effect == 'zoom' ) { ?>
      @media only screen and (min-width: 480px) {
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item1 img {
      <?php echo ($theme_row->album_compact_thumb_transition) ? '-webkit-transition: all .3s; transition: all .3s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item1 img:hover {
        -ms-transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        -webkit-transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
      }
      <?php if ( $params['compuct_album_title'] == 'hover' ) { ?>
        .bwg-album-thumbnails .bwg-zoom-effect .bwg-zoom-effect-overlay {
        <?php $thumb_bg_color = WDWLibrary::spider_hex2rgb( $theme_row->album_compact_thumb_bg_color ); ?>
        background-color:rgba(<?php echo $thumb_bg_color['red'] .','. $thumb_bg_color['green'] . ',' . $thumb_bg_color['blue'] . ', 0.3'; ?>);
        }
        .bwg-album-thumbnails .bwg-zoom-effect:hover img {
        -ms-transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        -webkit-transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        transform: scale(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        }
      <?php } ?>
      }
      <?php
    }
    else {
      ?>
      @media only screen and (min-width: 480px) {
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item0 {
      <?php echo ($theme_row->album_compact_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item0:hover {
        -ms-transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
      }
      }
      <?php
    }
    ?>
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-item1 {
      padding-top: <?php echo $params['compuct_album_thumb_height'] / $params['compuct_album_thumb_width'] * 100; ?>%;
    }
    <?php
    /* Show image title on hover.*/
    if ( $params['compuct_album_title'] == 'hover' ) { ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-title1 {
        position: absolute;
        top: 0;
        z-index: 100;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-content: center;
        flex-direction: column;
        opacity: 0;
      }
      <?php
    }
    ?>
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-thumbnails .bwg-title2 {
      color: #<?php echo ( $params['compuct_album_title'] == 'hover') ? (isset($theme_row->album_compact_title_font_color_hover) ? $theme_row->album_compact_title_font_color_hover : $theme_row->album_compact_title_font_color) : $theme_row->album_compact_title_font_color; ?>;
      font-family: <?php echo $theme_row->album_compact_title_font_style; ?>;
      font-size: <?php echo $theme_row->album_compact_title_font_size; ?>px;
      font-weight: <?php echo $theme_row->album_compact_title_font_weight; ?>;
      padding: <?php echo $theme_row->album_compact_title_margin; ?>;
      text-shadow: <?php echo $theme_row->album_compact_title_shadow; ?>;
      max-height: 100%;
    }
    <?php

    /* Add gallery styles, if gallery type exist.*/
    if ( $this->gallery_view && method_exists($this->gallery_view, 'inline_styles') ) {
      /* Set parameters for gallery view from album shortcode.*/
      $params['thumb_width'] = $params['compuct_album_image_thumb_width'];
      $params['thumb_height'] = $params['compuct_album_image_thumb_height'];
      $params['image_title'] = $params['compuct_album_image_title'];

      $params['image_column_number'] = $params['compuct_album_image_column_number'];
      $params['images_per_page'] = $params['compuct_album_images_per_page'];
      $params['image_enable_page'] = $params['compuct_album_enable_page'];

      $params['masonry_hor_ver'] = 'vertical';
      $params['show_masonry_thumb_description'] = BWG()->options->show_masonry_thumb_description;

      $params['mosaic_hor_ver'] = $params['compuct_album_mosaic_hor_ver'];
      $params['resizable_mosaic'] = $params['compuct_album_resizable_mosaic'];
      $params['mosaic_total_width'] = $params['compuct_album_mosaic_total_width'];

      /* Set theme parameters for gallery view.*/
      $theme_row->thumbs_bg_color = $theme_row->album_compact_thumbs_bg_color;
      $theme_row->masonry_thumbs_bg_color = $theme_row->album_compact_thumbs_bg_color;
      $theme_row->mosaic_thumbs_bg_color = $theme_row->album_compact_thumbs_bg_color;

      $theme_row->container_margin = $theme_row->compact_container_margin;
      $theme_row->masonry_container_margin = $theme_row->compact_container_margin;

      $theme_row->thumb_margin = $theme_row->album_compact_thumb_margin;
      $theme_row->masonry_thumb_padding = $theme_row->album_compact_thumb_margin;

      $theme_row->thumb_padding = $theme_row->album_compact_thumb_padding;
      $theme_row->mosaic_thumb_padding = $theme_row->album_compact_thumb_padding;

      $theme_row->thumb_align = $theme_row->album_compact_thumb_align;
      $theme_row->masonry_thumb_align = $theme_row->album_compact_thumb_align;
      $theme_row->mosaic_thumb_align = $theme_row->album_compact_thumb_align;

      $theme_row->thumb_bg_transparent = $theme_row->album_compact_thumb_bg_transparent;
      $theme_row->masonry_thumb_transparent = $theme_row->album_compact_thumb_transparent;
      $theme_row->mosaic_thumb_bg_transparent = $theme_row->album_compact_thumb_bg_transparent;

      $theme_row->thumb_transparent = $theme_row->album_compact_thumb_transparent;
      $theme_row->mosaic_thumb_transparent = $theme_row->album_compact_thumb_transparent;

      $theme_row->thumb_title_pos = $theme_row->album_compact_thumb_title_pos;

      $theme_row->thumb_bg_color = $theme_row->album_compact_thumb_bg_color;
      $theme_row->masonry_thumbs_bg_color = $theme_row->album_compact_thumb_bg_color;

      $theme_row->thumb_border_width = $theme_row->album_compact_thumb_border_width;
      $theme_row->masonry_thumb_border_width = $theme_row->album_compact_thumb_border_width;
      $theme_row->mosaic_thumb_border_width = $theme_row->album_compact_thumb_border_width;

      $theme_row->thumb_border_style = $theme_row->album_compact_thumb_border_style;
      $theme_row->masonry_thumb_border_style = $theme_row->album_compact_thumb_border_style;
      $theme_row->mosaic_thumb_border_style = $theme_row->album_compact_thumb_border_style;

      $theme_row->thumb_border_color = $theme_row->album_compact_thumb_border_color;
      $theme_row->masonry_thumb_border_color = $theme_row->album_compact_thumb_border_color;
      $theme_row->mosaic_thumb_border_color = $theme_row->album_compact_thumb_border_color;

      $theme_row->thumb_border_radius = $theme_row->album_compact_thumb_border_radius;
      $theme_row->masonry_thumb_border_radius = $theme_row->album_compact_thumb_border_radius;
      $theme_row->mosaic_thumb_border_radius = $theme_row->album_compact_thumb_border_radius;

      $theme_row->thumb_box_shadow = $theme_row->album_compact_thumb_box_shadow;

      $theme_row->thumb_title_font_color_hover = $theme_row->album_compact_title_font_color_hover;
      $theme_row->thumb_title_font_color = $theme_row->album_compact_title_font_color;
      $theme_row->masonry_thumb_title_font_color_hover = $theme_row->album_compact_title_font_color_hover;
      $theme_row->masonry_thumb_title_font_color = $theme_row->album_compact_title_font_color;
      $theme_row->mosaic_thumb_title_font_color = $theme_row->album_compact_title_font_color;
      $theme_row->mosaic_thumb_title_font_color = $theme_row->album_compact_title_font_color;

      $theme_row->thumb_title_font_style = $theme_row->album_compact_title_font_style;
      $theme_row->masonry_thumb_title_font_style = $theme_row->album_compact_title_font_style;
      $theme_row->mosaic_thumb_title_font_style = $theme_row->album_compact_title_font_style;

      $theme_row->thumb_title_font_size = $theme_row->album_compact_title_font_size;
      $theme_row->masonry_thumb_title_font_size = $theme_row->album_compact_title_font_size;
      $theme_row->mosaic_thumb_title_font_size = $theme_row->album_compact_title_font_size;

      $theme_row->thumb_title_font_weight = $theme_row->album_compact_title_font_weight;
      $theme_row->masonry_thumb_title_font_weight = $theme_row->album_compact_title_font_weight;
      $theme_row->mosaic_thumb_title_font_weight = $theme_row->album_compact_title_font_weight;

      $theme_row->thumb_title_shadow = $theme_row->album_compact_title_shadow;
      $theme_row->mosaic_thumb_title_shadow = $theme_row->album_compact_title_shadow;
      if ( !in_array( $params['gallery_type'], array('slideshow', 'image_browser', 'blog_style', 'carousel') ) ) {
		echo $this->gallery_view->inline_styles($bwg, $theme_row, $params);
	  }
    }

    return ob_get_clean();
  }
}
