<?php
class BWGViewSlideshow extends BWGViewSite {

public function display($params = array(), $bwg = 0) {

  $theme_row = $params['theme_row'];
  $image_rows = $params['image_rows'];
  $image_rows = $image_rows['images'];
  $images_count = count($image_rows);
  $content = '';
  $lazyload = BWG()->options->lazyload_images;

  if ( $images_count ) {
    $filmstrip_direction = 'horizontal';
    if ( $theme_row->slideshow_filmstrip_pos == 'right' || $theme_row->slideshow_filmstrip_pos == 'left' ) {
      $filmstrip_direction = 'vertical';
    }
    $slideshow_effect = $params['slideshow_effect'];
    $enable_slideshow_autoplay = $params['enable_slideshow_autoplay'];
    $enable_slideshow_shuffle = $params['enable_slideshow_shuffle'];
    $enable_slideshow_ctrl = $params['enable_slideshow_ctrl'];
    $enable_slideshow_filmstrip = BWG()->is_pro ? $params['enable_slideshow_filmstrip'] : 0;
    $slideshow_filmstrip_height = 0;
    $slideshow_filmstrip_width = 0;
    if ( $enable_slideshow_filmstrip ) {
      $thumb_width = BWG()->options->thumb_width;
      $thumb_height = BWG()->options->thumb_height;
      if ( $filmstrip_direction == 'horizontal' ) {
        $slideshow_filmstrip_height = $params['slideshow_filmstrip_height'];
        $thumb_ratio = $thumb_width / $thumb_height;
        $slideshow_filmstrip_width = round($thumb_ratio * $slideshow_filmstrip_height);
      }
      else {
        $slideshow_filmstrip_width = $params['slideshow_filmstrip_height'];
        $thumb_ratio = $thumb_height / $thumb_width;
        $slideshow_filmstrip_height = round($thumb_ratio * $slideshow_filmstrip_width);
      }
    }
    $enable_image_title = $params['slideshow_enable_title'];
    $slideshow_title_position = explode('-', $params['slideshow_title_position']);
    $enable_image_description = $params['slideshow_enable_description'];
    $slideshow_description_position = explode('-', $params['slideshow_description_position']);
    $enable_slideshow_music = $params['enable_slideshow_music'];
    $slideshow_music_url = $params['slideshow_music_url'];
    /* Validate url. If not valid add upload url.*/
    $url = filter_var($slideshow_music_url, FILTER_SANITIZE_URL);
    if ( FALSE === filter_var($url, FILTER_VALIDATE_URL) ) {
      $slideshow_music_url = BWG()->upload_url . $slideshow_music_url;
    }
    $image_width = $params['slideshow_width'];
    $image_height = $params['slideshow_height'];
    $watermark_font_size = $params['watermark_font_size'];
    $watermark_font = $params['watermark_font'];
    $watermark_color = $params['watermark_color'];
    $watermark_opacity = $params['watermark_opacity'];
    $watermark_position = explode('-', $params['watermark_position']);
    $watermark_link = $params['watermark_link'];
    $watermark_url = $params['watermark_url'];
    $watermark_width = $params['watermark_width'];
    $watermark_height = $params['watermark_height'];
    $current_image_id = ($image_rows ? $image_rows[0]->id : 0);
    $play_pause_button_display = 'undefined';
    $filmstrip_thumb_margin = $theme_row->slideshow_filmstrip_thumb_margin;
    $margins_split = explode(" ", $filmstrip_thumb_margin);
    $temp_iterator = ($filmstrip_direction == 'horizontal' ? 1 : 0);
    if ( isset($margins_split[$temp_iterator]) ) {
      $filmstrip_thumb_margin_right = (int) $margins_split[$temp_iterator];
      if ( isset($margins_split[$temp_iterator + 2]) ) {
        $filmstrip_thumb_margin_left = (int) $margins_split[$temp_iterator + 2];
      }
      else {
        $filmstrip_thumb_margin_left = $filmstrip_thumb_margin_right;
      }
    }
    elseif ( isset($margins_split[0]) ) {
      $filmstrip_thumb_margin_right = (int) $margins_split[0];
      $filmstrip_thumb_margin_left = $filmstrip_thumb_margin_right;
    }
    $filmstrip_thumb_margin_hor = $filmstrip_thumb_margin_right + $filmstrip_thumb_margin_left;
    if ( !$enable_slideshow_filmstrip ) {
      if ( $theme_row->slideshow_filmstrip_pos == 'left' ) {
        $theme_row->slideshow_filmstrip_pos = 'top';
      }
      if ( $theme_row->slideshow_filmstrip_pos == 'right' ) {
        $theme_row->slideshow_filmstrip_pos = 'bottom';
      }
    }
    $left_or_top = 'left';
    $width_or_height = 'width';
    $outerWidth_or_outerHeight = 'outerWidth';
    if ( !($filmstrip_direction == 'horizontal') ) {
      $left_or_top = 'top';
      $width_or_height = 'height';
      $outerWidth_or_outerHeight = 'outerHeight';
    }
    $inline_style = $this->inline_styles($bwg, $theme_row, $params, $image_width, $image_height, $filmstrip_direction, $slideshow_filmstrip_height, BWG()->options, $left_or_top, $width_or_height, $filmstrip_thumb_margin_hor, $slideshow_filmstrip_width, $image_rows, $watermark_position, $slideshow_title_position, $slideshow_description_position, $watermark_height, $watermark_width, $watermark_opacity, $watermark_font_size, $watermark_font, $watermark_color, $enable_slideshow_filmstrip);
    if ( !WDWLibrary::elementor_is_active() ) {
      if ( BWG()->options->use_inline_stiles_and_scripts ) {
        wp_add_inline_style('bwg_frontend', $inline_style);
        if ( !wp_script_is('bwg_embed', 'done') ) {
        wp_print_scripts('bwg_embed');
        }
        if ( !wp_script_is('jquery-mobile', 'done') ) {
        wp_print_scripts('jquery-mobile');
        }
      }
      else {
        echo '<style id="bwg-style-' . $bwg . '">' . $inline_style . '</style>';
      }
    }
    else {
      echo '<style id="bwg-style-' . $bwg . '">' . $inline_style . '</style>';
    }

    $data = array();
    $data[$bwg] = array();
    foreach ( $image_rows as $key => $image_row ) {
      if ( $image_row->id == $current_image_id ) {
        $current_image_alt = $image_row->alt;
        $current_image_description = str_replace(array(
                                                   "\r\n",
                                                   "\n",
                                                   "\r"
                                                 ), esc_html('<br />'), $image_row->description);
      }
      $data[$bwg][$key]["id"] = $image_row->id;
      $data[$bwg][$key]["alt"] = htmlspecialchars(str_replace(array( "\r\n", "\n", "\r" ), esc_html('<br />'), esc_html($image_row->alt)), ENT_COMPAT | ENT_QUOTES);
      $data[$bwg][$key]["description"] =  htmlspecialchars(str_replace(array("\r\n", "\n", "\r"), esc_html('<br />'), esc_html($image_row->description)), ENT_QUOTES);
      $data[$bwg][$key]["filetype"] = $image_row->filetype;
      $data[$bwg][$key]["filename"] = htmlspecialchars(str_replace(array( "\r\n", "\n", "\r" ), esc_html('<br />'), esc_html($image_row->filename)), ENT_COMPAT | ENT_QUOTES);
      $data[$bwg][$key]["image_url"] = htmlspecialchars($image_row->image_url, ENT_COMPAT | ENT_QUOTES);
      $data[$bwg][$key]["thumb_url"] = htmlspecialchars($image_row->thumb_url, ENT_COMPAT | ENT_QUOTES);
      $data[$bwg][$key]["redirect_url"] = htmlspecialchars($image_row->redirect_url, ENT_COMPAT | ENT_QUOTES);
      $data[$bwg][$key]["date"] = $image_row->date;
      $data[$bwg][$key]["is_embed"] = (preg_match('/EMBED/', $image_row->filetype) == 1 ? TRUE : FALSE);
      $data[$bwg][$key]["is_embed_video"] = (((preg_match('/EMBED/', $image_row->filetype) == 1) && (preg_match('/_VIDEO/', $image_row->filetype) == 1)) ? TRUE : FALSE);
    }
    ob_start();
    $trans_dur = ((floatval($params['slideshow_interval'] ) < 4) && (floatval($params['slideshow_interval']) != 0)) ? (floatval($params['slideshow_interval']) * 1000) / 4 : (floatval($params['slideshow_effect_duration']) * 1000);
    $bwg_param = array(
      'bwg_source' => 'slider',
      'bwg_current_key' => isset($current_key) ? $current_key : '',
      'bwg_transition_duration' => $trans_dur,
      'bwg_trans_in_progress' => FALSE,
      'data' => $data[$bwg],
      'width_or_height' => $width_or_height,
      'filmstrip_thumb_margin_hor' => $filmstrip_thumb_margin_hor,
      'left_or_top' => $left_or_top,
      'outerWidth_or_outerHeight' => $outerWidth_or_outerHeight,
      'enable_slideshow_shuffle' => $enable_slideshow_shuffle,
      'lightbox_filmstrip_thumb_border_width' => $theme_row->lightbox_filmstrip_thumb_border_width,
      'thumb_click_action' => $params['thumb_click_action'],
      'thumb_link_target' => $params['thumb_link_target'],
      'upload_url' => BWG()->upload_url,
      'preload_images' => BWG()->options->preload_images,
      'slideshow_effect' => $slideshow_effect,
      'enable_slideshow_filmstrip' => $enable_slideshow_filmstrip,
      'event_stack' => '',
      'preload_images_count' => (int) BWG()->options->preload_images_count,
      'image_width' => $image_width,
      'image_height' => $image_height,
      'filmstrip_direction' => $filmstrip_direction,
      'slideshow_filmstrip_width' => $slideshow_filmstrip_width,
      'slideshow_filmstrip_height' => $slideshow_filmstrip_height,
      'slideshow_play_pause_btn_size' => $theme_row->slideshow_play_pause_btn_size,
      'watermark_type' => $params['watermark_width'],
      'watermark_height' => $watermark_height,
      'watermark_font_size' => $watermark_font_size,
      'slideshow_title_font_size' => $theme_row->slideshow_title_font_size,
      'slideshow_description_font_size' => $theme_row->slideshow_description_font_size,
      'bwg_playInterval' => '',
      'slideshow_interval' => $params['slideshow_interval'],
      'image_right_click' => BWG()->options->image_right_click,
      'enable_slideshow_autoplay' => $enable_slideshow_autoplay,
      'enable_slideshow_music' => $enable_slideshow_music,
    );
    ?>
  <div class="bwg_slideshow_image_wrap_<?php echo $bwg; ?> bwg-container"
       data-lightbox-url="<?php echo addslashes(add_query_arg($params['params_array'], admin_url('admin-ajax.php'))); ?>">
    <?php
    $current_pos = 0;
    if ( $enable_slideshow_filmstrip ) {
      ?>
      <div class="bwg_slideshow_filmstrip_container_<?php echo $bwg; ?>">
        <div class="bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>">
          <i class="<?php echo($filmstrip_direction == 'horizontal' ? 'bwg-icon-angle-left' : 'bwg-icon-angle-up'); ?>"></i>
        </div>
        <div class="bwg_slideshow_filmstrip_<?php echo $bwg; ?> bwg_slideshow_filmstrip">
          <div class="bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>">
            <?php
            foreach ( $image_rows as $key => $image_row ) {
              if ( $image_row->id == $current_image_id ) {
                $current_pos = $key * (($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_width : $slideshow_filmstrip_height) + $filmstrip_thumb_margin_hor);
                $current_key = $key;
              }
              $is_embed = preg_match('/EMBED/', $image_row->filetype) == 1 ? TRUE : FALSE;
              $is_embed_video = ($is_embed && preg_match('/_VIDEO/', $image_row->filetype) == 1) ? TRUE : FALSE;
              $is_embed_instagram = preg_match('/EMBED_OEMBED_INSTAGRAM/', $image_row->filetype) == 1 ? TRUE : FALSE;
              if ( $play_pause_button_display === 'undefined' ) {
                if ( $is_embed_video ) {
                  $play_pause_button_display = 'none';
                }
                else {
                  $play_pause_button_display = '';
                }
              }
              if ( !$is_embed ) {
                $thumb_path_url = htmlspecialchars_decode(BWG()->upload_dir . $image_row->thumb_url, ENT_COMPAT | ENT_QUOTES);
                $thumb_path_url = explode('?bwg', $thumb_path_url);
                list($image_thumb_width, $image_thumb_height) = getimagesize($thumb_path_url[0]);
              }
              else {
                if ( $image_row->resolution != '' ) {
                  if ( !$is_embed_instagram ) {
                    $resolution_arr = explode(" ", $image_row->resolution);
                    $resolution_w = intval($resolution_arr[0]);
                    $resolution_h = intval($resolution_arr[2]);
                    if ( $resolution_w != 0 && $resolution_h != 0 ) {
                      $scale = $scale = max($slideshow_filmstrip_width / $resolution_w, $slideshow_filmstrip_height / $resolution_h);
                      $image_thumb_width = $resolution_w * $scale;
                      $image_thumb_height = $resolution_h * $scale;
                    }
                    else {
                      $image_thumb_width = $slideshow_filmstrip_width;
                      $image_thumb_height = $slideshow_filmstrip_height;
                    }
                  }
                  else {
                    // this will be ok while instagram thumbnails width and height are the same
                    $image_thumb_width = min($slideshow_filmstrip_width, $slideshow_filmstrip_height);
                    $image_thumb_height = $image_thumb_width;
                  }
                }
                else {
                  $image_thumb_width = $slideshow_filmstrip_width;
                  $image_thumb_height = $slideshow_filmstrip_height;
                }
              }
              if ( is_null($image_thumb_width) || is_null($image_thumb_height) ) {
                $res = explode('x', $image_row->resolution_thumb);
                if ( !empty($res) && isset($res[1]) ) {
                  $image_thumb_width = $res[0];
                  $image_thumb_height = $res[1];
                }
              }
              $scale = max($slideshow_filmstrip_width / $image_thumb_width, $slideshow_filmstrip_height / $image_thumb_height);
              $image_thumb_width *= $scale;
              $image_thumb_height *= $scale;
              $thumb_left = ($slideshow_filmstrip_width - $image_thumb_width) / 2;
              $thumb_top = ($slideshow_filmstrip_height - $image_thumb_height) / 2;
              ?>
              <div id="bwg_filmstrip_thumbnail_<?php echo $key; ?>_<?php echo $bwg; ?>" class="bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?> <?php echo(($image_row->id == $current_image_id) ? 'bwg_slideshow_thumb_active_' . $bwg : 'bwg_slideshow_thumb_deactive_' . $bwg); ?>">
                <img style="width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;"
                     class="skip-lazy bwg_filmstrip_thumbnail_img bwg_slideshow_filmstrip_thumbnail_img_<?php echo $bwg; ?> <?php if( $lazyload ) { ?> bwg_lazyload <?php } ?>"
                     src="<?php if( !$lazyload ) { echo ($is_embed ? "" : BWG()->upload_url) . $image_row->thumb_url; } else { echo BWG()->plugin_url."/images/lazy_placeholder.gif"; } ?>"
                     data-original="<?php echo ($is_embed ? "" : BWG()->upload_url) . $image_row->thumb_url; ?>"
                     onclick="bwg_change_image(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), '<?php echo $key; ?>', '', '', '<?php echo $bwg; ?>')"
                     image_id="<?php echo $image_row->id; ?>"
                     image_key="<?php echo $key; ?>"
                     alt="<?php echo $image_row->alt; ?>" />
              </div>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>">
          <i class="<?php echo($filmstrip_direction == 'horizontal' ? 'bwg-icon-angle-right' : 'bwg-icon-angle-down'); ?>"></i>
        </div>
      </div>
      <?php
    }
    else {
      ?>
      <div class="bwg_slideshow_dots_container_<?php echo $bwg; ?>">
        <div class="bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>">
          <?php
          foreach ( $image_rows as $key => $image_row ) {
            if ( $image_row->id == $current_image_id ) {
              $current_pos = $key * ($slideshow_filmstrip_width + 2);
              $current_key = $key;
            }
            ?>
            <span id="bwg_dots_<?php echo $key; ?>_<?php echo $bwg; ?>" class="bwg_slideshow_dots_<?php echo $bwg; ?> <?php echo(($image_row->id == $current_image_id) ? 'bwg_slideshow_dots_active_' . $bwg : 'bwg_slideshow_dots_deactive_' . $bwg); ?>" onclick="bwg_change_image(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), '<?php echo $key; ?>', '', '', <?php echo $bwg; ?>)" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $key; ?>"></span>
            <?php
          }
          ?>
        </div>
      </div>
      <?php
    }
    $bwg_param['bwg_current_filmstrip_pos'] = $current_pos;
    $bwg_params = json_encode($bwg_param);
    ?>
    <div id="bwg_slideshow_image_container_<?php echo $bwg; ?>" class="bwg_slideshow_image_container_<?php echo $bwg; ?>" data-params='<?php echo $bwg_params; ?>'>
      <div class="bwg_slide_container_<?php echo $bwg; ?>">
        <div class="bwg_slide_bg_<?php echo $bwg; ?>">
          <div class="bwg_slider_<?php echo $bwg; ?>">
            <?php
            foreach ( $image_rows as $key => $image_row ) {
              $is_embed = preg_match('/EMBED/', $image_row->filetype) == 1 ? TRUE : FALSE;
              $is_embed_instagram_post = preg_match('/INSTAGRAM_POST/', $image_row->filetype) == 1 ? TRUE : FALSE;
              $is_embed_instagram_video = preg_match('/INSTAGRAM_VIDEO/', $image_row->filetype) == 1 ? TRUE : FALSE;
              if ( $image_row->id == $current_image_id ) {
                $current_key = $key;
                ?>
                <span class="bwg_slideshow_image_spun_<?php echo $bwg; ?>" id="image_id_<?php echo $bwg; ?>_<?php echo $image_row->id; ?>">
                    <span class="bwg_slideshow_image_spun1_<?php echo $bwg; ?>">
                      <span class="bwg_slideshow_image_spun2_<?php echo $bwg; ?>">
                        <?php
                        if ( !$is_embed ) {
                          ?>
                          <a <?php echo($params['thumb_click_action'] == 'open_lightbox' ? (' class="bwg-a bwg_lightbox"' . (BWG()->options->enable_seo ? ' href="' . ($is_embed ? $image_row->thumb_url : BWG()->upload_url . $image_row->image_url) . '"' : '') . ' data-image-id="' . $image_row->id . '"') : ('class="bwg-a" ' . ($params['thumb_click_action'] == 'redirect_to_url' && $image_row->redirect_url ? 'href="' . $image_row->redirect_url . '" target="' . ($params['thumb_link_target'] ? '_blank' : '') . '"' : ''))); ?>>
                          <img id="bwg_slideshow_image_<?php echo $bwg; ?>"
                               class="skip-lazy bwg_slide bwg_slideshow_image_<?php echo $bwg; ?> <?php if( $lazyload ) { ?> bwg_lazyload <?php } ?>"
                               src="<?php if( !$lazyload ) { echo BWG()->upload_url . $image_row->image_url; } else { echo BWG()->plugin_url."/images/lazy_placeholder.gif"; } ?>"
                               data-original="<?php echo BWG()->upload_url . $image_row->image_url; ?>"
                               image_id="<?php echo $image_row->id; ?>"
                               alt="<?php echo $image_row->alt; ?>" />
                          </a>
                          <?php
                        }
                        else {  /*$is_embed*/
                          ?>
                          <span id="bwg_slideshow_image_<?php echo $bwg; ?>" class="bwg_slideshow_embed_<?php echo $bwg; ?>" image_id="<?php echo $image_row->id; ?>">
                            <?php echo $is_embed_instagram_video ? '<span class="bwg_inst_play_btn_cont" onclick="bwg_play_instagram_video(this)" ><span class="bwg_inst_play"></span></span>' : '';
                            if ( $is_embed_instagram_post ) {
                              $post_width = $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0);
                              $post_height = $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0);
                              if ( $post_height < $post_width + 88 ) {
                                $post_width = $post_height - 88;
                              }
                              else {
                                $post_height = $post_width + 88;
                              }
                              $instagram_post_width = $post_width;
                              $instagram_post_height = $post_height;
                              $image_resolution = explode(' x ', $image_row->resolution);
                              if ( is_array($image_resolution) ) {
                                $instagram_post_width = $image_resolution[0];
                                $instagram_post_height = explode(' ', $image_resolution[1]);
                                $instagram_post_height = $instagram_post_height[0];
                              }
                              WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array(
                                'class' => "bwg_embed_frame_" . $bwg,
                                'data-width' => $instagram_post_width,
                                'data-height' => $instagram_post_height,
                                'frameborder' => "0",
                                'style' => "width:" . $post_width . "px; height:" . $post_height . "px; vertical-align:middle; display:inline-block; position:relative;"
                              ));
                            }
                            else {
                              WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array(
                                'class' => "bwg_embed_frame_" . $bwg,
                                'frameborder' => "0",
                                'allowfullscreen' => "allowfullscreen",
                                'style' => "width:inherit; height:inherit; vertical-align:middle; display:table-cell;"
                              ));
                            }
                            ?>
                          </span>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                  </span>
                <span class="bwg_slideshow_image_second_spun_<?php echo $bwg; ?>">
                  </span>
                <input type="hidden" id="bwg_current_image_key_<?php echo $bwg; ?>" value="<?php echo $key; ?>" />
                <?php
                break;
              }
              else {
                ?>
                <span class="bwg_slideshow_image_second_spun_<?php echo $bwg; ?>" id="image_id_<?php echo $bwg; ?>_<?php echo $image_row->id; ?>">
                    <span class="bwg_slideshow_image_spun1_<?php echo $bwg; ?>">
                      <span class="bwg_slideshow_image_spun2_<?php echo $bwg; ?>">
                        <?php
                        if ( !$is_embed ) {
                          ?>
                          <a <?php echo($params['thumb_click_action'] == 'open_lightbox' ? (' class="bwg-a bwg_lightbox_' . $bwg . '"' . (BWG()->options->enable_seo ? ' href="' . ($is_embed ? $image_row->thumb_url : BWG()->upload_url . $image_row->image_url) . '"' : '') . ' data-image-id="' . $image_row->id . '"') : ('class="bwg-a" ' . ($params['thumb_click_action'] == 'redirect_to_url' && $image_row->redirect_url ? 'href="' . $image_row->redirect_url . '" target="' . ($params['thumb_link_target'] ? '_blank' : '') . '"' : ''))) ?>>
                          <img id="bwg_slideshow_image_<?php echo $bwg; ?>"
                               class="skip-lazy  bwg_slide bwg_slideshow_image_<?php echo $bwg; ?> <?php if( $lazyload ) { ?> bwg_lazyload lazy_loader <?php } ?>"
                               src="<?php if( !$lazyload ) { echo BWG()->upload_url . $image_row->image_url; } else { echo BWG()->plugin_url."/images/lazy_placeholder.gif"; } ?>"
                               data-original="<?php echo BWG()->upload_url . $image_row->image_url; ?>"
                               image_id="<?php echo $image_row->id; ?>"
                               alt="<?php echo $image_row->alt; ?>" />
                          </a>
                          <?php
                        }
                        else {   /*$is_embed*/ ?>
                          <span class="bwg_slideshow_embed_<?php echo $bwg; ?>">
                              <?php
                              if ( $is_embed_instagram_post ) {
                                $post_width = $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0);
                                $post_height = $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0);
                                if ( $post_height < $post_width + 88 ) {
                                  $post_width = $post_height - 88;
                                }
                                else {
                                  $post_height = $post_width + 88;
                                }
                                $instagram_post_width = $post_width;
                                $instagram_post_height = $post_height;
                                $image_resolution = explode(' x ', $image_row->resolution);
                                if ( is_array($image_resolution) ) {
                                  $instagram_post_width = $image_resolution[0];
                                  $instagram_post_height = explode(' ', $image_resolution[1]);
                                  $instagram_post_height = $instagram_post_height[0];
                                }
                                WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array(
                                  'class' => "bwg_embed_frame_" . $bwg,
                                  'data-width' => $instagram_post_width,
                                  'data-height' => $instagram_post_height,
                                  'frameborder' => "0",
                                  'style' => "width:" . $post_width . "px; height:" . $post_height . "px; vertical-align:middle; display:inline-block; position:relative;"
                                ));
                              }
                              else {
                                WDWLibraryEmbed::display_embed($image_row->filetype, $image_row->image_url, $image_row->filename, array(
                                  'class' => "bwg_embed_frame_" . $bwg,
                                  'frameborder' => "0",
                                  'allowfullscreen' => "allowfullscreen",
                                  'style' => "width:inherit; height:inherit; vertical-align:middle; display:table-cell;"
                                ));
                              }
                              ?>
                          </span>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                  </span>
                <?php
              }
            }
            ?>
          </div>
        </div>
      </div>
      <?php
      if ( $enable_slideshow_ctrl ) {
        ?>
        <a class="bwg-a" id="spider_slideshow_left_<?php echo $bwg; ?>" onclick="bwg_change_image(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) + <?php echo count($data[$bwg]); ?> - bwg_iterator(<?php echo $bwg; ?>)) % <?php echo count($data[$bwg]); ?>, '', '', <?php echo $bwg; ?>); return false;"><span id="spider_slideshow_left-ico_<?php echo $bwg; ?>"><span><i class="<?php echo $theme_row->slideshow_rl_btn_style; ?>-left bwg_slideshow_prev_btn_<?php echo $bwg; ?>"></i></span></span></a>
        <span id="bwg_slideshow_play_pause_<?php echo $bwg; ?>" class="bwg_slideshow_play_pause" style="display: <?php echo $play_pause_button_display; ?>;"><span><span id="bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>"><i class="bwg-icon-play bwg_ctrl_btn_<?php echo $bwg; ?> bwg_slideshow_play_pause_<?php echo $bwg; ?>"></i></span></span></span>
        <a class="bwg-a" id="spider_slideshow_right_<?php echo $bwg; ?>" onclick="bwg_change_image(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) + bwg_iterator(<?php echo $bwg; ?>)) % <?php echo count($data[$bwg]); ?>, '', '', <?php echo $bwg; ?>); return false;"><span id="spider_slideshow_right-ico_<?php echo $bwg; ?>"><span><i class="<?php echo $theme_row->slideshow_rl_btn_style; ?>-right bwg_slideshow_next_btn_<?php echo $bwg; ?>"></i></span></span></a>
        <?php
      }
      ?>
    </div>
    <?php
    if ( $params['watermark_type'] != 'none' ) {
      ?>
      <div class="bwg_slideshow_image_container_<?php echo $bwg; ?> bwg_slideshow_image_container" data-params="<?php echo $bwg_params; ?>">
        <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
          <div>
              <span class="bwg_slideshow_watermark_spun_<?php echo $bwg; ?>" id="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
                <?php
                if ( $params['watermark_type'] == 'image' ) {
                  ?>
                  <a class="bwg-a" href="<?php echo urldecode($watermark_link); ?>" target="_blank">
                  <img class="bwg_slideshow_watermark_image_<?php echo $bwg; ?> bwg_slideshow_watermark_<?php echo $bwg; ?>" src="<?php echo urldecode($watermark_url); ?>" />
                </a>
                  <?php
                }
                elseif ( $params['watermark_type'] == 'text' ) {
                  ?>
                  <a class="bwg_none_selectable_<?php echo $bwg; ?> bwg_slideshow_watermark_text_<?php echo $bwg; ?> bwg_slideshow_watermark_<?php echo $bwg; ?>" target="_blank" href="<?php echo urldecode($watermark_link); ?>"><?php echo $params['watermark_text']; ?></a>
                  <?php
                }
                ?>
              </span>
          </div>
        </div>
      </div>
      <?php
    }
    if ( $enable_image_title ) {
      ?>
      <div class="bwg_slideshow_image_container_<?php echo $bwg; ?> bwg_slideshow_image_container" data-params="<?php echo $bwg_params; ?>">
        <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
          <div>
              <span class="bwg_slideshow_title_spun_<?php echo $bwg; ?>">
                <div class="bwg_slideshow_title_text_<?php echo $bwg; ?> <?php if ( !$current_image_alt ) {
                  echo 'bwg-hidden';
                } ?>">
                  <?php echo html_entity_decode($current_image_alt); ?>
                </div>
              </span>
          </div>
        </div>
      </div>
      <?php
    }
    if ( $enable_image_description && isset($current_image_description) ) {
      ?>
      <div class="bwg_slideshow_image_container_<?php echo $bwg; ?> bwg_slideshow_image_container" data-params="<?php echo $bwg_params; ?>">
        <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
          <div>
              <span class="bwg_slideshow_description_spun_<?php echo $bwg; ?>">
                <div class="bwg_slideshow_description_text_<?php echo $bwg; ?> <?php if ( !$current_image_description ) {
                  echo 'bwg-hidden';
                } ?>">
                  <?php echo html_entity_decode(str_replace("\r\n", esc_html('<br />'), $current_image_description)); ?>
                </div>
              </span>
          </div>
        </div>
      </div>
      <?php
    }
    if ( $enable_slideshow_music ) {
      ?>
      <audio id="bwg_audio_<?php echo $bwg; ?>" src="<?php echo $slideshow_music_url ?>" loop volume="1.0"></audio>
      <?php
    }
    ?>
  </div>
  <?php
    $content = ob_get_clean();
  }

	if ( $params['ajax'] ) { /* Ajax response after ajax call for filters and pagination.*/
      parent::ajax_content($params, $bwg, $content);
    }
    else {
      parent::container($params, $bwg, $content);
    }
}

