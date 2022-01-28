<?php

class BWGViewAlbum_extended_preview extends BWGViewSite {

  private $gallery_view = FALSE;

  public function display( $params = array(), $bwg = 0 ) {
    /* Gallery view class.*/
	  $gallery_type = 'Thumbnails';
    if ( $params['gallery_view_type'] == 'masonry' ) {
      $gallery_type = 'Thumbnails_masonry';
    }
    elseif ( $params['gallery_view_type'] == 'mosaic' ) {
      $gallery_type = 'Thumbnails_mosaic';
    }
    elseif (  $params['gallery_view_type'] == 'mosaic' ) {
      $gallery_type = 'Thumbnails_mosaic';
    }
    elseif (  $params['gallery_view_type'] == 'slideshow' ) {
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
    $theme_row->thumb_gal_title_font_size = $theme_row->album_extended_gal_title_font_size;
    $theme_row->thumb_gal_title_font_color = $theme_row->album_extended_gal_title_font_color;
    $theme_row->thumb_gal_title_font_style = $theme_row->album_extended_gal_title_font_style;
    $theme_row->thumb_gal_title_font_weight = $theme_row->album_extended_gal_title_font_weight;
    $theme_row->thumb_gal_title_shadow = $theme_row->album_extended_gal_title_shadow;
    $theme_row->thumb_gal_title_margin = $theme_row->album_extended_gal_title_margin;
    $theme_row->thumb_gal_title_align = $theme_row->album_extended_gal_title_align;

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
      <div data-max-count="<?php echo $params['extended_album_column_number']; ?>"
           data-thumbnail-width="<?php echo $params['extended_album_thumb_width']; ?>"
           data-global-spacing="<?php echo $theme_row->album_extended_div_margin; ?>"
           data-spacing="<?php echo $theme_row->album_extended_div_padding; ?>"
           data-bwg="<?php echo $bwg; ?>"
           id="<?php echo $params['container_id']; ?>"
           class="bwg-album-extended bwg-border-box bwg-thumbnails bwg-container bwg-container-<?php echo $bwg; ?> bwg-album-thumbnails bwg_album_extended_thumbnails_<?php echo $bwg; ?>">
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
          <div class="bwg-extended-item">
            <div class="bwg-extended-item0">
              <a class="bwg-a bwg-album bwg_album_<?php echo $bwg; ?>"
                <?php echo ( ($enable_seo || $enable_dynamic_url ) ? "href='" . esc_url($href) . "'" : ""); ?>
                 style="font-size: 0;"
                 data-bwg="<?php echo $bwg; ?>"
                 data-container_id="<?php echo $params['container_id']; ?>"
                 data-alb_gal_id="<?php echo (($params['album_gallery_id'] != 0) ? $row->alb_gal_id : $row->id); ?>"
                 data-def_type="<?php echo $row->def_type; ?>"
                 data-title="<?php echo htmlspecialchars(addslashes($row->name)); ?>">
                <div class="bwg-item0 bwg_album_thumb_<?php echo $bwg; ?> <?php echo ($lazyload) ? 'lazy_loader' : ''; ?>">
                  <div class="bwg-item1 bwg_album_thumb_spun1_<?php echo $bwg; ?>">
                    <div class="bwg-item2">
                      <img class="skip-lazy <?php if( $lazyload ) { ?> bwg_lazyload <?php } ?>"
                           data-width="<?php echo $image_thumb_width; ?>"
                           data-height="<?php echo $image_thumb_height; ?>"
                           data-original="<?php echo $row->preview_image; ?>"
                           src="<?php if( !$lazyload ) { echo $row->preview_image; } else { echo BWG()->plugin_url."/images/lazy_placeholder.gif"; } ?>"
                           alt="<?php echo $row->name; ?>" />
                    </div>
                  </div>
                </div>
              </a>
            </div>
            <div class="bwg-extended-item1">
              <?php
              if ( $row->name ) {
                ?>
                <a class="bwg-album bwg_album_<?php echo $bwg; ?>"
                   <?php echo ( ($enable_seo || $enable_dynamic_url) ? "href='" . esc_url($href) . "'" : "" ); ?>
                   data-bwg="<?php echo $bwg; ?>"
                   data-container_id="<?php echo $params['container_id']; ?>"
                   data-alb_gal_id="<?php echo(($params['album_gallery_id'] != 0) ? $row->alb_gal_id : $row->id); ?>"
                   data-def_type="<?php echo $row->def_type; ?>"
                   data-title="<?php echo htmlspecialchars(addslashes($row->name)); ?>">
                  <span class="bwg_title_spun_<?php echo $bwg; ?>"><?php echo $row->name; ?></span>
                </a>
                <?php
              }
              if ( $params['extended_album_description_enable'] && $row->description ) {
                if ( stripos($row->description, '<!--more-->') !== FALSE ) {
                  $description_array = explode('<!--more-->', $row->description);
                  $description_short = $description_array[0];
                  $description_full = $description_array[1];
                  ?>
                <span class="bwg_description_spun1_<?php echo $bwg; ?>">
                  <span class="bwg_description_spun2_<?php echo $bwg; ?>">
                    <span class="bwg_description_short_<?php echo $bwg; ?>">
                      <?php echo $description_short; ?>
                    </span>
                    <span class="bwg_description_full">
                      <?php echo $description_full; ?>
                    </span>
                  </span>
                  <span data-more-msg="<?php _e('More', BWG()->prefix); ?>"
                        data-hide-msg="<?php _e('Hide', BWG()->prefix); ?>"
                        class="bwg_description_more bwg_description_more_<?php echo $bwg; ?> bwg_more">
                    <?php _e('More', BWG()->prefix); ?>
                  </span>
                </span>
                  <?php
                }
                else {
                  ?>
                  <span class="bwg_description_spun1_<?php echo $bwg; ?>">
                <span class="bwg_description_short_<?php echo $bwg; ?>">
                  <?php echo $row->description; ?>
                </span>
              </span>
                  <?php
                }
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

  private function inline_styles( $bwg, $theme_row, $params ) {
    ob_start();
    $rgb_album_extended_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_extended_thumbs_bg_color);
    $rgb_album_extended_div_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_extended_div_bg_color);
    ?>
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumbnails_<?php echo $bwg; ?> {
      -moz-box-sizing: border-box;
      box-sizing: border-box;
      background-color: rgba(<?php echo $rgb_album_extended_thumbs_bg_color['red']; ?>, <?php echo $rgb_album_extended_thumbs_bg_color['green']; ?>, <?php echo $rgb_album_extended_thumbs_bg_color['blue']; ?>, <?php echo number_format($theme_row->album_extended_thumb_bg_transparent / 100, 2, ".", ""); ?>);
      text-align: <?php echo $theme_row->album_extended_thumb_align; ?>;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-extended-item {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      min-height: <?php echo $params['extended_album_height']; ?>px;
      border-bottom: <?php echo $theme_row->album_extended_div_separator_width; ?>px <?php echo $theme_row->album_extended_div_separator_style; ?> #<?php echo $theme_row->album_extended_div_separator_color; ?>;
      background-color: rgba(<?php echo $rgb_album_extended_div_bg_color['red']; ?>, <?php echo $rgb_album_extended_div_bg_color['green']; ?>, <?php echo $rgb_album_extended_div_bg_color['blue']; ?>, <?php echo number_format($theme_row->album_extended_div_bg_transparent / 100, 2, ".", ""); ?>);
      border-radius: <?php echo $theme_row->album_extended_div_border_radius; ?>;
      margin: <?php echo $theme_row->album_extended_div_margin; ?>;
      overflow: hidden;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-extended-item0 {
      margin: <?php echo $theme_row->album_extended_div_padding; ?>px <?php echo $theme_row->album_extended_div_padding / 2; ?>px;
      background-color: #<?php echo $theme_row->album_extended_thumb_div_bg_color; ?>;
      border-radius: <?php echo $theme_row->album_extended_thumb_div_border_radius; ?>;
      border: <?php echo $theme_row->album_extended_thumb_div_border_width; ?>px <?php echo $theme_row->album_extended_thumb_div_border_style; ?> #<?php echo $theme_row->album_extended_thumb_div_border_color; ?>;
      display: flex;
      flex-direction: column;
      padding: <?php echo $theme_row->album_extended_thumb_div_padding; ?>;
      justify-content: center;
      max-width: <?php echo $params['extended_album_thumb_width']; ?>px;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-extended-item1 {
      margin: <?php echo $theme_row->album_extended_div_padding; ?>px <?php echo $theme_row->album_extended_div_padding / 2; ?>px;
      background-color: #<?php echo $theme_row->album_extended_text_div_bg_color; ?>;
      border-radius: <?php echo $theme_row->album_extended_text_div_border_radius; ?>;
      border: <?php echo $theme_row->album_extended_text_div_border_width; ?>px <?php echo $theme_row->album_extended_text_div_border_style; ?> #<?php echo $theme_row->album_extended_text_div_border_color; ?>;
      display: flex;
      flex-direction: column;
      border-collapse: collapse;
      padding: <?php echo $theme_row->album_extended_text_div_padding; ?>;
      justify-content: <?php if ( $theme_row->album_extended_title_desc_alignment == 'top' ) { echo 'start'; } elseif ( $theme_row->album_extended_title_desc_alignment == 'bottom' ) { echo 'flex-end'; } else { echo 'center'; } ; ?>;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_title_spun_<?php echo $bwg; ?> {
      border: <?php echo $theme_row->album_extended_title_span_border_width; ?>px <?php echo $theme_row->album_extended_title_span_border_style; ?> #<?php echo $theme_row->album_extended_title_span_border_color; ?>;
      color: #<?php echo $theme_row->album_extended_title_font_color; ?>;
      display: block;
      font-family: <?php echo $theme_row->album_extended_title_font_style; ?>;
      font-size: <?php echo $theme_row->album_extended_title_font_size; ?>px;
      font-weight: <?php echo $theme_row->album_extended_title_font_weight; ?>;
      height: inherit;
      margin-bottom: <?php echo $theme_row->album_extended_title_margin_bottom; ?>px;
      padding: <?php echo $theme_row->album_extended_title_padding; ?>;
      text-align: left;
      vertical-align: middle;
      width: 100%;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_spun1_<?php echo $bwg; ?> a {
      color: #<?php echo $theme_row->album_extended_desc_font_color; ?>;
      font-size: <?php echo $theme_row->album_extended_desc_font_size; ?>px;
      font-weight: <?php echo $theme_row->album_extended_desc_font_weight; ?>;
      font-family: <?php echo $theme_row->album_extended_desc_font_style; ?>;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_spun1_<?php echo $bwg; ?> .bwg_description_short_<?php echo $bwg; ?> p {
      border: <?php echo $theme_row->album_extended_desc_span_border_width; ?>px <?php echo $theme_row->album_extended_desc_span_border_style; ?> #<?php echo $theme_row->album_extended_desc_span_border_color; ?>;
      display: inline-block;
      color: #<?php echo $theme_row->album_extended_desc_font_color; ?>;
      font-size: <?php echo $theme_row->album_extended_desc_font_size; ?>px;
      font-weight: <?php echo $theme_row->album_extended_desc_font_weight; ?>;
      font-family: <?php echo $theme_row->album_extended_desc_font_style; ?>;
      height: inherit;
      padding: <?php echo $theme_row->album_extended_desc_padding; ?>;
      vertical-align: middle;
      width: 100%;
      word-wrap: break-word;
      word-break: break-word;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_spun1_<?php echo $bwg; ?> * {
      margin: 0;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_spun2_<?php echo $bwg; ?> {
      float: left;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_short_<?php echo $bwg; ?> {
      display: inline;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_full {
      display: none;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg_description_more_<?php echo $bwg; ?> {
      clear: both;
      color: #<?php echo $theme_row->album_extended_desc_more_color; ?>;
      cursor: pointer;
      float: right;
      font-size: <?php echo $theme_row->album_extended_desc_more_size; ?>px;
      font-weight: normal;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item0 {
      padding: <?php echo $theme_row->album_extended_thumb_padding; ?>px;
      background-color: #<?php echo $theme_row->album_extended_thumb_bg_color; ?>;
      border-radius: <?php echo $theme_row->album_extended_thumb_border_radius; ?>;
      border: <?php echo $theme_row->album_extended_thumb_border_width; ?>px <?php echo $theme_row->album_extended_thumb_border_style; ?> #<?php echo $theme_row->album_extended_thumb_border_color; ?>;
      box-shadow: <?php echo $theme_row->album_extended_thumb_box_shadow; ?>;
      margin: <?php echo $theme_row->album_extended_thumb_margin; ?>px;
      opacity: <?php echo number_format($theme_row->album_extended_thumb_transparent / 100, 2, ".", ""); ?>;
    }
    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item1 {
      padding-top: <?php echo $params['extended_album_thumb_height'] / $params['extended_album_thumb_width'] * 100; ?>%;
    }
    <?php if ( $theme_row->album_extended_thumb_hover_effect == 'zoom' ) { ?>
      @media only screen and (min-width: 480px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item1 img {
          <?php echo ($theme_row->album_extended_thumb_transition) ? '-webkit-transition: all .3s; transition: all .3s;' : ''; ?>
        }
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item1 img:hover {
          -ms-transform: scale(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
          -webkit-transform: scale(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
          transform: scale(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
        }
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item0:hover {
          -ms-transform: none;
          -webkit-transform: none;
          transform: none;
        }
      }
      <?php
    }
    else {
      ?>
      @media only screen and (min-width: 480px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item0 {
          <?php echo ($theme_row->album_extended_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        }
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item0:hover {
          -ms-transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
          -webkit-transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
          transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
        }
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg-container-<?php echo $bwg; ?>.bwg-album-extended .bwg-item1 img:hover {
          -ms-transform: none;
          -webkit-transform: none;
          transform: none;
        }
      }
      <?php
    }

    /* Add gallery styles, if gallery type exist.*/
    if ( $this->gallery_view && method_exists($this->gallery_view, 'inline_styles') ) {
      /* Set parameters for gallery view from album shortcode.*/
      $params['thumb_width'] = $params['extended_album_image_thumb_width'];
      $params['thumb_height'] = $params['extended_album_image_thumb_height'];
      $params['image_title'] = $params['extended_album_image_title'];

      $params['image_enable_page'] = $params['extended_album_enable_page'];
      $params['images_per_page'] = $params['extended_albums_per_page'];
      $params['items_col_num'] = $params['extended_album_image_column_number'];

      $params['masonry_hor_ver'] = 'vertical';
      $params['show_masonry_thumb_description'] = BWG()->options->show_masonry_thumb_description;

      $params['mosaic_hor_ver'] = $params['extended_album_mosaic_hor_ver'];
      $params['resizable_mosaic'] = $params['extended_album_resizable_mosaic'];
      $params['mosaic_total_width'] = $params['extended_album_mosaic_total_width'];
	  if ( !in_array( $params['gallery_type'], array('slideshow', 'image_browser', 'blog_style', 'carousel') ) ) {
		echo $this->gallery_view->inline_styles($bwg, $theme_row, $params);
	  }
    }

    return ob_get_clean();
  }
}