public function inline_styles($bwg, $theme_row, $params, $image_width, $image_height, $filmstrip_direction, $slideshow_filmstrip_height, $options, $left_or_top, $width_or_height, $filmstrip_thumb_margin_hor, $slideshow_filmstrip_width, $image_rows, $watermark_position, $slideshow_title_position, $slideshow_description_position, $watermark_height, $watermark_width, $watermark_opacity, $watermark_font_size, $watermark_font, $watermark_color, $enable_slideshow_filmstrip) {
  ob_start();
  ?>
  #bwg_container1_<?php echo $bwg; ?> {
	/*visibility: hidden;*/
  }
  #bwg_container1_<?php echo $bwg; ?> * {
	  -moz-user-select: none;
	  -khtml-user-select: none;
	  -webkit-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_wrap_<?php echo $bwg; ?> {
	  background-color: #<?php echo $theme_row->slideshow_cont_bg_color; ?>;
	  width: <?php echo $image_width; ?>px;
	  height: <?php echo $image_height; ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_<?php echo $bwg; ?> {
	  max-width: <?php echo $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0); ?>px;
	  max-height: <?php echo $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_embed_<?php echo $bwg; ?> {
	width: <?php echo $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0); ?>px;
	height: <?php echo $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause_<?php echo $bwg; ?> {
	background: transparent url("<?php echo BWG()->plugin_url . '/images/blank.gif'; ?>") repeat scroll 0 0;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause-ico_<?php echo $bwg; ?> {
	color: #<?php echo $theme_row->slideshow_rl_btn_color; ?>;
	font-size: <?php echo $theme_row->slideshow_play_pause_btn_size; ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>:hover {
	color: #<?php echo $theme_row->slideshow_close_rl_btn_hover_color; ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left_<?php echo $bwg; ?>,
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right_<?php echo $bwg; ?> {
	background: transparent url("<?php echo BWG()->plugin_url . '/images/blank.gif'; ?>") repeat scroll 0 0;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left-ico_<?php echo $bwg; ?>,
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right-ico_<?php echo $bwg; ?> {
	  background-color: #<?php echo $theme_row->slideshow_rl_btn_bg_color; ?>;
	  border-radius: <?php echo $theme_row->slideshow_rl_btn_border_radius; ?>;
	  border: <?php echo $theme_row->slideshow_rl_btn_border_width; ?>px <?php echo $theme_row->slideshow_rl_btn_border_style; ?> #<?php echo $theme_row->slideshow_rl_btn_border_color; ?>;
	  box-shadow: <?php echo $theme_row->slideshow_rl_btn_box_shadow; ?>;
	  color: #<?php echo $theme_row->slideshow_rl_btn_color; ?>;
	  height: <?php echo $theme_row->slideshow_rl_btn_height; ?>px;
	  font-size: <?php echo $theme_row->slideshow_rl_btn_size; ?>px;
	  width: <?php echo $theme_row->slideshow_rl_btn_width; ?>px;
	  opacity: <?php echo number_format($theme_row->slideshow_close_btn_transparent / 100, 2, ".", ""); ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left-ico_<?php echo $bwg; ?>:hover,
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right-ico_<?php echo $bwg; ?>:hover {
	  color: #<?php echo $theme_row->slideshow_close_rl_btn_hover_color; ?>;
  }
  <?php
  if ( $params['autohide_slideshow_navigation'] ) {
    ?>
    #spider_slideshow_left-ico_<?php echo $bwg; ?>{
      left: -9999px;
    }
    #spider_slideshow_right-ico_<?php echo $bwg; ?>{
      left: -9999px;
    }
    <?php
  }
  else {
    ?>
    #spider_slideshow_left-ico_<?php echo $bwg; ?>{
      left: 20px;
    }
    #spider_slideshow_right-ico_<?php echo $bwg; ?>{
      left: auto;
      right: 20px;
    }
    <?php
  }
  ?>
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_container_<?php echo $bwg; ?> {
	  <?php echo $theme_row->slideshow_filmstrip_pos; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : $slideshow_filmstrip_width); ?>px;
	  width: <?php echo $image_width; ?>px;
	  height: <?php echo $image_height; ?>px;
	  }
	  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_container_<?php echo $bwg; ?> {
	  display: <?php echo ($filmstrip_direction == 'horizontal'? 'table' : 'block'); ?>;
	  height: <?php echo ($filmstrip_direction == 'horizontal'? $slideshow_filmstrip_height : $image_height); ?>px;
	  width: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width : $slideshow_filmstrip_width); ?>px;
	  <?php echo $theme_row->slideshow_filmstrip_pos; ?>: 0;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_<?php echo $bwg; ?> {
	  <?php echo $left_or_top; ?>: 20px;
	  <?php echo $width_or_height; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width - 40 : $image_height - 40); ?>px;
	  /*z-index: 10106;*/
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?> {
	  height: <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : ($slideshow_filmstrip_height + $filmstrip_thumb_margin_hor) * count($image_rows)); ?>px;
	  <?php echo $left_or_top; ?>: 0px;
	  width: <?php echo ($filmstrip_direction == 'horizontal' ? ($slideshow_filmstrip_width + $filmstrip_thumb_margin_hor) * count($image_rows) : $slideshow_filmstrip_width); ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?> {
	  border: <?php echo $theme_row->slideshow_filmstrip_thumb_border_width; ?>px <?php echo $theme_row->slideshow_filmstrip_thumb_border_style; ?> #<?php echo $theme_row->slideshow_filmstrip_thumb_border_color; ?>;
	  border-radius: <?php echo $theme_row->slideshow_filmstrip_thumb_border_radius; ?>;
	  height: <?php echo $slideshow_filmstrip_height; ?>px;
	  margin: <?php echo $theme_row->slideshow_filmstrip_thumb_margin; ?>;
	  width: <?php echo $slideshow_filmstrip_width; ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_thumb_active_<?php echo $bwg; ?> {
	  border: <?php echo $theme_row->slideshow_filmstrip_thumb_active_border_width; ?>px solid #<?php echo $theme_row->slideshow_filmstrip_thumb_active_border_color; ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_thumb_deactive_<?php echo $bwg; ?> {
    opacity: <?php echo number_format($theme_row->slideshow_filmstrip_thumb_deactive_transparent / 100, 2, ".", ""); ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_left_<?php echo $bwg; ?> {
	  background-color: #<?php echo $theme_row->slideshow_filmstrip_rl_bg_color; ?>;
	  display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
	  <?php echo $width_or_height; ?>: 20px;
	  <?php echo $left_or_top; ?>: 0;
	  <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'position: absolute;') ?>
	  <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?>
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_right_<?php echo $bwg; ?> {
	  background-color: #<?php echo $theme_row->slideshow_filmstrip_rl_bg_color; ?>;
	  <?php echo($filmstrip_direction == 'horizontal' ? 'right' : 'bottom') ?>: 0;
	  <?php echo $width_or_height; ?>: 20px;
	  display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
	  <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'position: absolute;') ?>
	  <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?>
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_left_<?php echo $bwg; ?> i,
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_right_<?php echo $bwg; ?> i {
	  color: #<?php echo $theme_row->slideshow_filmstrip_rl_btn_color; ?>;
	  font-size: <?php echo $theme_row->slideshow_filmstrip_rl_btn_size; ?>px;
  }

  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_spun_<?php echo $bwg; ?> {
	  text-align: <?php echo $watermark_position[1]; ?>;
	  vertical-align: <?php echo $watermark_position[0]; ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_title_spun_<?php echo $bwg; ?> {
	  text-align: <?php echo $slideshow_title_position[1]; ?>;
	  vertical-align: <?php echo $slideshow_title_position[0]; ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_spun_<?php echo $bwg; ?> {
	  text-align: <?php echo $slideshow_description_position[1]; ?>;
	  vertical-align: <?php echo $slideshow_description_position[0]; ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_image_<?php echo $bwg; ?> {
	  max-height: <?php echo $watermark_height; ?>px;
	  max-width: <?php echo $watermark_width; ?>px;
	  opacity: <?php echo number_format($watermark_opacity / 100, 2, ".", ""); ?>;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_text_<?php echo $bwg; ?>,
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_text_<?php echo $bwg; ?>:hover {
	  text-decoration: none;
	  margin: 4px;
	  position: relative;
	  z-index: 15;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_title_text_<?php echo $bwg; ?> {
	  font-size: <?php echo $theme_row->slideshow_title_font_size; ?>px;
	  font-family: <?php echo $theme_row->slideshow_title_font; ?>;
	  color: #<?php echo $theme_row->slideshow_title_color; ?> !important;
	  opacity: <?php echo number_format($theme_row->slideshow_title_opacity / 100, 2, ".", ""); ?>;
    border-radius: <?php echo $theme_row->slideshow_title_border_radius; ?>;
	  background-color: #<?php echo $theme_row->slideshow_title_background_color; ?>;
	  padding: <?php echo $theme_row->slideshow_title_padding; ?>;
	  <?php if($params['slideshow_title_full_width']) { ?>
		width: 100%;
	  <?php } else { ?>
		margin: 5px;
	  <?php } ?>
	  <?php if (!$enable_slideshow_filmstrip && $slideshow_title_position[0] == $theme_row->slideshow_filmstrip_pos) echo $theme_row->slideshow_filmstrip_pos . ':' . ($theme_row->slideshow_dots_height + 4) . 'px;'; ?>
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_text_<?php echo $bwg; ?> {
	  font-size: <?php echo $theme_row->slideshow_description_font_size; ?>px;
	  font-family: <?php echo $theme_row->slideshow_description_font; ?>;
	  color: #<?php echo $theme_row->slideshow_description_color; ?> !important;
	  opacity: <?php echo number_format($theme_row->slideshow_description_opacity / 100, 2, ".", ""); ?>;
    border-radius: <?php echo $theme_row->slideshow_description_border_radius; ?>;
	  background-color: #<?php echo $theme_row->slideshow_description_background_color; ?>;
	  padding: <?php echo $theme_row->slideshow_description_padding; ?>;
	  <?php if (!$enable_slideshow_filmstrip && $slideshow_description_position[0] == $theme_row->slideshow_filmstrip_pos) echo $theme_row->slideshow_filmstrip_pos . ':' . ($theme_row->slideshow_dots_height + 4) . 'px;'; ?>
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_text_<?php echo $bwg; ?> * {
	text-decoration: none;
	color: #<?php echo $theme_row->slideshow_description_color; ?> !important;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_<?php echo $bwg; ?> {
	  width: <?php echo $theme_row->slideshow_dots_width; ?>px;
	  height: <?php echo $theme_row->slideshow_dots_height; ?>px;
	  border-radius: <?php echo $theme_row->slideshow_dots_border_radius; ?>;
	  background: #<?php echo $theme_row->slideshow_dots_background_color; ?>;
	  margin: <?php echo $theme_row->slideshow_dots_margin; ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_container_<?php echo $bwg; ?> {
	  width: <?php echo $image_width; ?>px;
	  <?php echo $theme_row->slideshow_filmstrip_pos; ?>: 0;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?> {
	  height: <?php echo ($theme_row->slideshow_dots_height + $theme_row->slideshow_dots_margin * 2); ?>px;
	  width: <?php echo ($theme_row->slideshow_dots_width + $theme_row->slideshow_dots_margin * 2) * count($image_rows); ?>px;
  }
  #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_active_<?php echo $bwg; ?> {
	  background: #<?php echo $theme_row->slideshow_dots_active_background_color; ?>;
	  border: <?php echo $theme_row->slideshow_dots_active_border_width; ?>px solid #<?php echo $theme_row->slideshow_dots_active_border_color; ?>;
  }
  <?php
  return ob_get_clean();
}
}