<?php

class OptionsView_bwg extends AdminView_bwg {

  public function display($params = array()) {
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script(BWG()->prefix . '_admin');
    wp_admin_css('thickbox');
    wp_enqueue_style(BWG()->prefix . '_tables');
    wp_enqueue_script(BWG()->prefix . '_jscolor');
    if ( WDWLibrary::get('bwg_start_tour') ) {
      update_user_meta(get_current_user_id(), 'bwg_photo_gallery', '1');
      WDWLibrary::spider_redirect('admin.php?page=options_bwg');
    }
    ob_start();
    echo $this->body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_options_form',
      'name' => BWG()->prefix . '_options_form',
      'class' => BWG()->prefix . '_options_form wd-form wp-core-ui js bwg-hidden',
      'action' => add_query_arg( array('page' => $params['page'] ), admin_url('admin.php')),
    );
    echo $this->form(ob_get_clean(), $form_attr);
  }

  public function body($params = array()) {
    $row = $params['row'];
	  $instagram_return_url = $params['instagram_return_url'];
    $instagram_reset_href = $params['instagram_reset_href'];
    $options_url_ajax = $params['options_url_ajax'];
    $imgcount = $params['imgcount'];
    if (!$row) {
      echo WDWLibrary::message_id(2);
      return;
    }
    $permissions = $params['permissions'];

    $built_in_watermark_fonts = $params['built_in_watermark_fonts'];
    $watermark_fonts = $params['watermark_fonts'];
    $gallery_types_name = $params['gallery_types_name'];
    $album_types_name = $params['album_types_name'];
    $buttons = array(
      'save' => array(
        'id' => 'bwg_save_options',
        'title' => __('Save options', BWG()->prefix),
        'onclick' => 'spider_set_input_value("task", "save")',
        'class' => 'tw-button-primary',
      ),
      'reset' => array(
        'id' => 'bwg_save_options',
        'title' => __('Reset', BWG()->prefix),
        'onclick' => 'if (confirm("' . addslashes(__('Do you want to reset to default?', BWG()->prefix)) . '")) {
                                                                 spider_set_input_value("task", "reset");
                                                               } else {
                                                                 return false;
                                                               }',
        'class' => 'tw-button-secondary',
      ),
    );
    echo $this->title( array(
        'title' => $params['page_title'],
        'title_class' => 'wd-header',
        'buttons' => $buttons,
      )
    );

    ?>
    <div class="bwg_tabs">
      <div id="search_in_tablet">
        <div id="div_search_in_options_tablets">
          <input type="text" class="search_in_options" placeholder="Search">
          <span class="current_match"></span>
          <span class="total_matches"></span>
          <span class="tablenav-pages-navspan tablenav-pages-navspan-search search_prev" aria-hidden="true"><img src="<?php echo BWG()->plugin_url . '/images/icons/up_arrow.svg'; ?>"></span>
          <span class="tablenav-pages-navspan tablenav-pages-navspan-search search_next" aria-hidden="true"><img src="<?php echo BWG()->plugin_url . '/images/icons/down_arrow.svg'; ?>"></span>
          <span class="search_close"><img src="<?php echo BWG()->plugin_url . '/images/icons/close_search.svg'; ?>"></span>
        </div>
        <div id='search_in_options_container' class="top">
          <ul class="bwg-tabs">
            <li class="tabs">
              <a href="#bwg_tab_general_content" class="bwg-tablink"><?php _e('General', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_general_content_bage"></div>
            </li>
            <li class="tabs">
              <a href="#bwg_tab_gallery_content" class="bwg-tablink"><?php _e('Gallery views', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_gallery_content_bage"></div>
            </li>
            <li class="tabs">
              <a href="#bwg_tab_gallery_group_content" class="bwg-tablink"><?php _e('Group of gallery views', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_gallery_group_content_bage"></div>
            </li>
            <li class="tabs">
              <a href="#bwg_tab_lightbox_content" class="bwg-tablink"><?php _e('Lightbox', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_lightbox_content_bage"></div>
            </li>
            <li class="tabs">
              <a href="#bwg_tab_watermark_content" class="bwg-tablink"><?php _e('Watermark', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_watermark_content_bage"></div>
            </li>
            <li class="tabs">
              <a href="#bwg_tab_advanced_content" class="bwg-tablink"><?php _e('Advanced', BWG()->prefix); ?>
                <a href="#bwg_tab_general_content" class="bwg-tablink-bottom"></a>
              </a>
              <div class='search_count' id="bwg_tab_advanced_content_bage"></div>
            </li>
          </ul>
          <div id="div_search_in_options">
            <input type="text" class="search_in_options" placeholder="Search">
            <span class="current_match"></span>
            <span class="total_matches"></span>
            <span class="tablenav-pages-navspan tablenav-pages-navspan-search search_prev" aria-hidden="true"><img src="<?php echo BWG()->plugin_url . '/images/icons/up_arrow.svg'; ?>"></span>
            <span class="tablenav-pages-navspan tablenav-pages-navspan-search search_next" aria-hidden="true"><img src="<?php echo BWG()->plugin_url . '/images/icons/down_arrow.svg'; ?>"></span>
            <span class="search_close"><img src="<?php echo BWG()->plugin_url . '/images/icons/close_search.svg'; ?>"></span>
          </div>
        </div>
      </div>
      <div id="bwg_tab_general_content" class="search-div bwg-section wd-box-content">
        <div class="bwg-section bwg-flex-wrap">
          <div class="wd-box-content wd-width-100 bwg-flex-wrap">
            <div class="wd-box-content wd-width-50">
              <?php
              if ( $row->images_directory !== 'wp-content/uploads' ) {
                ?>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label" for="images_directory"><?php _e('Images directory', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input id="images_directory" name="images_directory" type="text" style="display:inline-block; width:100%;" value="<?php echo $row->images_directory; ?>" />
                    <input type="hidden" id="old_images_directory" name="old_images_directory" value="<?php echo $row->old_images_directory; ?>" />
                  </div>
                  <p class="description"><?php _e('Provide the path of an existing folder inside the WordPress directory of your website to store uploaded images.<br />The content of the previous directory will be moved to the new one.', BWG()->prefix); ?></p>
                </div>
              </div>
                <?php
              }
              ?>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Image click action', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="thumb_click_action" id="thumb_click_action_1" value="open_lightbox" <?php if ($row->thumb_click_action == 'open_lightbox') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_1" class="wd-radio-label"><?php _e('Open lightbox', BWG()->prefix); ?></label>
                    <input type="radio" name="thumb_click_action" id="thumb_click_action_2" value="redirect_to_url" <?php if ($row->thumb_click_action == 'redirect_to_url') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_2" class="wd-radio-label"><?php _e('Redirect to url', BWG()->prefix); ?></label>
                    <input type="radio" name="thumb_click_action" id="thumb_click_action_3" value="do_nothing" <?php if ($row->thumb_click_action == 'do_nothing') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_3" class="wd-radio-label"><?php _e('Do Nothing', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Select the action which runs after clicking on gallery thumbnails.', BWG()->prefix); ?></p>
                </div>
                <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-redirect" id="tr_thumb_link_target">
                  <div class="wd-group">
                    <label class="wd-label"><?php _e('Open in a new window', BWG()->prefix); ?></label>
                    <div class="bwg-flex">
                      <input type="radio" name="thumb_link_target" id="thumb_link_target_yes" value="1" <?php if ($row->thumb_link_target) echo 'checked="checked"'; ?> /><label for="thumb_link_target_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                      <input type="radio" name="thumb_link_target" id="thumb_link_target_no" value="0" <?php if (!$row->thumb_link_target) echo 'checked="checked"'; ?> /><label for="thumb_link_target_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label" for="upload_img_width"><?php _e('Image dimensions', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="upload_img_width" id="upload_img_width" value="<?php echo $row->upload_img_width; ?>" min="0" /><span>x</span>
                    <input type="number" name="upload_img_height" id="upload_img_height" value="<?php echo $row->upload_img_height; ?>" min="0" /><span>px</span>
                  </div>
                  <p class="description"><?php _e('Specify the maximum dimensions of uploaded images (set 0 for original size).', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label" for="upload_thumb_width"><?php _e('Generated thumbnail dimensions', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="upload_thumb_width" id="upload_thumb_width" value="<?php echo $row->upload_thumb_width; ?>" min="0" /><span>x</span>
                    <input type="number" name="upload_thumb_height" id="upload_thumb_height" value="<?php echo $row->upload_thumb_height; ?>" min="0" /><span>px</span>
                    <input type="hidden" name="imgcount" id="bwg_imgcount" value="<?php echo $imgcount; ?>">
                    <input type="submit" class="button-primary" onclick="<?php echo (BWG()->is_demo ? 'alert(\'' . addslashes(__('This option is disabled in demo.', BWG()->prefix)) . '\'); return false;' : (BWG()->wp_editor_exists ? 'return bwg_recreate_thumb(0);' : 'alert(\'' . addslashes(__('Image edit functionality is not supported by your web host.', BWG()->prefix)) . '\'); return false;')); ?>" value="<?php _e('Recreate', BWG()->prefix); ?>" />
                  </div>
                  <p class="description"><?php _e('Specify the maximum dimensions of generated thumbnails. They must be larger than frontend thumbnail dimensions.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label" for="image_quality"><?php _e('Image quality', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="image_quality" id="image_quality" value="<?php echo $row->image_quality; ?>" min="0" max="100" /><span>%</span>
                  </div>
                  <p class="description"><?php _e('Set the quality of gallery images. Provide a value from 0 to 100%.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Resizable thumbnails', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="resizable_thumbnails" id="resizable_thumbnails_1" value="1" <?php if ($row->resizable_thumbnails) echo 'checked="checked"'; ?> /><label for="resizable_thumbnails_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="resizable_thumbnails" id="resizable_thumbnails_0" value="0" <?php if (!$row->resizable_thumbnails) echo 'checked="checked"'; ?> /><label for="resizable_thumbnails_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enable this option to allow resizing gallery thumbnails on smaller screens.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Lazy load', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="lazyload_images" id="lazyload_images_1" value="1" <?php if ($row->lazyload_images) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_lazyload_images_count', 'lazyload_images_1')" /><label for="lazyload_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="lazyload_images" id="lazyload_images_0" value="0" <?php if (!$row->lazyload_images) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_lazyload_images_count', 'lazyload_images_0')" /><label for="lazyload_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enable this option to activate lazy loading for images and improve the loading speed on your galleries.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Preload images', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="preload_images" id="preload_images_1" value="1" <?php if ($row->preload_images) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_preload_images_count', 'preload_images_1')" /><label for="preload_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="preload_images" id="preload_images_0" value="0" <?php if (!$row->preload_images) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_preload_images_count', 'preload_images_0')" /><label for="preload_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('If this setting is enabled, Photo Gallery loads a specific number of images before opening lightbox. This lets you showcase images without loading delays, providing better user experience.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_preload_images_count">
                <div class="wd-group">
                  <label class="wd-label" for="preload_images_count"><?php _e('Number of preloaded images', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="preload_images_count" id="preload_images_count" value="<?php echo $row->preload_images_count; ?>" min="0" />
                  </div>
                  <p class="description"><?php _e('Specify the number of images to preload, e.g. 5 (set 0 for all).', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show custom posts', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="show_hide_custom_post" id="show_hide_custom_post_1" value="1" <?php if ($row->show_hide_custom_post) echo 'checked="checked"'; ?> /><label for="show_hide_custom_post_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="show_hide_custom_post" id="show_hide_custom_post_0" value="0" <?php if (!$row->show_hide_custom_post) echo 'checked="checked"'; ?> /><label for="show_hide_custom_post_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Activate this setting to display Photo Gallery custom posts with new menu items under WordPress admin menu.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Discourage Search Engine Visibility', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="noindex_custom_post" id="noindex_custom_post_1" value="1" <?php if ($row->noindex_custom_post) echo 'checked="checked"'; ?> /><label for="noindex_custom_post_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="noindex_custom_post" id="noindex_custom_post_0" value="0" <?php if (!$row->noindex_custom_post) echo 'checked="checked"'; ?> /><label for="noindex_custom_post_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Discourage search engines from indexing Photo Gallery custom posts.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show comments for custom posts', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="show_hide_post_meta" id="show_hide_post_meta_1" value="1" <?php if ($row->show_hide_post_meta) echo 'checked="checked"'; ?> /><label for="show_hide_post_meta_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="show_hide_post_meta" id="show_hide_post_meta_0" value="0" <?php if (!$row->show_hide_post_meta) echo 'checked="checked"'; ?> /><label for="show_hide_post_meta_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Use this setting to show or hide comments under Photo Gallery custom posts.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Use AND operator for tag filtering', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="tags_filter_and_or" id="tags_filter_and_or_1" value="1" <?php if ($row->tags_filter_and_or) echo 'checked="checked"'; ?> /><label for="tags_filter_and_or_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="tags_filter_and_or" id="tags_filter_and_or_0" value="0" <?php if (!$row->tags_filter_and_or) echo 'checked="checked"'; ?> /><label for="tags_filter_and_or_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enable this option to filter images with AND operator. In this case, the filter results must have all selected tags in the Tag Box.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable GDPR compliance', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="gdpr_compliance" id="gdpr_compliance_1" value="1" <?php if ($row->gdpr_compliance) echo 'checked="checked"'; ?> /><label for="gdpr_compliance_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="gdpr_compliance" id="gdpr_compliance_0" value="0" <?php if (!$row->gdpr_compliance) echo 'checked="checked"'; ?> /><label for="gdpr_compliance_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enable this option to have General Data Protection Regulation.', BWG()->prefix); ?></p>
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-50">
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Save IP ', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="save_ip" id="save_ip_1" value="1" <?php if ($row->save_ip) echo 'checked="checked"'; ?> /><label for="save_ip_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="save_ip" id="save_ip_0" value="0" <?php if (!$row->save_ip) echo 'checked="checked"'; ?> /><label for="save_ip_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Disable saving user IP address when rating the images.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Right-click protection', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="image_right_click" id="image_right_click_1" value="1" <?php if ($row->image_right_click) echo 'checked="checked"'; ?> /><label for="image_right_click_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="image_right_click" id="image_right_click_0" value="0" <?php if (!$row->image_right_click) echo 'checked="checked"'; ?> /><label for="image_right_click_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Switch off right-click on your gallery images by enabling this setting.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Include styles/scripts on gallery pages only', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="use_inline_stiles_and_scripts" id="use_inline_stiles_and_scripts_1" value="1" <?php if ($row->use_inline_stiles_and_scripts) echo 'checked="checked"'; ?> /><label for="use_inline_stiles_and_scripts_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="use_inline_stiles_and_scripts" id="use_inline_stiles_and_scripts_0" value="0" <?php if (!$row->use_inline_stiles_and_scripts) echo 'checked="checked"'; ?> /><label for="use_inline_stiles_and_scripts_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('If this option is enabled, CSS and Javascript files of Photo Gallery will only load on pages with galleries and gallery groups.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable Google fonts', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="enable_google_fonts" id="enable_google_fonts_1" value="1" <?php if ($row->enable_google_fonts) echo 'checked="checked"'; ?> /><label for="enable_google_fonts_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="enable_google_fonts" id="enable_google_fonts_0" value="0" <?php if (!$row->enable_google_fonts) echo 'checked="checked"'; ?> /><label for="enable_google_fonts_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('If this option is disabled, Google fonts will not be included in your pages.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable HTML editor', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="enable_wp_editor" id="enable_wp_editor_1" value="1" <?php if ($row->enable_wp_editor) echo 'checked="checked"'; ?> /><label for="enable_wp_editor_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="enable_wp_editor" id="enable_wp_editor_0" value="0" <?php if (!$row->enable_wp_editor) echo 'checked="checked"'; ?> /><label for="enable_wp_editor_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Description text boxes of Photo Gallery will use TinyMCE editor, in case this setting is enabled.', BWG()->prefix); ?></p>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable get parameter for image URL', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="enable_date_parameter" id="enable_date_parameter_1" value="1" <?php if ($row->enable_date_parameter) echo 'checked="checked"'; ?> /><label for="enable_date_parameter_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="enable_date_parameter" id="enable_date_parameter_0" value="0" <?php if (!$row->enable_date_parameter) echo 'checked="checked"'; ?> /><label for="enable_date_parameter_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('If this option is enabled, some IDs will be added after the image extension to enable CDN to serve those images.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable href attribute', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="enable_seo" id="enable_seo_1" value="1" <?php if ($row->enable_seo) echo 'checked="checked"'; ?> /><label for="enable_seo_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="enable_seo" id="enable_seo_0" value="0" <?php if (!$row->enable_seo) echo 'checked="checked"'; ?> /><label for="enable_seo_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Disable this option only if Photo Gallery conflicts with your theme.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Auto-fill metadata', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="read_metadata" id="read_metadata_1" value="1" <?php if ($row->read_metadata) echo 'checked="checked"'; ?> /><label for="read_metadata_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="read_metadata" id="read_metadata_0" value="0" <?php if (!$row->read_metadata) echo 'checked="checked"'; ?> /><label for="read_metadata_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enabling this option will let the plugin fill in meta descriptions of photos into Image Description option automatically.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Generate Shortcode', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <a class="button" href="<?php echo add_query_arg( array( 'page' => 'shortcode_' . BWG()->prefix), admin_url('admin.php') ); ?>" target="_blank">
                      <?php _e('Generate Shortcode', BWG()->prefix); ?>
                    </a>
                  </div>
                  <p class="description"><?php _e('Generate or edit Photo Gallery shortcodes that are used to publish galleries or gallery groups.', BWG()->prefix); ?></p>
                </div>
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable dynamic URLs for galleries and gallery groups', BWG()->prefix); ?></label>
                  <input type="radio" name="front_ajax" id="front_ajax_1" value="1" <?php if ($row->front_ajax) echo 'checked="checked"'; ?> /><label for="front_ajax_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="front_ajax" id="front_ajax_0" value="0" <?php if (!$row->front_ajax) echo 'checked="checked"'; ?> /><label for="front_ajax_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  <p class="description"><?php _e('Enable this option to browse galleries and gallery groups, as well as search results and tagged images with dynamic links.', BWG()->prefix); ?></p>
                </div>
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Developer mode', BWG()->prefix); ?></label>
                  <input type="radio" name="developer_mode" id="developer_mode_1" value="1" <?php if ($row->developer_mode) echo 'checked="checked"'; ?> /><label for="developer_mode_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="developer_mode" id="developer_mode_0" value="0" <?php if (!$row->developer_mode) echo 'checked="checked"'; ?> /><label for="developer_mode_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  <p class="description"><?php _e('Do not use minified JS and CSS files. Enable this option if You need to debug JS or CSS issues.', BWG()->prefix); ?></p>
                </div>
              </div>
              <?php
              if ( !BWG()->is_demo ) {
                ?>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php echo sprintf(__('Uninstall %s', BWG()->prefix), BWG()->nicename); ?></label>
                  <div class="bwg-flex">
                    <a class="button" href="<?php echo add_query_arg( array( 'page' => 'uninstall_' . BWG()->prefix), admin_url('admin.php') ); ?>">
                      <?php _e('Uninstall', BWG()->prefix); ?>
                    </a>
                  </div>
                  <p class="description"><?php _e('Note, that uninstalling Photo Gallery will completely remove all galleries, gallery groups and other data on the plugin. Please make sure you don\'t have any important information before you proceed.', BWG()->prefix); ?></p>
                </div>
              </div>
                <?php
              }
              ?>
              <?php do_action('bwg_print_options_general_after') ?>
            </div>
          </div>
        </div>
      </div>
      </div>
      <div id="bwg_tab_gallery_content" class="search-div bwg-section wd-box-content">
       <div class="bwg-section bwg-flex-wrap">
        <div class="wd-box-content wd-width-100 bwg-flex-wrap">
          <div id="bwg_tab_galleries_content">
            <div class="bwg_change_gallery_type">
              <span class="gallery_type" onClick="bwg_gallery_type_options('thumbnails')">
                <div class="gallery_type_div">
                  <label for="thumbnails">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/thumbnails.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/thumbnails_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="thumbnails" name="gallery_type" value="thumbnails" />
                  <label class="gallery_type_label" for="thumbnails"><?php echo __('Thumbnails', BWG()->prefix); ?></label>
                </div>
              </span>
              <span class="gallery_type bwg-thumbnails_masonry" onClick="bwg_gallery_type_options('thumbnails_masonry')" data-img-url="<?php echo BWG()->plugin_url . '/images/upgrade_to_pro_masonry.png'; ?>" data-title="Masonry" data-demo-link="https://demo.10web.io/photo-gallery/masonry/?utm_source=photo_gallery&utm_medium=free_plugin">
                <div class="gallery_type_div">
                  <label for="thumbnails_masonry">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/thumbnails_masonry.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/thumbnails_masonry_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="thumbnails_masonry" name="gallery_type" value="thumbnails_masonry" />
                  <label class="gallery_type_label" for="thumbnails_masonry"><?php echo __('Masonry', BWG()->prefix); ?></label>
                  <?php if ( !BWG()->is_pro ) { ?>
                    <span class="pro_btn">Premium</span>
                  <?php } ?>
                </div>
              </span>
              <span class="gallery_type bwg-thumbnails_mosaic" onClick="bwg_gallery_type_options('thumbnails_mosaic')"  data-img-url="<?php echo BWG()->plugin_url . '/images/upgrade_to_pro_mosaic.png'; ?>" data-title="Mosaic" data-demo-link="https://demo.10web.io/photo-gallery/mosaic/?utm_source=photo_gallery&utm_medium=free_plugin">
                <div class="gallery_type_div">
                  <label for="thumbnails_mosaic" >
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/thumbnails_mosaic.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/thumbnails_mosaic_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="thumbnails_mosaic" name="gallery_type" value="thumbnails_mosaic" />
                  <label class="gallery_type_label" for="thumbnails_mosaic"><?php echo __('Mosaic', BWG()->prefix); ?></label>
                  <?php if ( !BWG()->is_pro ) { ?>
                    <span class="pro_btn">Premium</span>
                  <?php } ?>
                </div>
              </span>
              <span class="gallery_type" onClick="bwg_gallery_type_options('slideshow')">
                <div class="gallery_type_div">
                  <label for="slideshow">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/slideshow.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/slideshow_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="slideshow" name="gallery_type" value="slideshow" />
                  <label class="gallery_type_label" for="slideshow"><?php echo __('Slideshow', BWG()->prefix); ?></label>
                </div>
              </span>
              <span class="gallery_type" onClick="bwg_gallery_type_options('image_browser')">
                <div class="gallery_type_div">
                  <label for="image_browser">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/image_browser.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/image_browser_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="image_browser" name="gallery_type" value="image_browser" />
                  <label class="gallery_type_label" for="image_browser"><?php echo __('Image Browser', BWG()->prefix); ?></label>
                </div>
              </span>
              <span class="gallery_type bwg-blog_style" onClick="bwg_gallery_type_options('blog_style')" data-img-url="<?php echo BWG()->plugin_url . '/images/upgrade_to_pro_blog_style.png'; ?>" data-title="Blog Style" data-demo-link="https://demo.10web.io/photo-gallery/blog-style/?utm_source=photo_gallery&utm_medium=free_plugin">
                <div class="gallery_type_div">
                  <label for="blog_style">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/blog_style.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/blog_style_active.svg'; ?>" />
                  </label>
                  <input type="radio" class="gallery_type_radio" id="blog_style" name="gallery_type" value="blog_style" />
                  <label class="gallery_type_label" for="blog_style"><?php echo __('Blog Style', BWG()->prefix); ?></label>
                  <?php if ( !BWG()->is_pro ) { ?>
                    <span class="pro_btn">Premium</span>
                  <?php } ?>
                </div>
              </span>
              <span class="gallery_type bwg-carousel" onClick="bwg_gallery_type_options('carousel')" data-img-url="<?php echo BWG()->plugin_url . '/images/upgrade_to_pro_carousel.png'; ?>" data-title="Carousel" data-demo-link="https://demo.10web.io/photo-gallery/carousel/?utm_source=photo_gallery&utm_medium=free_plugin">
                <div class="gallery_type_div">
                  <label for="carousel">
                    <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/carousel.svg'; ?>" />
                    <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/carousel_active.svg'; ?>" />
                  </label>
                  <input class="gallery_type_radio" type="radio" id="carousel" name="gallery_type" value="carousel" />
                  <label class="gallery_type_label" for="carousel"><?php echo __('Carousel', BWG()->prefix); ?></label>
                  <?php if ( !BWG()->is_pro ) { ?>
                    <span class="pro_btn">Premium</span>
                  <?php } ?>
                </div>
              </span>
            </div>
            <div class="bwg_select_gallery_type">
<!--              <label class="wd-label" for="gallery_types_name">--><?php //_e('View type', BWG()->prefix); ?><!--</label>-->
              <select name="gallery_types_name" id="gallery_types_name" onchange="bwg_gallery_type_options(jQuery(this).val());">
                <?php
                foreach ($gallery_types_name as $key=>$gallery_type_name) {
                  ?>
                  <option <?php echo selected($gallery_type_name,true); ?> value="<?php echo $key; ?>"><?php echo $gallery_type_name; ?></option>
                  <?php
                }
                ?>
              </select>
              <div class="bwg-gallery-type-select">
                <div class="bwg-btn-gallery-type-select type-closed" value="thumbnails" id="gallery-view-type">Thumbnails</div>
                <div class="bwg-gallery-ul-div">
                  <ul class="bwg-gallery-ul">
                    <?php
                    foreach ($gallery_types_name as $key=>$gallery_type_name) {
                      ?>
                      <li class="gallery-type-li" data-value="<?php echo $key; ?>">
                        <img src="<?php echo BWG()->plugin_url . '/images/' . $key . '.svg'; ?>">
                        <span><?php echo $gallery_type_name; ?> </span>
                        <?php if ( !BWG()->is_pro && ( $key == 'thumbnails_masonry' || $key == 'thumbnails_mosaic' || $key == 'blog_style' || $key == 'carousel' )) { ?>
                          <span class="pro_btn">Premium</span>
                        <?php } ?>
                      </li>
                      <?php
                    }
                    ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <?php
          if ( !BWG()->is_pro ) {
            ?>
            <div class="wd-box-content wd-width-100 wd-free-msg bwg-upgrade-view">
              <div class="upgrade-to-pro-text">
                <p class="upgrade-to-pro-title"></p>
                <p class="upgrade-to-pro-desc">
                  <?php _e('Visit demo page for this view');?>
                </p>
                <a href="https://10web.io/plugins/wordpress-photo-gallery/?utm_source=photo_gallery/?utm_medium=free_plugin" target="_blank" class="button-upgrade"><?php _e('UPGRADE to Premium');?></a>
                <a class="button-demo" href="https://demo.10web.io/photo-gallery/" target="_blank" ><?php _e('view demo');?></a>
              </div>
              <div class="upgrade-to-img" data-url="<?php echo BWG()->plugin_url . '/images/';?>">
                <img class="pro-views-img desktop" src="">
              </div>
            </div>
            <?php
          }
          ?>
          <?php
          self::gallery_options($row);
          ?>
        </div>
      </div>
      </div>
      <div id="bwg_tab_gallery_group_content" class="search-div bwg-section wd-box-content">
        <div class="bwg-section bwg-flex-wrap">
          <div class="wd-box-content bwg-flex-wrap">
            <div id="bwg_tab_albums_content">
              <div class="bwg_change_gallery_type">
                    <span class="gallery_type" onClick="bwg_album_type_options('album_compact_preview')">
                      <div class="album_type_div">
                        <label for="album_compact_preview">
                          <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/album_compact_preview.svg'; ?>" />
                          <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/album_compact_preview_active.svg'; ?>" />
                        </label>
                        <input type="radio" class="album_type_radio" id="album_compact_preview" name="album_type" value="album_compact_preview" />
                        <label class="album_type_label" for="album_compact_preview"><?php echo __('Compact', BWG()->prefix); ?></label>
                      </div>
                    </span>
                <span class="gallery_type bwg-album_masonry_preview" onClick="bwg_album_type_options('album_masonry_preview')" data-img-url="<?php echo BWG()->plugin_url . '/images/upgrade_to_pro_masonry.png'; ?>" data-title="Masonry" data-demo-link="https://demo.10web.io/photo-gallery/masonry/?utm_source=photo_gallery&utm_medium=free_plugin">
                      <div></div>
                      <div class="album_type_div">
                        <label for="album_masonry_preview">
                          <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/album_masonry_preview.svg'; ?>" />
                          <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/album_masonry_preview_active.svg'; ?>" />
                        </label>
                        <input type="radio" class="album_type_radio" id="album_masonry_preview" name="album_type" value="album_masonry_preview" />
                        <label class="album_type_label" for="album_masonry_preview"><?php echo __('Masonry', BWG()->prefix); ?></label>
                        <?php if ( !BWG()->is_pro ) { ?>
                          <span class="pro_btn">Premium</span>
                        <?php } ?>
                      </div>
                    </span>
                <span class="gallery_type" onClick="bwg_album_type_options('album_extended_preview')">
                      <div class="album_type_div">
                        <label for="album_extended_preview">
                          <img class="view_type_img" src="<?php echo BWG()->plugin_url . '/images/album_extended_preview.svg'; ?>" />
                          <img class="view_type_img_active" src="<?php echo BWG()->plugin_url . '/images/album_extended_preview_active.svg'; ?>" />
                        </label>
                        <input type="radio" class="album_type_radio" id="album_extended_preview" name="album_type" value="album_extended_preview" />
                        <label class="album_type_label" for="album_extended_preview"><?php echo __('Extended', BWG()->prefix); ?></label>
                      </div>
                    </span>
              </div>
              <div class="bwg_select_gallery_type">
<!--                <label class="wd-label" for="album_types_name">--><?php //_e('View type', BWG()->prefix); ?><!--</label>-->
                <select name="album_types_name" id="album_types_name" onchange="bwg_album_type_options(jQuery(this).val());">
                  <?php
                  foreach ($album_types_name as $key=>$album_type_name) {
                    ?>
                    <option <?php echo selected($album_type_name,true); ?> value="<?php echo $key; ?>"><?php echo $album_type_name; ?></option>
                    <?php
                  }
                  ?>
                </select>
                <div class="bwg-gallery-type-select">
                  <div class="bwg-btn-gallery-type-select type-closed" value="album_compact_preview" id="album-view-type">Compact</div>
                  <div class="bwg-gallery-ul-div">
                    <ul class="bwg-gallery-ul">
                      <?php
                      foreach ($album_types_name as $key=>$album_type_name) {
                        ?>
                        <li class="gallery-type-li" data-value="<?php echo $key; ?>">
                          <img src="<?php echo BWG()->plugin_url . '/images/' . $key . '.svg'; ?>">
                          <span><?php echo $album_type_name; ?> </span>
                          <?php if ( !BWG()->is_pro && ( $key == 'album_masonry_preview' ) ) { ?>
                            <span class="pro_btn">Premium</span>
                          <?php } ?>
                        </li>
                        <?php
                      }
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <?php
            if ( !BWG()->is_pro ) {
              ?>
              <div class="wd-box-content wd-width-100 wd-free-msg bwg-upgrade-view">
                <div class="upgrade-to-pro-text">
                  <p class="upgrade-to-pro-title"></p>
                  <p class="upgrade-to-pro-desc">
                    <?php _e('Visit demo page for this view');?>
                  </p>
                  <a href="https://10web.io/plugins/wordpress-photo-gallery/?utm_source=photo_gallery/?utm_medium=free_plugin" target="_blank" class="button-upgrade"><?php _e('UPGRADE to Premium');?></a>
                  <a class="button-demo" href="https://demo.10web.io/photo-gallery/" target="_blank" ><?php _e('view demo');?></a>
                </div>
                <div class="upgrade-to-img">
                  <img class="desktop pro-views-img" src="">
                </div>
              </div>
              <?php
            }
            ?>
            <?php
            self::gallery_group_options($row);
            ?>
          </div>
        </div>
      </div>
      <div id="bwg_tab_lightbox_content" class="search-div bwg-section wd-box-content">
        <div class="bwg-section bwg-flex-wrap">
          <?php
          self::lightbox_options($row);
          ?>
        </div>
      </div>
      <div id="bwg_tab_advanced_content" class="search-div bwg-section wd-box-content">
        <div class="bwg-section bwg-flex-wrap">
          <div class="wd-box-content wd-width-100 meta-box-sortables">
            <div class="postbox">
              <button class="button-link handlediv" type="button" aria-expanded="true">
                <span class="screen-reader-text"><?php _e('Toggle panel:', BWG()->prefix); ?></span>
                <span class="toggle-indicator" aria-hidden="false"></span>
              </button>
              <h2 class="hndle">
                <span><?php _e('Social', BWG()->prefix); ?></span>
              </h2>
              <div class="inside bwg-flex-wrap">
                <div class="wd-box-content wd-width-100 bwg-flex-wrap">
                  <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group wd-width-50">
                      <label class="wd-label" for="autoupdate_interval_hour"><?php _e('Gallery autoupdate interval', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" id="autoupdate_interval_hour" name="autoupdate_interval_hour" min="0" max="24" value="<?php echo floor($row->autoupdate_interval / 60); ?>" />
                        <span><?php _e('hour', BWG()->prefix); ?></span>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" id="autoupdate_interval_min" name="autoupdate_interval_min" min="0" max="59" value="<?php echo floor($row->autoupdate_interval % 60); ?>" />
                        <span><?php _e('min', BWG()->prefix); ?></span>
                      </div>
                      <p class="description"><?php _e('Set the interval when Instagram galleries will be updated, and will display new posts of your Instagram or Facebook account.', BWG()->prefix) ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-50 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-box-title">
                      <strong><?php _e('Instagram', BWG()->prefix); ?></strong>
                    </div>
                    <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                      <div class="wd-group" id="login_with_instagram">
                        <input id="instagram_access_token" name="instagram_access_token" type="hidden" size="30" value="<?php echo $row->instagram_access_token; ?>" readonly />
						<?php if ( empty($row->instagram_access_token) ) {
							$instagram_description = __('Press this button to sign in to your Instagram account. In this case, access token will be added automatically.', BWG()->prefix);
						?>
                          <a <?php echo BWG()->is_pro ? 'href="' . $instagram_return_url . '"' : 'disabled="disabled"'; ?>>
                            <img src="<?php echo BWG()->plugin_url . '/images/logos/instagram.png'; ?>">
                            <span class="bwg-instagram-sign-in"><?php _e('Sign in with Instagram', BWG()->prefix) ?></span>
                          </a>
                          <p class="bwg-clear description"><?php _e('Press this button to sign in to your Instagram account. This lets you incorporate Instagram API to your website.', BWG()->prefix) ?></p>
                        <?php }
                        else {
							$instagram_description = __('Press this button to sign out from your Instagram account. The access token will reset.', BWG()->prefix);
						?>
                          <a <?php echo BWG()->is_pro ? 'href="' . $instagram_reset_href . '" onClick="if(confirm(\'' . addslashes(__('Are you sure you want to reset access token, after resetting it you will need to log in with Instagram again for using plugin', BWG()->prefix)) . '\')){ return true; } else { return false; }"' : 'disabled="disabled"'; ?>>
                            <img src="<?php echo BWG()->plugin_url . '/images/logos/instagram.png'; ?>">
                            <span class="bwg-instagram-sign-out"><?php _e('Sign out from Instagram', BWG()->prefix) ?></span>
                          </a>
                          <p class="bwg-clear description"><?php _e('Press this button to sign out from your Instagram account.', BWG()->prefix) ?></p>
                        <?php } ?>
                      </div>
                    </div>
                    <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                  </div>
                  <?php if ( has_action('init_display_facebook_options_bwg') ) { ?>
                  <div class="wd-box-content wd-width-50">
                    <div class="wd-box-title">
                      <strong><?php _e('Facebook', BWG()->prefix); ?></strong>
                    </div>
                    <?php
                      do_action('init_display_facebook_options_bwg', $row );
                    ?>
                  </div>
                  <?php } ?>
                  <?php do_action('bwg_advanced_sections_social', $row ); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 meta-box-sortables">
            <div class="postbox closed">
              <button class="button-link handlediv" type="button" aria-expanded="true">
                <span class="screen-reader-text"><?php _e('Toggle panel:', BWG()->prefix); ?></span>
                <span class="toggle-indicator" aria-hidden="false"></span>
              </button>
              <h2 class="hndle">
                <span><?php _e('Roles', BWG()->prefix); ?></span>
              </h2>
              <div class="inside bwg-flex-wrap">
                <div class="wd-box-content wd-width-50">
                  <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label" for="permissions"><?php _e('Roles', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <select <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> id="permissions" name="permissions" onchange="bwg_show_hide_roles();">
                          <?php
                          foreach ($permissions as $key => $permission) {
                            ?>
                            <option value="<?php echo $key; ?>" <?php if ($row->permissions == $key) echo 'selected="selected"'; ?>><?php echo $permission; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                      <p class="description"><?php _e('Choose a WordPress user role which can add and edit galleries, images, gallery groups, tags, themes and edit settings.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                </div>
                <div class="wd-box-content wd-width-50">
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Gallery role restrictions', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="gallery_role" id="gallery_role_1" value="1" <?php if ($row->gallery_role) echo 'checked="checked"'; ?> /><label for="gallery_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="gallery_role" id="gallery_role_0" value="0" <?php if (!$row->gallery_role) echo 'checked="checked"'; ?> /><label for="gallery_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enable this setting to restrict authors from modifying galleries created by other users.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Gallery group restrictions', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_role" id="album_role_1" value="1" <?php if ($row->album_role) echo 'checked="checked"'; ?> /><label for="album_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_role" id="album_role_0" value="0" <?php if (!$row->album_role) echo 'checked="checked"'; ?> /><label for="album_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enabling this option will restrict authors from modifying galleries groups created by other users.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Image role restrictions', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="image_role" id="image_role_1" value="1" <?php if ($row->image_role) echo 'checked="checked"'; ?> /><label for="image_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="image_role" id="image_role_0" value="0" <?php if (!$row->image_role) echo 'checked="checked"'; ?> /><label for="image_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enable this setting to restrict authors from modifying images added by other users.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Tag permission', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="tag_role" id="tag_role_1" value="1" <?php if ($row->tag_role) echo 'checked="checked"'; ?> /><label for="tag_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="tag_role" id="tag_role_0" value="0" <?php if (!$row->tag_role) echo 'checked="checked"'; ?> /><label for="tag_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enable this setting to allow users to add/edit tags.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Theme permission', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="theme_role" id="theme_role_1" value="1" <?php if ($row->theme_role) echo 'checked="checked"'; ?> /><label for="theme_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="theme_role" id="theme_role_0" value="0" <?php if (!$row->theme_role) echo 'checked="checked"'; ?> /><label for="theme_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enable this setting to allow users to add/edit themes.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100 bwg_roles <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Global settings permission', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="settings_role" id="settings_role_1" value="1" <?php if ($row->settings_role) echo 'checked="checked"'; ?> /><label for="settings_role_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                        <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="settings_role" id="settings_role_0" value="0" <?php if (!$row->settings_role) echo 'checked="checked"'; ?> /><label for="settings_role_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Enable this setting to allow users to edit global settings.', BWG()->prefix); ?></p>
                      <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 meta-box-sortables">
            <div class="postbox closed">
              <button class="button-link handlediv" type="button" aria-expanded="true">
                <span class="screen-reader-text"><?php _e('Toggle panel:', BWG()->prefix); ?></span>
                <span class="toggle-indicator" aria-hidden="false"></span>
              </button>
              <h2 class="hndle">
                <span><?php _e('Advertisement', BWG()->prefix); ?></span>
              </h2>
              <div class="inside bwg-flex-wrap">
                <div class="wd-box-content wd-width-50">
                  <div class="wd-box-content wd-width-100">
                    <div class="wd-group">
                      <label class="wd-label"><?php _e('Advertisement type', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="radio" name="watermark_type" id="watermark_type_none" value="none" <?php if ($row->watermark_type == 'none') echo 'checked="checked"'; ?> onClick="bwg_watermark('watermark_type_none')" />
                        <label for="watermark_type_none" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                        <input type="radio" name="watermark_type" id="watermark_type_text" value="text" <?php if ($row->watermark_type == 'text') echo 'checked="checked"'; ?> onClick="bwg_watermark('watermark_type_text')" onchange="preview_watermark()" />
                        <label for="watermark_type_text" class="wd-radio-label"><?php _e('Text', BWG()->prefix); ?></label>
                        <input type="radio" name="watermark_type" id="watermark_type_image" value="image" <?php if ($row->watermark_type == 'image') echo 'checked="checked"'; ?> onClick="bwg_watermark('watermark_type_image')" onchange="preview_watermark()" />
                        <label for="watermark_type_image" class="wd-radio-label"><?php _e('Image', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Add Text or Image advertisement to your images with this option.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_url">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_url"><?php _e('Advertisement URL', BWG()->prefix); ?></label>
                      <div>
                        <?php
                        $query_url = add_query_arg(array('action' => 'addImages', 'width' => '800', 'height' => '550', 'extensions' => 'jpg,jpeg,png,gif,svg', 'callback' => 'bwg_add_watermark_image'), admin_url('admin-ajax.php'));
                        $query_url = wp_nonce_url( $query_url, 'addImages', 'bwg_nonce' );
                        $query_url = add_query_arg(array('TB_iframe' => '1'), $query_url );
                        ?>
                        <a href="<?php echo $query_url; ?>" id="button_add_watermark_image" class="button-primary thickbox thickbox-preview"
                           title="<?php _e('Select Image', BWG()->prefix); ?>"
                           onclick="return false;">
                          <?php _e('Select Image', BWG()->prefix); ?>
                        </a>
                        <br /><?php _e('or', BWG()->prefix); ?><br />
                        <input type="text" id="watermark_url" name="watermark_url" value="<?php echo $row->watermark_url; ?>" onchange="preview_watermark()" placeholder="e.g. https://example.com/uploads/watermark.png" />
                      </div>
                      <p class="description"><?php _e('Provide the absolute URL of the image you would like to use as advertisement.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_text">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_text"><?php _e('Advertisement text', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="text" name="watermark_text" id="watermark_text" style="width: 100%;" value="<?php echo $row->watermark_text; ?>" onchange="preview_watermark()" onkeypress="preview_watermark()" />
                      </div>
                      <p class="description"><?php _e('Write the text to add to images as advertisement.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_link">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_link"><?php _e('Advertisement link', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="text" name="watermark_link" id="watermark_link" style="width: 100%;" value="<?php echo $row->watermark_link; ?>" onchange="preview_watermark()" onkeypress="preview_watermark()" />
                      </div>
                      <p class="description"><?php _e('Provide the link to be added to advertisement on images.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_width_height">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_width"><?php _e('Advertisement dimensions', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="number" name="watermark_width" id="watermark_width" value="<?php echo $row->watermark_width; ?>" min="0" onchange="preview_watermark()" /><span>x</span>
                        <input type="number" name="watermark_height" id="watermark_height" value="<?php echo $row->watermark_height; ?>" min="0" onchange="preview_watermark()" /><span>px</span>
                      </div>
                      <p class="description"><?php _e('Select the dimensions of the advertisement image.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_font_size">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_font_size"><?php _e('Advertisement font size', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="number" name="watermark_font_size" id="watermark_font_size" value="<?php echo $row->watermark_font_size; ?>" min="0" onchange="preview_watermark()" /><span>px</span>
                      </div>
                      <p class="description"><?php _e('Specify the font size of the advertisement text.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_font">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_font"><?php _e('Advertisement font style', BWG()->prefix); ?></label>
                      <div>
                        <select name="watermark_font" id="watermark_font" onchange="preview_watermark()">
                          <?php
                          $google_fonts = WDWLibrary::get_google_fonts();
                          $is_google_fonts = (in_array($row->watermark_font, $google_fonts) ) ? true : false;
                          $watermark_font_families = ($is_google_fonts == true) ? $google_fonts : $watermark_fonts;
                          foreach ($watermark_font_families as $watermark_font) {
                            ?>
                            <option value="<?php echo $watermark_font; ?>" <?php if ($row->watermark_font == $watermark_font) echo 'selected="selected"'; ?>><?php echo $watermark_font; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                        <input type="radio" name="watermark_google_fonts" id="watermark_google_fonts1" onchange="bwg_change_fonts('watermark_font', jQuery(this).attr('id'))" value="1" <?php if ($is_google_fonts) echo 'checked="checked"'; ?> />
                        <label for="watermark_google_fonts1" id="watermark_google_fonts1_lbl" class="wd-radio-label"><?php _e('Google fonts', BWG()->prefix); ?></label>
                        <input type="radio" name="watermark_google_fonts" id="watermark_google_fonts0" onchange="bwg_change_fonts('watermark_font', '')" value="0" <?php if (!$is_google_fonts) echo 'checked="checked"'; ?> />
                        <label for="watermark_google_fonts0" id="watermark_google_fonts0_lbl" class="wd-radio-label"><?php _e('Default', BWG()->prefix); ?></label>
                      </div>
                      <p class="description"><?php _e('Select the font family of the advertisement text.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_color">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_color"><?php _e('Advertisement color', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="text" name="watermark_color" id="watermark_color" value="<?php echo $row->watermark_color; ?>" class="color" onchange="preview_watermark()" />
                      </div>
                      <p class="description"><?php _e('Choose the color for the advertisement text on images.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_opacity">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_opacity"><?php _e('Advertisement opacity', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <input type="number" name="watermark_opacity" id="watermark_opacity" value="<?php echo $row->watermark_opacity; ?>" min="0" max="100" onchange="preview_watermark()" /><span>%</span>
                      </div>
                      <p class="description"><?php _e('Specify the opacity of the advertisement. The value must be between 0 to 100.', BWG()->prefix) ?></p>
                    </div>
                  </div>
                  <div class="wd-box-content wd-width-100" id="tr_watermark_position">
                    <div class="wd-group">
                      <label class="wd-label" for="watermark_opacity"><?php _e('Advertisement position', BWG()->prefix); ?></label>
                      <div class="bwg-flex">
                        <table class="bwg_position_table">
                          <tbody>
                          <tr>
                            <td><input type="radio" value="top-left" name="watermark_position" <?php if ($row->watermark_position == "top-left") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="top-center" name="watermark_position" <?php if ($row->watermark_position == "top-center") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="top-right" name="watermark_position" <?php if ($row->watermark_position == "top-right") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                          </tr>
                          <tr>
                            <td><input type="radio" value="middle-left" name="watermark_position" <?php if ($row->watermark_position == "middle-left") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="middle-center" name="watermark_position" <?php if ($row->watermark_position == "middle-center") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="middle-right" name="watermark_position" <?php if ($row->watermark_position == "middle-right") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                          </tr>
                          <tr>
                            <td><input type="radio" value="bottom-left" name="watermark_position" <?php if ($row->watermark_position == "bottom-left") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="bottom-center" name="watermark_position" <?php if ($row->watermark_position == "bottom-center") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                            <td><input type="radio" value="bottom-right" name="watermark_position" <?php if ($row->watermark_position == "bottom-right") echo 'checked="checked"'; ?> onchange="preview_watermark()"></td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <p class="description"><?php _e('Mark the position where the advertisement should appear on images.', BWG()->prefix) ?></p>
                  </div>
                </div>
                <div class="wd-box-content wd-width-50">
                 <span id="preview_watermark" style="display:table-cell; background-image:url('<?php echo BWG()->plugin_url . '/images/watermark_preview.jpg'?>');background-size:100% 100%;width:400px;height:400px;padding-top: 4px; position:relative;">
                </div>
              </div>
            </div>
          </div>
        </div>		  
	    </div>
      <div id="bwg_tab_watermark_content" class="search-div bwg-section wd-box-content">
        <div class="bwg-section bwg-flex-wrap">
          <div class="wd-box-content wd-width-100 bwg-flex-wrap">
            <div class="wd-box-content wd-width-50">
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_type">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Watermark type', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="built_in_watermark_type" id="built_in_watermark_type_none" value="none" <?php if ($row->built_in_watermark_type == 'none') echo 'checked="checked"'; ?> onClick="bwg_built_in_watermark('watermark_type_none')" />
                    <label for="built_in_watermark_type_none" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                    <input type="radio" name="built_in_watermark_type" id="built_in_watermark_type_text" value="text" <?php if ($row->built_in_watermark_type == 'text') echo 'checked="checked"'; ?> onClick="bwg_built_in_watermark('watermark_type_text')" onchange="preview_built_in_watermark()" />
                    <label for="built_in_watermark_type_text" class="wd-radio-label"><?php _e('Text', BWG()->prefix); ?></label>
                    <input type="radio" name="built_in_watermark_type" id="built_in_watermark_type_image" value="image" <?php if ($row->built_in_watermark_type == 'image') echo 'checked="checked"'; ?> onClick="bwg_built_in_watermark('watermark_type_image')" onchange="preview_built_in_watermark()" />
                    <label for="built_in_watermark_type_image" class="wd-radio-label"><?php _e('Image', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Add Text or Image watermark to your images with this option.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_url">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_url"><?php _e('Watermark URL', BWG()->prefix); ?></label>
                  <div>
                    <?php
                    $query_url = add_query_arg(array('action' => 'addImages', 'width' => '800', 'height' => '550', 'extensions' => 'png', 'callback' => 'bwg_add_built_in_watermark_image'), admin_url('admin-ajax.php'));
                    $query_url = wp_nonce_url( $query_url, 'addImages', 'bwg_nonce' );
                    $query_url =  add_query_arg(array('TB_iframe' => '1'), $query_url );
                    ?>
                    <a href="<?php echo $query_url; ?>" id="button_add_built_in_watermark_image" class="button-primary thickbox thickbox-preview"
                       title="<?php _e('Select Image', BWG()->prefix); ?>"
                       onclick="return false;">
                      <?php _e('Select Image', BWG()->prefix); ?>
                    </a>
                    <br /><?php _e('or', BWG()->prefix); ?><br />
                    <input type="text" id="built_in_watermark_url" name="built_in_watermark_url" value="<?php echo $row->built_in_watermark_url; ?>" onchange="preview_built_in_watermark()" placeholder="e.g. https://example.com/uploads/watermark.png" />
                  </div>
                  <p class="description"><?php _e('Provide the absolute URL of the image you would like to use as watermark.', BWG()->prefix); ?><br><?php _e('Only .png format is supported.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_text">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_text"><?php _e('Watermark text', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="text" name="built_in_watermark_text" id="built_in_watermark_text" style="width: 100%;" value="<?php echo esc_attr($row->built_in_watermark_text); ?>" onchange="preview_built_in_watermark()" onkeypress="preview_built_in_watermark()" />
                  </div>
                  <p class="description"><?php _e('Provide the text to add to images as watermark.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_size">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_size"><?php _e('Watermark size', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="built_in_watermark_size" id="built_in_watermark_size" value="<?php echo $row->built_in_watermark_size; ?>" min="0" max="100" onchange="preview_built_in_watermark()" /><span>%</span>
                  </div>
                  <p class="description"><?php _e('Specify the size of watermark on images in percent.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_font_size">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_font_size"><?php _e('Watermark font size', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="built_in_watermark_font_size" id="built_in_watermark_font_size" value="<?php echo $row->built_in_watermark_font_size; ?>" min="0" onchange="preview_built_in_watermark()" />
                  </div>
                  <p class="description"><?php _e('Specify the font size of the watermark text.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_font">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_font"><?php _e('Watermark font style', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <select name="built_in_watermark_font" id="built_in_watermark_font" onchange="preview_built_in_watermark()">
                      <?php
                      foreach ($built_in_watermark_fonts as $watermark_font) {
                        ?>
                        <option value="<?php echo $watermark_font; ?>" <?php if ($row->built_in_watermark_font == $watermark_font) echo 'selected="selected"'; ?>><?php echo $watermark_font; ?></option>
                        <?php
                      }
                      ?>
                    </select>
                    <?php
                    foreach ($built_in_watermark_fonts as $watermark_font) {
                      ?>
                      <style>
                        @font-face {
                          font-family: <?php echo 'bwg_' . str_replace('.ttf', '', $watermark_font); ?>;
                          src: url("<?php echo BWG()->plugin_url . '/fonts/' . $watermark_font; ?>");
                        }
                      </style>
                      <?php
                    }
                    ?>
                  </div>
                  <p class="description"><?php _e('Select the font family of the watermark text.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_color">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_color"><?php _e('Watermark color', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="text" name="built_in_watermark_color" id="built_in_watermark_color" value="<?php echo $row->built_in_watermark_color; ?>" class="color" onchange="preview_built_in_watermark()" />
                  </div>
                  <p class="description"><?php _e('Choose the color for the watermark text on images.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_opacity">
                <div class="wd-group">
                  <label class="wd-label" for="built_in_watermark_opacity"><?php _e('Watermark opacity', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="number" name="built_in_watermark_opacity" id="built_in_watermark_opacity" value="<?php echo $row->built_in_watermark_opacity; ?>" min="0" max="100" onchange="preview_built_in_watermark()" /><span>%</span>
                  </div>
                  <p class="description"><?php _e('Specify the opacity of the watermark. The value must be between 0 to 100.', BWG()->prefix) ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100" id="tr_built_in_watermark_position">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Watermark position', BWG()->prefix); ?></label>
                  <div class="">
                    <table class="bwg_position_table">
                      <tbody>
                      <tr>
                        <td><input type="radio" value="top-left" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "top-left") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="top-center" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "top-center") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="top-right" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "top-right") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                      </tr>
                      <tr>
                        <td><input type="radio" value="middle-left" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "middle-left") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="middle-center" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "middle-center") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="middle-right" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "middle-right") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                      </tr>
                      <tr>
                        <td><input type="radio" value="bottom-left" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "bottom-left") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="bottom-center" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "bottom-center") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                        <td><input type="radio" value="bottom-right" name="built_in_watermark_position" <?php if ($row->built_in_watermark_position == "bottom-right") echo 'checked="checked"'; ?> onchange="preview_built_in_watermark()"></td>
                      </tr>
                      </tbody>
                    </table>
                    <input type="submit" class="button-primary" title="<?php _e('Set watermark', BWG()->prefix); ?>" style="margin-top: 5px;"
                           onclick="<?php echo (BWG()->is_demo ? 'alert(\'' . addslashes(__('This option is disabled in demo.', BWG()->prefix)) . '\'); return false;' : (BWG()->wp_editor_exists ?  'return bwg_set_watermark(0)' : 'alert(\'' . addslashes(__('Image edit functionality is not supported by your web host.', BWG()->prefix)) . '\'); return false;')); ?>"
                           value="<?php _e('Set Watermark', BWG()->prefix); ?>"/>
                    <input type="submit" class="button" title="<?php _e('Reset watermark', BWG()->prefix); ?>" style="margin-top: 5px;"
                           onclick="<?php echo (BWG()->is_demo ? 'alert(\'' . addslashes(__('This option is disabled in demo.', BWG()->prefix)) . '\'); return false;' : (BWG()->wp_editor_exists ? 'return bwg_reset_watermark_all(0)' : 'alert(\'' . addslashes(__('Image edit functionality is not supported by your web host.', BWG()->prefix)) . '\'); return false;')); ?>"
                           value="<?php _e('Reset Watermark', BWG()->prefix); ?>"/>
                  </div>
                  <p class="description"><?php _e('Mark the position where the watermark should appear on images.', BWG()->prefix) ?></p>
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-50">
              <span id="preview_built_in_watermark" style="display:table-cell; background-image:url('<?php echo BWG()->plugin_url .    '/images/watermark_preview.jpg'?>');background-size:100% 100%;width:400px;height:400px;padding-top: 4px; position:relative;"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
	  <div id="loading_div" class="bwg_show"></div>
    <input id="recreate" name="recreate" type="hidden" value="" />
    <input id="watermark" name="watermark" type="hidden" value="" />
    <input id="active_tab" name="active_tab" type="hidden" value="<?php echo $params['active_tab']; ?>" />
    <input id="gallery_type" name="gallery_type" type="hidden" value="<?php echo $params['gallery_type']; ?>" />
    <input id="album_type" name="album_type" type="hidden" value="<?php echo $params['album_type']; ?>" />
    <script>
      var bwg_options_url_ajax = '<?php echo $options_url_ajax; ?>';
      function bwg_add_built_in_watermark_image(files) {
        document.getElementById("built_in_watermark_url").value = '<?php echo BWG()->upload_url; ?>' + files[0]['url'];
      }
      function bwg_add_watermark_image(files) {
        document.getElementById("watermark_url").value = '<?php echo BWG()->upload_url; ?>' + files[0]['url'];
      }
      jQuery(function () {		
        bwg_inputs();
        bwg_watermark('watermark_type_<?php echo $row->watermark_type ?>');
        bwg_built_in_watermark('watermark_type_<?php echo $row->built_in_watermark_type ?>');
        bwg_enable_disable(<?php echo $row->popup_fullscreen? "'none', 'tr_popup_dimensions', 'show_search_box_1'" : "'', 'tr_popup_dimensions', 'popup_fullscreen_0'" ?>);
        bwg_enable_disable(<?php echo $row->show_search_box ? "'', 'tr_search_box_width', 'show_search_box_1'" : "'none', 'tr_search_box_width', 'show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->show_search_box ? "'', 'tr_search_box_placeholder', 'show_search_box_1'" : "'none', 'tr_search_box_placeholder', 'show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->masonry_show_search_box ? "'', 'tr_masonry_search_box_width', 'masonry_show_search_box_1'" : "'none', 'tr_masonry_search_box_width', 'masonry_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->masonry_show_search_box ? "'', 'tr_masonry_search_box_placeholder', 'masonry_show_search_box_1'" : "'none', 'tr_masonry_search_box_placeholder', 'masonry_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->mosaic_show_search_box ? "'', 'tr_mosaic_search_box_width', 'mosaic_show_search_box_1'" : "'none', 'tr_mosaic_search_box_width', 'mosaic_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->mosaic_show_search_box ? "'', 'tr_mosaic_search_box_placeholder', 'mosaic_show_search_box_1'" : "'none', 'tr_mosaic_search_box_placeholder', 'mosaic_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->image_browser_show_search_box ? "'', 'tr_image_browser_search_box_width', 'image_browser_show_search_box_1'" : "'none', 'tr_image_browser_search_box_width', 'image_browser_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->image_browser_show_search_box ? "'', 'tr_image_browser_search_box_placeholder', 'image_browser_show_search_box_1'" : "'none', 'tr_image_browser_search_box_placeholder', 'image_browser_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->blog_style_show_search_box ? "'', 'tr_blog_style_search_box_width', 'blog_style_show_search_box_1'" : "'none', 'tr_blog_style_search_box_width', 'blog_style_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->blog_style_show_search_box ? "'', 'tr_blog_style_search_box_placeholder', 'blog_style_show_search_box_1'" : "'none', 'tr_blog_style_search_box_placeholder', 'blog_style_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_show_search_box ? "'', 'tr_album_search_box_width', 'album_show_search_box_1'" : "'none', 'tr_album_search_box_width', 'album_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_show_search_box ? "'', 'tr_album_search_box_placeholder', 'album_show_search_box_1'" : "'none', 'tr_album_search_box_placeholder', 'album_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_masonry_show_search_box ? "'', 'tr_album_masonry_search_box_width', 'album_masonry_show_search_box_1'" : "'none', 'tr_album_masonry_search_box_width', 'album_masonry_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_masonry_show_search_box ? "'', 'tr_album_masonry_search_box_placeholder', 'album_masonry_show_search_box_1'" : "'none', 'tr_album_masonry_search_box_placeholder', 'album_masonry_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_extended_show_search_box ? "'', 'tr_album_extended_search_box_width', 'album_extended_show_search_box_1'" : "'none', 'tr_album_extended_search_box_width', 'album_extended_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->album_extended_show_search_box ? "'', 'tr_album_extended_search_box_placeholder', 'album_extended_show_search_box_1'" : "'none', 'tr_album_extended_search_box_placeholder', 'album_extended_show_search_box_0'" ?>);
        bwg_enable_disable(<?php echo $row->lazyload_images ? "'', 'tr_lazyload_images_count', 'lazyload_images_1'" : "'none', 'tr_lazyload_images_count', 'lazyload_images_0'" ?>);
        bwg_enable_disable(<?php echo $row->preload_images ? "'', 'tr_preload_images_count', 'preload_images_1'" : "'none', 'tr_preload_images_count', 'preload_images_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_fullscreen', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_fullscreen', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_info', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_info', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_download', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_download', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_fullsize_image', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_fullsize_image', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_comment', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_comment', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? ($row->popup_enable_comment ? "'', 'tr_comment_moderation', 'popup_enable_comment_1'" : "'none', 'tr_comment_moderation', 'popup_enable_comment_0'") : "'none', 'tr_comment_moderation', 'popup_enable_comment_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? ($row->popup_enable_comment ? "'', 'tr_popup_email', 'popup_enable_comment_1'" : "'none', 'tr_popup_email', 'popup_enable_comment_0'") : "'none', 'tr_popup_email', 'popup_enable_comment_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? ($row->popup_enable_comment ? "'', 'tr_popup_captcha', 'popup_enable_comment_1'" : "'none', 'tr_popup_captcha', 'popup_enable_comment_0'") : "'none', 'tr_popup_captcha', 'popup_enable_comment_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_facebook', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_facebook', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_twitter', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_twitter', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_google', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_google', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_pinterest', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_pinterest', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_ctrl_btn ? "'', 'tr_popup_thumblr', 'popup_enable_ctrl_btn_1'" : "'none', 'tr_popup_thumblr', 'popup_enable_ctrl_btn_0'" ?>);
        bwg_enable_disable(<?php echo $row->popup_enable_filmstrip ? "'', 'tr_popup_filmstrip_height', 'popup_enable_filmstrip_1'" : "'none', 'tr_popup_filmstrip_height', 'popup_enable_filmstrip_0'" ?>);
        bwg_enable_disable(<?php echo $row->slideshow_enable_filmstrip ? "'', 'tr_slideshow_filmstrip_height', 'slideshow_enable_filmstrip_yes'" : "'none', 'tr_slideshow_filmstrip_height', 'slideshow_enable_filmstrip_no'" ?>);
        bwg_enable_disable(<?php echo $row->slideshow_enable_title ? "'', 'tr_slideshow_title_position', 'slideshow_enable_title_yes'" : "'none', 'tr_slideshow_title_position', 'slideshow_enable_title_no'" ?>);
        bwg_enable_disable(<?php echo $row->slideshow_enable_description ? "'', 'tr_slideshow_description_position', 'slideshow_enable_description_yes'" : "'none', 'tr_slideshow_description_position', 'slideshow_enable_description_no'" ?>);
        bwg_enable_disable(<?php echo $row->slideshow_enable_music ? "'', 'tr_slideshow_music_url', 'slideshow_enable_music_yes'" : "'none', 'tr_slideshow_music_url', 'slideshow_enable_music_no'" ?>);
        bwg_enable_disable(<?php echo $row->slideshow_enable_ctrl ? "'', 'tr_autohide_slideshow_navigation', 'slideshow_enable_ctrl_yes'" : "'none', 'tr_autohide_slideshow_navigation', 'slideshow_enable_ctrl_no'" ?>);
        bwg_enable_disable(<?php echo $row->enable_addthis ? "'', 'tr_addthis_profile_id', 'enable_addthis_yes'" : "'none', 'tr_addthis_profile_id', 'enable_addthis_no'" ?>);
        bwg_enable_disable(<?php echo $row->thumb_click_action == 'redirect_to_url' ? "'', 'tr_thumb_link_target', 'thumb_click_action_2'" : "'none', 'tr_thumb_link_target', 'thumb_click_action_" . ($row->thumb_click_action == 'open_lightbox' ? 1 : 3) . "'"; ?>);
        bwg_enable_disable(<?php echo $row->image_enable_page == '2' ? "'', 'tr_load_more_image_count', 'image_enable_page_loadmore'" : "'none', 'tr_load_more_image_count', 'image_enable_page_" . ($row->image_enable_page == '0' ? 'no' : ($row->image_enable_page == '1' ? 'yes' : 'scroll_load')) . "'"; ?>);
        bwg_enable_disable(<?php echo $row->masonry_image_enable_page == '2' ? "'', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_loadmore'" : "'none', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_" . ($row->masonry_image_enable_page == '0' ? 'no' : ($row->masonry_image_enable_page == '1' ? 'yes' : 'scroll_load')) . "'"; ?>);
        bwg_enable_disable(<?php echo $row->mosaic_image_enable_page == '2' ? "'', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_loadmore'" : "'none', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_" . ($row->mosaic_image_enable_page == '0' ? 'no' : ($row->mosaic_image_enable_page == '1' ? 'yes' : 'scroll_load')) . "'"; ?>);
        bwg_enable_disable(<?php echo $row->blog_style_enable_page == '2' ? "'', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_2'" : "'none', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_" . $row->blog_style_enable_page . "'"; ?>);
		bwg_enable_disable(<?php echo $row->masonry == 'horizontal' ? "'none', 'bwg-vertical-block-masonry', 'masonry_1'" : "'', 'bwg-vertical-block-masonry', 'masonry_0'"; ?>);
        preview_watermark();
        preview_built_in_watermark();
        bwg_show_hide_roles();
        bwg_pagination_description(jQuery('#image_enable_page_<?php echo $row->image_enable_page; ?>'));
        bwg_pagination_description(jQuery('#masonry_image_enable_page_<?php echo $row->masonry_image_enable_page; ?>'));
        bwg_pagination_description(jQuery('#mosaic_image_enable_page_<?php echo $row->mosaic_image_enable_page; ?>'));
        bwg_pagination_description(jQuery('#blog_style_enable_page_<?php echo $row->blog_style_enable_page; ?>'));
        bwg_pagination_description(jQuery('#album_enable_page_<?php echo $row->album_enable_page; ?>'));
        bwg_pagination_description(jQuery('#album_masonry_enable_page_<?php echo $row->album_masonry_enable_page; ?>'));
        bwg_pagination_description(jQuery('#album_extended_enable_page_<?php echo $row->album_extended_enable_page; ?>'));
      });
		<?php if ( WDWLibrary::get('instagram_token') || WDWLibrary::get('code') ) { ?>
      jQuery(window).on('load',function(){
        var advanced_tab_index = 5;
        jQuery( ".bwg_tabs" ).tabs({ active: advanced_tab_index });
      });
		<?php } ?>
    </script>
    <?php
  }

  private static function get_effects() {
    return  array(
      'none' => __('None',BWG()->prefix),
      'cubeH' => __('Cube Horizontal',BWG()->prefix),
      'cubeV' => __('Cube Vertical',BWG()->prefix),
      'fade' => __('Fade',BWG()->prefix),
      'sliceH' => __('Slice Horizontal',BWG()->prefix),
      'sliceV' => __('Slice Vertical',BWG()->prefix),
      'slideH' => __('Slide Horizontal',BWG()->prefix),
      'slideV' => __('Slide Vertical',BWG()->prefix),
      'scaleOut' => __('Scale Out',BWG()->prefix),
      'scaleIn' => __('Scale In',BWG()->prefix),
      'blockScale' => __('Block Scale',BWG()->prefix),
      'kaleidoscope' => __('Kaleidoscope',BWG()->prefix),
      'fan' => __('Fan',BWG()->prefix),
      'blindH' => __('Blind Horizontal',BWG()->prefix),
      'blindV' => __('Blind Vertical',BWG()->prefix),
      'random' => __('Random',BWG()->prefix),
      );
  }

  public static function gallery_options($row) {
    $effects = self::get_effects();
	  $zipArchiveClass = ( class_exists('ZipArchive') ) ? TRUE : FALSE;
    ?>
      <div id="thumbnails_options" class="gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="thumb_width"><?php _e('Thumbnail dimensions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="thumb_width" id="thumb_width" value="<?php echo $row->thumb_width; ?>" min="0" /><span>x</span>
                <input type="number" name="thumb_height" id="thumb_height" value="<?php echo $row->thumb_height; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('The default dimensions of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="image_column_number"><?php _e('Number of image columns', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="image_column_number" id="image_column_number" value="<?php echo $row->image_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of image columns in galleries. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_enable_page" id="image_enable_page_0" value="0" <?php if ($row->image_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_load_more_image_count', 'image_enable_page_0'); bwg_pagination_description(this);" /><label for="image_enable_page_0" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                <input type="radio" name="image_enable_page" id="image_enable_page_1" value="1" <?php if ($row->image_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_load_more_image_count', 'image_enable_page_1'); bwg_pagination_description(this);" /><label for="image_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input type="radio" name="image_enable_page" id="image_enable_page_2" value="2" <?php if ($row->image_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_load_more_image_count', 'image_enable_page_2'); bwg_pagination_description(this);" /><label for="image_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input type="radio" name="image_enable_page" id="image_enable_page_3" value="3" <?php if ($row->image_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_load_more_image_count', 'image_enable_page_3'); bwg_pagination_description(this);" /><label for="image_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="image_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="image_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="image_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="image_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="images_per_page" id="images_per_page" value="<?php echo $row->images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_load_more_image_count">
            <div class="wd-group">
              <label class="wd-label" for="load_more_image_count"><?php _e('Images per load', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="load_more_image_count" id="load_more_image_count" value="<?php echo $row->load_more_image_count; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per load on galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
				<div class="wd-width-65">
					<label class="wd-label" for="sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
					<select name="sort_by" id="sort_by">
						<option value="order" <?php if ($row->sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
						<option value="alt" <?php if ($row->sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
						<option value="date" <?php if ($row->sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
						<option value="filename" <?php if ($row->sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
						<option value="size" <?php if ($row->sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
						<option value="random" <?php if ($row->sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				</div>
				<div class="wd-width-30">
					<select name="order_by" id="order_by">
						<option value="asc" <?php if ($row->order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_search_box" id="show_search_box_1" value="1" <?php if ($row->show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_search_box_width', 'show_search_box_1'); bwg_enable_disable('', 'tr_search_box_placeholder', 'show_search_box_1')" /><label for="show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_search_box" id="show_search_box_0" value="0" <?php if (!$row->show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_search_box_width', 'show_search_box_0'); bwg_enable_disable('none', 'tr_search_box_placeholder', 'show_search_box_0')" /><label for="show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="placeholder" id="placeholder" value="<?php echo $row->placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="search_box_width" id="search_box_width" value="<?php echo $row->search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_sort_images" id="show_sort_images_1" value="1" <?php if ($row->show_sort_images) echo 'checked="checked"'; ?> /><label for="show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_sort_images" id="show_sort_images_0" value="0" <?php if (!$row->show_sort_images) echo 'checked="checked"'; ?> /><label for="show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_tag_box" id="show_tag_box_1" value="1" <?php if ($row->show_tag_box) echo 'checked="checked"'; ?> /><label for="show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_tag_box" id="show_tag_box_0" value="0" <?php if (!$row->show_tag_box) echo 'checked="checked"'; ?> /><label for="show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="showthumbs_name" id="thumb_name_yes" value="1" <?php if ($row->showthumbs_name) echo 'checked="checked"'; ?> /><label for="thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="showthumbs_name" id="thumb_name_no" value="0"  <?php if (!$row->showthumbs_name) echo 'checked="checked"'; ?> /><label for="thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_gallery_description" id="show_gallery_description_1" value="1" <?php if ($row->show_gallery_description) echo 'checked="checked"'; ?> /><label for="show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_gallery_description" id="show_gallery_description_0" value="0" <?php if (!$row->show_gallery_description) echo 'checked="checked"'; ?> /><label for="show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_title_show_hover" id="image_title_show_hover_1" value="hover" <?php if ($row->image_title_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="image_title_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="image_title_show_hover" id="image_title_show_hover_0" value="show" <?php if ($row->image_title_show_hover == "show") echo 'checked="checked"'; ?> /><label for="image_title_show_hover_0" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                <input type="radio" name="image_title_show_hover" id="image_title_show_hover_2" value="none" <?php if ($row->image_title_show_hover == "none") echo 'checked="checked"'; ?> /><label for="image_title_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image descriptions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_thumb_description" id="thumb_desc_1" value="1" <?php if ($row->show_thumb_description) echo 'checked="checked"'; ?> /><label for="thumb_desc_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_thumb_description" id="thumb_desc_0" value="0" <?php if (!$row->show_thumb_description) echo 'checked="checked"'; ?> /><label for="thumb_desc_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to display descriptions under images.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Play icon on video thumbnails', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="play_icon" id="play_icon_yes" value="1" <?php if ($row->play_icon) echo 'checked="checked"'; ?> /><label for="play_icon_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="play_icon" id="play_icon_no" value="0" <?php if (!$row->play_icon) echo 'checked="checked"'; ?> /><label for="play_icon_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this option to add a Play button on thumbnails of videos.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="gallery_download" id="gallery_download_1" value="1" <?php if ($row->gallery_download) echo 'checked="checked"'; ?> /><label for="gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="gallery_download" id="gallery_download_0" value="0" <?php if (!$row->gallery_download) echo 'checked="checked"'; ?> /><label for="gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
          <?php
          if (function_exists('BWGEC')) {
            ?>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="ecommerce_icon_show_hover" id="ecommerce_icon_show_hover_1" value="hover" <?php if ($row->ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="ecommerce_icon_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <input type="radio" name="ecommerce_icon_show_hover" id="ecommerce_icon_show_hover_0" value="show" <?php if ($row->ecommerce_icon_show_hover == "show") echo 'checked="checked"'; ?> /><label for="ecommerce_icon_show_hover_0" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                  <input type="radio" name="ecommerce_icon_show_hover" id="ecommerce_icon_show_hover_2" value="none" <?php if ($row->ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><label for="ecommerce_icon_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div id="thumbnails_masonry_options" class="bwg-pro-views gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
<?php /*
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Masonry type', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="masonry" id="masonry_0" value="vertical" <?php if ($row->masonry == "vertical") echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'bwg-vertical-block-masonry', 'masonry_0');" /><label for="masonry_0" class="wd-radio-label"><?php _e('Vertical', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="masonry" id="masonry_1" value="horizontal" <?php if ($row->masonry == "horizontal") echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'bwg-vertical-block-masonry', 'masonry_1');" /><label for="masonry_1" class="wd-radio-label"><?php _e('Horizontal', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Select the type of Masonry galleries, Vertical or Horizontal.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
*/
      ?>
      <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="masonry_thumb_size"><?php _e('Thumbnail size', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="masonry_thumb_size" id="masonry_thumb_size" value="<?php echo $row->masonry_thumb_size; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('The default size of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label masonry_col_num" style="<?php echo ($row->masonry == "vertical") ? '' : 'display:none'; ?>" for="masonry_image_column_number"><?php _e('Number of image columns', BWG()->prefix); ?></label>
              <label class="wd-label masonry_row_num" style="<?php echo ($row->masonry == "vertical") ? 'display:none' : ''; ?>" for="masonry_image_column_number"><?php _e('Number of image rows', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="masonry_image_column_number" id="masonry_image_column_number" value="<?php echo $row->masonry_image_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of image columns (or rows) in galleries. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_image_enable_page" id="masonry_image_enable_page_0" value="0" <?php if ($row->masonry_image_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_0'); bwg_pagination_description(this);" /><label for="masonry_image_enable_page_0" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_image_enable_page" id="masonry_image_enable_page_1" value="1" <?php if ($row->masonry_image_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_1'); bwg_pagination_description(this);" /><label for="masonry_image_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_image_enable_page" id="masonry_image_enable_page_2" value="2" <?php if ($row->masonry_image_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_2'); bwg_pagination_description(this);" /><label for="masonry_image_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_image_enable_page" id="masonry_image_enable_page_3" value="3" <?php if ($row->masonry_image_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_masonry_load_more_image_count', 'masonry_image_enable_page_3'); bwg_pagination_description(this);" /><label for="masonry_image_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="masonry_image_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="masonry_image_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="masonry_image_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="masonry_image_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_masonry_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="masonry_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="masonry_images_per_page" id="masonry_images_per_page" value="<?php echo $row->masonry_images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_masonry_load_more_image_count">
            <div class="wd-group">
              <label class="wd-label" for="masonry_load_more_image_count"><?php _e('Images per load', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="masonry_load_more_image_count" id="masonry_load_more_image_count" value="<?php echo $row->masonry_load_more_image_count; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per load on galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="masonry_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
              <div class="wd-width-65">
			  <select name="masonry_sort_by" id="masonry_sort_by">
                <option value="order" <?php if ($row->masonry_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
                <option value="alt" <?php if ($row->masonry_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
                <option value="date" <?php if ($row->masonry_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
                <option value="filename" <?php if ($row->masonry_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
                <option value="size" <?php if ($row->masonry_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
                <option value="random" <?php if ($row->masonry_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
              </select>
			  </div>
			  <div class="wd-width-30">
					<select name="masonry_order_by" id="masonry_order_by">
						<option value="asc" <?php if ($row->masonry_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->masonry_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
			  </div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_show_search_box" id="masonry_show_search_box_1" value="1" <?php if ($row->masonry_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_masonry_search_box_width', 'masonry_show_search_box_1'); bwg_enable_disable('', 'tr_masonry_search_box_placeholder', 'masonry_show_search_box_1')" /><label for="masonry_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_show_search_box" id="masonry_show_search_box_0" value="0" <?php if (!$row->masonry_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_masonry_search_box_width', 'masonry_show_search_box_0'); bwg_enable_disable('none', 'tr_masonry_search_box_placeholder', 'masonry_show_search_box_0')" /><label for="masonry_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_masonry_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="masonry_placeholder" id="masonry_placeholder" value="<?php echo $row->masonry_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_masonry_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="masonry_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="masonry_search_box_width" id="masonry_search_box_width" value="<?php echo $row->masonry_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_show_sort_images" id="masonry_show_sort_images_1" value="1" <?php if ($row->masonry_show_sort_images) echo 'checked="checked"'; ?> /><label for="masonry_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_show_sort_images" id="masonry_show_sort_images_0" value="0" <?php if (!$row->masonry_show_sort_images) echo 'checked="checked"'; ?> /><label for="masonry_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_show_tag_box" id="masonry_show_tag_box_1" value="1" <?php if ($row->masonry_show_tag_box) echo 'checked="checked"'; ?> /><label for="masonry_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_show_tag_box" id="masonry_show_tag_box_0" value="0" <?php if (!$row->masonry_show_tag_box) echo 'checked="checked"'; ?> /><label for="masonry_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_show_gallery_title" id="masonry_thumb_name_yes" value="1" <?php if ($row->masonry_show_gallery_title) echo 'checked="checked"'; ?> /><label for="masonry_thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_show_gallery_title" id="masonry_thumb_name_no" value="0"  <?php if (!$row->masonry_show_gallery_title) echo 'checked="checked"'; ?> /><label for="masonry_thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_show_gallery_description" id="masonry_show_gallery_description_1" value="1" <?php if ($row->masonry_show_gallery_description) echo 'checked="checked"'; ?> /><label for="masonry_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_show_gallery_description" id="masonry_show_gallery_description_0" value="0" <?php if (!$row->masonry_show_gallery_description) echo 'checked="checked"'; ?> /><label for="masonry_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 bwg-vertical-block-masonry">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_image_title" id="masonry_image_title_0" value="hover" <?php if ($row->masonry_image_title == "hover") echo 'checked="checked"'; ?> /><label for="masonry_image_title_0" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_image_title" id="masonry_image_title_1" value="show" <?php if ($row->masonry_image_title == "show") echo 'checked="checked"'; ?> /><label for="masonry_image_title_1" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_image_title" id="masonry_image_title_2" value="none" <?php if ($row->masonry_image_title == "none") echo 'checked="checked"'; ?> /><label for="masonry_image_title_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>		  
          <div class="wd-box-content wd-width-100 bwg-vertical-block-masonry <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_show_masonry_thumb_description">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image descriptions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="show_masonry_thumb_description" id="masonry_thumb_desc_1" value="1" <?php if ($row->show_masonry_thumb_description) echo 'checked="checked"'; ?> /><label for="masonry_thumb_desc_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="show_masonry_thumb_description" id="masonry_thumb_desc_0" value="0" <?php if (!$row->show_masonry_thumb_description) echo 'checked="checked"'; ?> /><label for="masonry_thumb_desc_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to display descriptions under images.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Play icon on video thumbnails', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="masonry_play_icon" id="masonry_play_icon_yes" value="1" <?php if ($row->masonry_play_icon) echo 'checked="checked"'; ?> /><label for="masonry_play_icon_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="masonry_play_icon" id="masonry_play_icon_no" value="0" <?php if (!$row->masonry_play_icon) echo 'checked="checked"'; ?> /><label for="masonry_play_icon_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this option to add a Play button on thumbnails of videos.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="masonry_gallery_download" id="masonry_gallery_download_1" value="1" <?php if ($row->masonry_gallery_download) echo 'checked="checked"'; ?> /><label for="masonry_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="masonry_gallery_download" id="masonry_gallery_download_0" value="0" <?php if (!$row->masonry_gallery_download) echo 'checked="checked"'; ?> /><label for="masonry_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
          <?php
          if (function_exists('BWGEC')) {
            ?>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="masonry_ecommerce_icon_show_hover" id="masonry_ecommerce_icon_show_hover_1" value="hover" <?php if ($row->masonry_ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="masonry_ecommerce_icon_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <input type="radio" name="masonry_ecommerce_icon_show_hover" id="masonry_ecommerce_icon_show_hover_2" value="none" <?php if ($row->masonry_ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><label for="masonry_ecommerce_icon_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div id="thumbnails_mosaic_options" class="bwg-pro-views gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Mosaic gallery type', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="mosaic" id="mosaic_0" value="vertical" <?php if ($row->mosaic == "vertical") echo 'checked="checked"'; ?> /><label for="mosaic_0" class="wd-radio-label"><?php _e('Vertical', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="mosaic" id="mosaic_1" value="horizontal" <?php if ($row->mosaic == "horizontal") echo 'checked="checked"'; ?> /><label for="mosaic_1" class="wd-radio-label"><?php _e('Horizontal', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Select the type of Mosaic galleries, Vertical or Horizontal.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Resizable mosaic', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="resizable_mosaic" id="resizable_mosaic_1" value="1" <?php if ($row->resizable_mosaic == "1") echo 'checked="checked"'; ?> /><label for="resizable_mosaic_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="resizable_mosaic" id="resizable_mosaic_0" value="0" <?php if ($row->resizable_mosaic == "0") echo 'checked="checked"'; ?> /><label for="resizable_mosaic_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('If this setting is enabled, Photo Gallery resizes all thumbnail images on Mosaic galleries, without modifying their initial display.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_total_width"><?php _e('Width of mosaic galleries', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="mosaic_total_width" id="mosaic_total_width" value="<?php echo $row->mosaic_total_width; ?>" min="0" /><span>%</span>
              </div>
              <p class="description"><?php _e('The total width of mosaic galleries as a percentage of container\'s width.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_thumb_size"><?php _e('Thumbnail size', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="mosaic_thumb_size" id="mosaic_thumb_size" value="<?php echo $row->mosaic_thumb_size; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('The default size of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_image_enable_page" id="mosaic_image_enable_page_0" value="0" <?php if ($row->mosaic_image_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_0'); bwg_pagination_description(this);" /><label for="mosaic_image_enable_page_0" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_image_enable_page" id="mosaic_image_enable_page_1" value="1" <?php if ($row->mosaic_image_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_1'); bwg_pagination_description(this);" /><label for="mosaic_image_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_image_enable_page" id="mosaic_image_enable_page_2" value="2" <?php if ($row->mosaic_image_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_2'); bwg_pagination_description(this);" /><label for="mosaic_image_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_image_enable_page" id="mosaic_image_enable_page_3" value="3" <?php if ($row->mosaic_image_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_mosaic_load_more_image_count', 'mosaic_image_enable_page_3'); bwg_pagination_description(this);" /><label for="mosaic_image_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="mosaic_image_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="mosaic_image_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="mosaic_image_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="mosaic_image_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_mosaic_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="mosaic_images_per_page" id="mosaic_images_per_page" value="<?php echo $row->mosaic_images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_mosaic_load_more_image_count">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_load_more_image_count"><?php _e('Images per load', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="mosaic_load_more_image_count" id="mosaic_load_more_image_count" value="<?php echo $row->mosaic_load_more_image_count; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per load on galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
			  <div class="wd-width-65">
              <select name="mosaic_sort_by" id="mosaic_sort_by">
                <option value="order" <?php if ($row->mosaic_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
                <option value="alt" <?php if ($row->mosaic_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
                <option value="date" <?php if ($row->mosaic_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
                <option value="filename" <?php if ($row->mosaic_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
                <option value="size" <?php if ($row->mosaic_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
                <option value="random" <?php if ($row->mosaic_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
              </select>
			  </div>
			  <div class="wd-width-30">
					<select name="mosaic_order_by" id="mosaic_order_by">
						<option value="asc" <?php if ($row->mosaic_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->mosaic_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
			  </div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_show_search_box" id="mosaic_show_search_box_1" value="1" <?php if ($row->mosaic_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_mosaic_search_box_width', 'mosaic_show_search_box_1'); bwg_enable_disable('', 'tr_mosaic_search_box_placeholder', 'mosaic_show_search_box_1')" /><label for="mosaic_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_show_search_box" id="mosaic_show_search_box_0" value="0" <?php if (!$row->mosaic_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_mosaic_search_box_width', 'mosaic_show_search_box_0'); bwg_enable_disable('none', 'tr_mosaic_search_box_placeholder', 'mosaic_show_search_box_0')" /><label for="mosaic_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_mosaic_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="mosaic_placeholder" id="mosaic_placeholder" value="<?php echo $row->mosaic_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_mosaic_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="mosaic_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="mosaic_search_box_width" id="mosaic_search_box_width" value="<?php echo $row->mosaic_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_show_sort_images" id="mosaic_show_sort_images_1" value="1" <?php if ($row->mosaic_show_sort_images) echo 'checked="checked"'; ?> /><label for="mosaic_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_show_sort_images" id="mosaic_show_sort_images_0" value="0" <?php if (!$row->mosaic_show_sort_images) echo 'checked="checked"'; ?> /><label for="mosaic_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_show_tag_box" id="mosaic_show_tag_box_1" value="1" <?php if ($row->mosaic_show_tag_box) echo 'checked="checked"'; ?> /><label for="mosaic_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_show_tag_box" id="mosaic_show_tag_box_0" value="0" <?php if (!$row->mosaic_show_tag_box) echo 'checked="checked"'; ?> /><label for="mosaic_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_show_gallery_title" id="mosaic_thumb_name_yes" value="1" <?php if ($row->mosaic_show_gallery_title) echo 'checked="checked"'; ?> /><label for="mosaic_thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_show_gallery_title" id="mosaic_thumb_name_no" value="0"  <?php if (!$row->mosaic_show_gallery_title) echo 'checked="checked"'; ?> /><label for="mosaic_thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_show_gallery_description" id="mosaic_show_gallery_description_1" value="1" <?php if ($row->mosaic_show_gallery_description) echo 'checked="checked"'; ?> /><label for="mosaic_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_show_gallery_description" id="mosaic_show_gallery_description_0" value="0" <?php if (!$row->mosaic_show_gallery_description) echo 'checked="checked"'; ?> /><label for="mosaic_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_image_title_show_hover" id="mosaic_image_title_show_hover_1" value="hover" <?php if ($row->mosaic_image_title_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="mosaic_image_title_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_image_title_show_hover" id="mosaic_image_title_show_hover_0" value="none" <?php if ($row->mosaic_image_title_show_hover == "none") echo 'checked="checked"'; ?> /><label for="mosaic_image_title_show_hover_0" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Play icon on video thumbnails', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="mosaic_play_icon" id="mosaic_play_icon_yes" value="1" <?php if ($row->mosaic_play_icon) echo 'checked="checked"'; ?> /><label for="mosaic_play_icon_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="mosaic_play_icon" id="mosaic_play_icon_no" value="0" <?php if (!$row->mosaic_play_icon) echo 'checked="checked"'; ?> /><label for="mosaic_play_icon_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this option to add a Play button on thumbnails of videos.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="mosaic_gallery_download" id="mosaic_gallery_download_1" value="1" <?php if ($row->mosaic_gallery_download) echo 'checked="checked"'; ?> /><label for="mosaic_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="mosaic_gallery_download" id="mosaic_gallery_download_0" value="0" <?php if (!$row->mosaic_gallery_download) echo 'checked="checked"'; ?> /><label for="mosaic_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
			      </div>
          </div>
          <?php
          if (function_exists('BWGEC')) {
            ?>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="mosaic_ecommerce_icon_show_hover" id="mosaic_ecommerce_icon_show_hover_1" value="hover" <?php if ($row->mosaic_ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="mosaic_ecommerce_icon_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <input type="radio" name="mosaic_ecommerce_icon_show_hover" id="mosaic_ecommerce_icon_show_hover_2" value="none" <?php if ($row->mosaic_ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><label for="mosaic_ecommerce_icon_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div id="slideshow_options" class="gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_type"><?php _e('Slideshow effect', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <select name="slideshow_type" id="slideshow_type">
                  <?php
                  foreach ($effects as $key => $effect) {
                    ?>
                    <option value="<?php echo $key; ?>"
                      <?php echo (!BWG()->is_pro && $key != 'none' && $key != 'fade') ? 'disabled="disabled" title="' . __('This effect is disabled in free version.', BWG()->prefix) . '"' : ''; ?>
                      <?php if ($row->slideshow_type == $key) echo 'selected="selected"'; ?>><?php echo __($effect, BWG()->prefix); ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              <p class="description"><?php _e('Select the animation effect for your slideshow.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_effect_duration"><?php _e('Effect duration', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="slideshow_effect_duration" id="slideshow_effect_duration" value="<?php echo $row->slideshow_effect_duration; ?>" min="0" step="0.1" /><span>sec.</span>
              </div>
              <p class="description"><?php _e('Set the duration of your slideshow animation effect.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_interval"><?php _e('Time interval', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="slideshow_interval" id="slideshow_interval" value="<?php echo $row->slideshow_interval; ?>" min="0" /><span>sec.</span>
              </div>
              <p class="description"><?php _e('Specify the time interval between slides in Photo Gallery\'s Slideshow view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_width"><?php _e('Slideshow dimensions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="slideshow_width" id="slideshow_width" value="<?php echo $row->slideshow_width; ?>" min="0" /><span>px</span>
                <input type="number" name="slideshow_height" id="slideshow_height" value="<?php echo $row->slideshow_height; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Set the default dimensions of your slideshow galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
              <div class="wd-width-65">
				<select name="slideshow_sort_by" id="slideshow_sort_by">
					<option value="order" <?php if ($row->slideshow_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
					<option value="alt" <?php if ($row->slideshow_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
					<option value="date" <?php if ($row->slideshow_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
					<option value="filename" <?php if ($row->slideshow_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
					<option value="size" <?php if ($row->slideshow_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
					<option value="random" <?php if ($row->slideshow_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
				</select>
			  </div>
              <div class="wd-width-30">
					<select name="slideshow_order_by" id="slideshow_order_by">
						<option value="asc" <?php if ($row->slideshow_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->slideshow_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
			  </div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable autoplay', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_autoplay" id="slideshow_enable_autoplay_yes" value="1" <?php if ($row->slideshow_enable_autoplay) echo 'checked="checked"'; ?> /><label for="slideshow_enable_autoplay_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_autoplay" id="slideshow_enable_autoplay_no" value="0" <?php if (!$row->slideshow_enable_autoplay) echo 'checked="checked"'; ?> /><label for="slideshow_enable_autoplay_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this option to autoplay slideshow galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable shuffle', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_shuffle" id="slideshow_enable_shuffle_yes" value="1" <?php if ($row->slideshow_enable_shuffle) echo 'checked="checked"'; ?> /><label for="slideshow_enable_shuffle_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_shuffle" id="slideshow_enable_shuffle_no" value="0" <?php if (!$row->slideshow_enable_shuffle) echo 'checked="checked"'; ?> /><label for="slideshow_enable_shuffle_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('The slideshow images will be shuffled in case this setting is enabled.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable control buttons', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_ctrl" id="slideshow_enable_ctrl_yes" value="1" <?php if ($row->slideshow_enable_ctrl) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_autohide_slideshow_navigation', 'slideshow_enable_ctrl_yes');" /><label for="slideshow_enable_ctrl_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_ctrl" id="slideshow_enable_ctrl_no" value="0" <?php if (!$row->slideshow_enable_ctrl) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_autohide_slideshow_navigation', 'slideshow_enable_ctrl_no');" /><label for="slideshow_enable_ctrl_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to show control buttons on your slideshow galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_autohide_slideshow_navigation">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Next / Previous buttons', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="autohide_slideshow_navigation" id="autohide_slideshow_navigation_1" value="1" <?php if ($row->autohide_slideshow_navigation) echo 'checked="checked"'; ?> /><label for="autohide_slideshow_navigation_1" class="wd-radio-label"><?php _e('On hover', BWG()->prefix); ?></label>
                <input type="radio" name="autohide_slideshow_navigation" id="autohide_slideshow_navigation_0" value="0" <?php if (!$row->autohide_slideshow_navigation) echo 'checked="checked"'; ?> /><label for="autohide_slideshow_navigation_0" class="wd-radio-label"><?php _e('Always', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display Next/Previous buttons on your slideshow galleries activating this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable slideshow filmstrip', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="slideshow_enable_filmstrip" id="slideshow_enable_filmstrip_yes" value="1" <?php if ($row->slideshow_enable_filmstrip) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_slideshow_filmstrip_height', 'slideshow_enable_filmstrip_yes')" /><label for="slideshow_enable_filmstrip_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="slideshow_enable_filmstrip" id="slideshow_enable_filmstrip_no" value="0" <?php if (!$row->slideshow_enable_filmstrip) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_slideshow_filmstrip_height', 'slideshow_enable_filmstrip_no')" /><label for="slideshow_enable_filmstrip_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Add a filmstrip with image thumbnails to your slideshow galleries by enabling this option.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_slideshow_filmstrip_height">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_filmstrip_height"><?php _e('Slideshow filmstrip size', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="slideshow_filmstrip_height" id="slideshow_filmstrip_height" value="<?php echo $row->slideshow_filmstrip_height; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Set the size of your filmstrip. If the filmstrip is horizontal, this indicates its height, whereas for vertical filmstrips it sets the width.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_title" id="slideshow_enable_title_yes" value="1" <?php if ($row->slideshow_enable_title) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_slideshow_title_position', 'slideshow_enable_title_yes')" /><label for="slideshow_enable_title_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_title" id="slideshow_enable_title_no" value="0" <?php if (!$row->slideshow_enable_title) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_slideshow_title_position', 'slideshow_enable_title_no')" /><label for="slideshow_enable_title_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to display titles of images in Slideshow view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_slideshow_title_position">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Title position', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <table class="bwg_position_table">
                  <tbody>
                  <tr>
                    <td><input type="radio" value="top-left" id="slideshow_title_topLeft" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "top-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="top-center" id="slideshow_title_topCenter" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "top-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="top-right" id="slideshow_title_topRight" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "top-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  <tr>
                    <td><input type="radio" value="middle-left" id="slideshow_title_midLeft" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "middle-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="middle-center" id="slideshow_title_midCenter" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "middle-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="middle-right" id="slideshow_title_midRight" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "middle-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  <tr>
                    <td><input type="radio" value="bottom-left" id="slideshow_title_botLeft" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "bottom-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="bottom-center" id="slideshow_title_botCenter" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "bottom-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="bottom-right" id="slideshow_title_botRight" name="slideshow_title_position" <?php if ($row->slideshow_title_position == "bottom-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <p class="description"><?php _e('Set the position of image titles in Slideshow view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_slideshow_full_width_title">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Full width title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_title_full_width" id="slideshow_title_full_width_1" value="1" <?php if ($row->slideshow_title_full_width) echo 'checked="checked"'; ?>  /><label for="slideshow_title_full_width_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_title_full_width" id="slideshow_title_full_width_0" value="0" <?php if (!$row->slideshow_title_full_width) echo 'checked="checked"'; ?>  /><label for="slideshow_title_full_width_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display image title based on the slideshow dimensions.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_description" id="slideshow_enable_description_yes" value="1" <?php if ($row->slideshow_enable_description) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_slideshow_description_position', 'slideshow_enable_description_yes')" /><label for="slideshow_enable_description_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_description" id="slideshow_enable_description_no" value="0" <?php if (!$row->slideshow_enable_description) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_slideshow_description_position', 'slideshow_enable_description_no')" /><label for="slideshow_enable_description_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to show descriptions of images in Slideshow view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_slideshow_description_position">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Description position', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <table class="bwg_position_table">
                  <tbody>
                  <tr>
                    <td><input type="radio" value="top-left" id="slideshow_description_topLeft" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "top-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="top-center" id="slideshow_description_topCenter" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "top-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="top-right" id="slideshow_description_topRight" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "top-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  <tr>
                    <td><input type="radio" value="middle-left" id="slideshow_description_midLeft" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "middle-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="middle-center" id="slideshow_description_midCenter" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "middle-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="middle-right" id="slideshow_description_midRight" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "middle-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  <tr>
                    <td><input type="radio" value="bottom-left" id="slideshow_description_botLeft" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "bottom-left") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="bottom-center" id="slideshow_description_botCenter" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "bottom-center") echo 'checked="checked"'; ?>></td>
                    <td><input type="radio" value="bottom-right" id="slideshow_description_botRight" name="slideshow_description_position" <?php if ($row->slideshow_description_position == "bottom-right") echo 'checked="checked"'; ?>></td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <p class="description"><?php _e('Set the position of image descriptions in Slideshow view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable slideshow Music', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="slideshow_enable_music" id="slideshow_enable_music_yes" value="1" <?php if ($row->slideshow_enable_music) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_slideshow_music_url', 'slideshow_enable_music_yes')" /><label for="slideshow_enable_music_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="slideshow_enable_music" id="slideshow_enable_music_no" value="0" <?php if (!$row->slideshow_enable_music) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_slideshow_music_url', 'slideshow_enable_music_no')"  /><label for="slideshow_enable_music_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enabling this option, you can have music playing along with your slideshow.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_slideshow_music_url">
            <div class="wd-group">
              <label class="wd-label" for="slideshow_audio_url"><?php _e('Audio URL', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" id="slideshow_audio_url" name="slideshow_audio_url" value="<?php echo $row->slideshow_audio_url; ?>" />
              </div>
              <p class="description"><?php _e('Provide the absolute URL of the audio file you would like to play with your slideshow.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="slideshow_gallery_download" id="slideshow_gallery_download_1" value="1" <?php if ($row->slideshow_gallery_download) echo 'checked="checked"'; ?> /><label for="slideshow_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="slideshow_gallery_download" id="slideshow_gallery_download_0" value="0" <?php if (!$row->slideshow_gallery_download) echo 'checked="checked"'; ?> /><label for="slideshow_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
			        <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div id="image_browser_options" class="gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="image_browser_width"><?php _e('Image width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="image_browser_width" id="image_browser_width" value="<?php echo $row->image_browser_width; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Specify the default width of images in Image Browser view.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_title_enable" id="image_browser_title_enable_1" value="1" <?php if ($row->image_browser_title_enable) echo 'checked="checked"'; ?> /><label for="image_browser_title_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_title_enable" id="image_browser_title_enable_0" value="0" <?php if (!$row->image_browser_title_enable) echo 'checked="checked"'; ?> /><label for="image_browser_title_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_description_enable" id="image_browser_description_enable_1" value="1" <?php if ($row->image_browser_description_enable) echo 'checked="checked"'; ?> /><label for="image_browser_description_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_description_enable" id="image_browser_description_enable_0" value="0" <?php if (!$row->image_browser_description_enable) echo 'checked="checked"'; ?> /><label for="image_browser_description_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to display titles of images in Image Browser view.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="image_browser_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
              <div class="wd-width-65">
			  <select name="image_browser_sort_by" id="image_browser_sort_by">
                <option value="order" <?php if ($row->image_browser_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
                <option value="alt" <?php if ($row->image_browser_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
                <option value="date" <?php if ($row->image_browser_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
                <option value="filename" <?php if ($row->image_browser_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
                <option value="size" <?php if ($row->image_browser_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
                <option value="random" <?php if ($row->image_browser_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
              </select>
			  </div>
              <div class="wd-width-30">
					<select name="image_browser_order_by" id="image_browser_order_by">
						<option value="asc" <?php if ($row->image_browser_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->image_browser_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
			  </div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_show_search_box" id="image_browser_show_search_box_1" value="1" <?php if ($row->image_browser_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_image_browser_search_box_width', 'image_browser_show_search_box_1'); bwg_enable_disable('', 'tr_image_browser_search_box_placeholder', 'image_browser_show_search_box_1')" /><label for="image_browser_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_show_search_box" id="image_browser_show_search_box_0" value="0" <?php if (!$row->image_browser_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_image_browser_search_box_width', 'image_browser_show_search_box_0'); bwg_enable_disable('none', 'tr_image_browser_search_box_placeholder', 'image_browser_show_search_box_0')" /><label for="image_browser_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_image_browser_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="image_browser_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="image_browser_placeholder" id="image_browser_placeholder" value="<?php echo $row->image_browser_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_image_browser_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="image_browser_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="image_browser_search_box_width" id="image_browser_search_box_width" value="<?php echo $row->image_browser_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_show_sort_images" id="image_browser_show_sort_images_1" value="1" <?php if ($row->image_browser_show_sort_images) echo 'checked="checked"'; ?> /><label for="image_browser_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_show_sort_images" id="image_browser_show_sort_images_0" value="0" <?php if (!$row->image_browser_show_sort_images) echo 'checked="checked"'; ?> /><label for="image_browser_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_show_tag_box" id="image_browser_show_tag_box_1" value="1" <?php if ($row->image_browser_show_tag_box) echo 'checked="checked"'; ?> /><label for="image_browser_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_show_tag_box" id="image_browser_show_tag_box_0" value="0" <?php if (!$row->image_browser_show_tag_box) echo 'checked="checked"'; ?> /><label for="image_browser_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_show_gallery_title" id="image_browser_thumb_name_yes" value="1" <?php if ($row->image_browser_show_gallery_title) echo 'checked="checked"'; ?> /><label for="image_browser_thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_show_gallery_title" id="image_browser_thumb_name_no" value="0"  <?php if (!$row->image_browser_show_gallery_title) echo 'checked="checked"'; ?> /><label for="image_browser_thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="image_browser_show_gallery_description" id="image_browser_show_gallery_description_1" value="1" <?php if ($row->image_browser_show_gallery_description) echo 'checked="checked"'; ?> /><label for="image_browser_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="image_browser_show_gallery_description" id="image_browser_show_gallery_description_0" value="0" <?php if (!$row->image_browser_show_gallery_description) echo 'checked="checked"'; ?> /><label for="image_browser_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="image_browser_gallery_download" id="image_browser_gallery_download_1" value="1" <?php if ($row->image_browser_gallery_download) echo 'checked="checked"'; ?> /><label for="image_browser_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="image_browser_gallery_download" id="image_browser_gallery_download_0" value="0" <?php if (!$row->image_browser_gallery_download) echo 'checked="checked"'; ?> /><label for="image_browser_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div id="blog_style_options" class="bwg-pro-views gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_width"><?php _e('Image width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="blog_style_width" id="blog_style_width" value="<?php echo $row->blog_style_width; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Specify the default width of images in Blog Style view.', BWG()->prefix); ?></p>
            </div>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_enable_page" id="blog_style_enable_page_0" value="0" <?php if ($row->blog_style_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_0'); bwg_pagination_description(this);" /><label for="blog_style_enable_page_0" class="wd-radio-label"><?php _e('None', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_enable_page" id="blog_style_enable_page_1" value="1" <?php if ($row->blog_style_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_1'); bwg_pagination_description(this);" /><label for="blog_style_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_enable_page" id="blog_style_enable_page_2" value="2" <?php if ($row->blog_style_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_2'); bwg_pagination_description(this);" /><label for="blog_style_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_enable_page" id="blog_style_enable_page_3" value="3" <?php if ($row->blog_style_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_blog_style_load_more_image_count', 'blog_style_enable_page_3'); bwg_pagination_description(this);" /><label for="blog_style_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="blog_style_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="blog_style_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="blog_style_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="blog_style_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_blog_style_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="blog_style_images_per_page" id="blog_style_images_per_page" value="<?php echo $row->blog_style_images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Select the number of images displayed per page in Blog Style view.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_blog_style_load_more_image_count">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_load_more_image_count"><?php _e('Images per load', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="blog_style_load_more_image_count" id="blog_style_load_more_image_count" value="<?php echo $row->blog_style_load_more_image_count; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per load on galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
			  <div class="wd-width-65">
				<select name="blog_style_sort_by" id="blog_style_sort_by">
					<option value="order" <?php if ($row->blog_style_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
					<option value="alt" <?php if ($row->blog_style_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
					<option value="date" <?php if ($row->blog_style_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
					<option value="filename" <?php if ($row->blog_style_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
					<option value="size" <?php if ($row->blog_style_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
					<option value="random" <?php if ($row->blog_style_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
				</select>
			  </div>
              <div class="wd-width-30">
					<select name="blog_style_order_by" id="blog_style_order_by">
						<option value="asc" <?php if ($row->blog_style_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->blog_style_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
			  </div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="blog_style_show_search_box" id="blog_style_show_search_box_1" value="1" <?php if ($row->blog_style_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_blog_style_search_box_width', 'blog_style_show_search_box_1'); bwg_enable_disable('', 'tr_blog_style_search_box_placeholder', 'blog_style_show_search_box_1')" /><label for="blog_style_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="blog_style_show_search_box" id="blog_style_show_search_box_0" value="0" <?php if (!$row->blog_style_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_blog_style_search_box_width', 'blog_style_show_search_box_0'); bwg_enable_disable('none', 'tr_blog_style_search_box_placeholder', 'blog_style_show_search_box_0')" /><label for="blog_style_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_blog_style_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="blog_style_placeholder" id="blog_style_placeholder" value="<?php echo $row->blog_style_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_blog_style_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="blog_style_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="blog_style_search_box_width" id="blog_style_search_box_width" value="<?php echo $row->blog_style_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="blog_style_show_sort_images" id="blog_style_show_sort_images_1" value="1" <?php if ($row->blog_style_show_sort_images) echo 'checked="checked"'; ?> /><label for="blog_style_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="blog_style_show_sort_images" id="blog_style_show_sort_images_0" value="0" <?php if (!$row->blog_style_show_sort_images) echo 'checked="checked"'; ?> /><label for="blog_style_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="blog_style_show_tag_box" id="blog_style_show_tag_box_1" value="1" <?php if ($row->blog_style_show_tag_box) echo 'checked="checked"'; ?> /><label for="blog_style_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="blog_style_show_tag_box" id="blog_style_show_tag_box_0" value="0" <?php if (!$row->blog_style_show_tag_box) echo 'checked="checked"'; ?> /><label for="blog_style_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="blog_style_show_gallery_title" id="blog_style_thumb_name_yes" value="1" <?php if ($row->blog_style_show_gallery_title) echo 'checked="checked"'; ?> /><label for="blog_style_thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="blog_style_show_gallery_title" id="blog_style_thumb_name_no" value="0"  <?php if (!$row->blog_style_show_gallery_title) echo 'checked="checked"'; ?> /><label for="blog_style_thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="blog_style_show_gallery_description" id="blog_style_show_gallery_description_1" value="1" <?php if ($row->blog_style_show_gallery_description) echo 'checked="checked"'; ?> /><label for="blog_style_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="blog_style_show_gallery_description" id="blog_style_show_gallery_description_0" value="0" <?php if (!$row->blog_style_show_gallery_description) echo 'checked="checked"'; ?> /><label for="blog_style_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_title_enable" id="blog_style_title_enable_1" value="1" <?php if ($row->blog_style_title_enable) echo 'checked="checked"'; ?> /><label for="blog_style_title_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_title_enable" id="blog_style_title_enable_0" value="0" <?php if (!$row->blog_style_title_enable) echo 'checked="checked"'; ?> /><label for="blog_style_title_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to display titles of images in Blog Style view.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_description_enable" id="blog_style_description_enable_1" value="1" <?php if ($row->blog_style_description_enable) echo 'checked="checked"'; ?> /><label for="blog_style_description_enable_1" class="wd-radio-label"><?php echo _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="blog_style_description_enable" id="blog_style_description_enable_0" value="0" <?php if (!$row->blog_style_description_enable) echo 'checked="checked"'; ?> /><label for="blog_style_description_enable_0" class="wd-radio-label"><?php echo _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this setting to show descriptions of images in Blog Style view.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="blog_style_gallery_download" id="blog_style_gallery_download_1" value="1" <?php if ($row->blog_style_gallery_download) echo 'checked="checked"'; ?> /><label for="blog_style_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="blog_style_gallery_download" id="blog_style_gallery_download_0" value="0" <?php if (!$row->blog_style_gallery_download) echo 'checked="checked"'; ?> /><label for="blog_style_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
			        <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div id="carousel_options" class="bwg-pro-views gallery_options wd-box-content wd-width-100 bwg-flex-wrap">
            <div class="wd-box-content wd-width-33">
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_image_column_number"><?php _e('Max. number of images', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_image_column_number" id="carousel_image_column_number" value="<?php echo $row->carousel_image_column_number; ?>" min="0" />
                  </div>
                  <p class="description"><?php _e('Set the maximum number of images that are shown with Carousel display.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_width"><?php _e('Image dimensions', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_width" id="carousel_width" value="<?php echo $row->carousel_width; ?>" min="0" /><span>x</span>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_height" id="carousel_height" value="<?php echo $row->carousel_height; ?>" min="0" /><span>px</span>
                  </div>
                  <p class="description"><?php _e('Specify the dimensions of carousel images in pixels.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_image_par"><?php _e('Carousel ratio', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_image_par" id="carousel_image_par" value="<?php echo $row->carousel_image_par; ?>" min="0" max="1" step="0.01" />
                  </div>
                  <p class="description"><?php _e('This option defines the proportion of dimensions between neighboring images in the carousel.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_r_width"><?php _e('Fixed width', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_r_width" id="carousel_r_width" value="<?php echo $row->carousel_r_width; ?>" min="0" /><span>px</span>
                  </div>
                  <p class="description"><?php _e('Specify the fixed width of Carousel gallery container.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-33">
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_sort_by"><?php _e('Order by', BWG()->prefix); ?></label>
				  <div class="wd-width-65">
					<select name="carousel_sort_by" id="carousel_sort_by">
						<option value="order" <?php if ($row->carousel_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
						<option value="alt" <?php if ($row->carousel_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
						<option value="date" <?php if ($row->carousel_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
						<option value="filename" <?php if ($row->carousel_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
						<option value="size" <?php if ($row->carousel_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
						<option value="random" <?php if ($row->carousel_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				  </div>
                  <div class="wd-width-30">
					<select name="carousel_order_by" id="carousel_order_by">
						<option value="asc" <?php if ($row->carousel_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->carousel_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				  </div>
				  <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable autoplay', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_enable_autoplay" id="carousel_enable_autoplay_yes" value="1" <?php if ($row->carousel_enable_autoplay) echo 'checked="checked"'; ?> /><label for="carousel_enable_autoplay_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_enable_autoplay" id="carousel_enable_autoplay_no" value="0" <?php if (!$row->carousel_enable_autoplay) echo 'checked="checked"'; ?> /><label for="carousel_enable_autoplay_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Activate this option to autoplay Carousel galleries.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label" for="carousel_interval"><?php _e('Time interval', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="carousel_interval" id="carousel_interval" value="<?php echo $row->carousel_interval; ?>" min="0" step="0.1" /><span>sec.</span>
                  </div>
                  <p class="description"><?php _e('Specify the time interval between rotations in Photo Gallery\'s Carousel view.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Container fit', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_fit_containerWidth" id="carousel_fit_containerWidth_yes" value="1" <?php if ($row->carousel_fit_containerWidth) echo 'checked="checked"'; ?> /><label for="carousel_fit_containerWidth_yes" class="wd-radio-label"><?php _e("Yes", BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_fit_containerWidth" id="carousel_fit_containerWidth_no" value="0" <?php if (!$row->carousel_fit_containerWidth) echo 'checked="checked"'; ?> /><label for="carousel_fit_containerWidth_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enabling this setting fits the images inside their container on Carousel galleries with fixed width.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Next/Previous buttons', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_prev_next_butt" id="carousel_prev_next_butt_yes" value="1" <?php if ($row->carousel_prev_next_butt) echo 'checked="checked"'; ?> /><label for="carousel_prev_next_butt_yes" class="wd-radio-label"><?php _e("Yes", BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_prev_next_butt" id="carousel_prev_next_butt_no" value="0" <?php if (!$row->carousel_prev_next_butt) echo 'checked="checked"'; ?> /><label for="carousel_prev_next_butt_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Enable this setting to display Next/Previous buttons on your galleries with Carousel view.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-33">
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="carousel_show_gallery_title" id="carousel_thumb_name_yes" value="1"  <?php if ($row->carousel_show_gallery_title) echo 'checked="checked"'; ?> /><label for="carousel_thumb_name_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="carousel_show_gallery_title" id="carousel_thumb_name_no" value="0"  <?php if (!$row->carousel_show_gallery_title) echo 'checked="checked"'; ?> /><label for="carousel_thumb_name_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Allow users to see the titles of your galleries by enabling this setting.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show gallery description', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input type="radio" name="carousel_show_gallery_description" id="carousel_show_gallery_description_1" value="1" <?php if ($row->carousel_show_gallery_description) echo 'checked="checked"'; ?> /><label for="carousel_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input type="radio" name="carousel_show_gallery_description" id="carousel_show_gallery_description_0" value="0" <?php if (!$row->carousel_show_gallery_description) echo 'checked="checked"'; ?> /><label for="carousel_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Display the descriptions of your galleries by activating this option.', BWG()->prefix); ?></p>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_enable_title" id="carousel_enable_title_yes" value="1" <?php if ($row->carousel_enable_title) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_carousel_title_position', 'carousel_enable_title_yes')" /><label for="carousel_enable_title_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_enable_title" id="carousel_enable_title_no" value="0" <?php if (!$row->carousel_enable_title) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_carousel_title_position', 'carousel_enable_title_no')" /><label for="carousel_enable_title_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Display image titles in Photo Gallery Carousel view by activating this option.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Play/Pause buttons', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_play_pause_butt" id="carousel_play_pause_butt_yes" value="1" <?php if ($row->carousel_play_pause_butt) echo 'checked="checked"'; ?> /><label for="carousel_play_pause_butt_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="carousel_play_pause_butt" id="carousel_play_pause_butt_no" value="0" <?php if (!$row->carousel_play_pause_butt) echo 'checked="checked"'; ?> /><label for="carousel_play_pause_butt_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Activate this to show Play/Pause buttons on your Carousel galleries.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                </div>
              </div>
              <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="carousel_gallery_download" id="carousel_gallery_download_1" value="1" <?php if ($row->carousel_gallery_download) echo 'checked="checked"'; ?> /><label for="carousel_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                    <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="carousel_gallery_download" id="carousel_gallery_download_0" value="0" <?php if (!$row->carousel_gallery_download) echo 'checked="checked"'; ?> /><label for="carousel_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
                  <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                  <?php
                  if ( !$zipArchiveClass) {
                    echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
    <?php
  }

  public static function gallery_group_options($row) {
    $zipArchiveClass = ( class_exists('ZipArchive') ) ? TRUE : FALSE;
    ?>
      <div id="album_compact_preview_options" class="album_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_column_number"><?php _e('Number of gallery group columns', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_column_number" id="album_column_number" value="<?php echo $row->album_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of columns in gallery groups. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_thumb_width"><?php _e('Gallery group thumbnail dimensions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_thumb_width" id="album_thumb_width" value="<?php echo $row->album_thumb_width; ?>" min="0" /><span>x</span>
                <input type="number" name="album_thumb_height" id="album_thumb_height" value="<?php echo $row->album_thumb_height; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Specify the dimensions of thumbnails in gallery groups.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_image_column_number"><?php _e('Number of image columns', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_image_column_number" id="album_image_column_number" value="<?php echo $row->album_image_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of image columns in galleries. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_thumbnail_dimensions">
            <div class="wd-group">
              <label class="wd-label" for="album_image_thumb_width"><?php _e('Thumbnail dimensions', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_image_thumb_width" id="album_image_thumb_width" value="<?php echo $row->album_image_thumb_width; ?>" min="0" /><span>x</span>
                <input type="number" name="album_image_thumb_height" id="album_image_thumb_height" value="<?php echo $row->album_image_thumb_height; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('The default dimensions of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_pagination">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_enable_page" id="album_enable_page_0" value="0" <?php if ($row->album_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_enable_page_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                <input type="radio" name="album_enable_page" id="album_enable_page_1" value="1" <?php if ($row->album_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input type="radio" name="album_enable_page" id="album_enable_page_2" value="2" <?php if ($row->album_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input type="radio" name="album_enable_page" id="album_enable_page_3" value="3" <?php if ($row->album_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="album_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_albums_per_page">
            <div class="wd-group">
              <label class="wd-label" for="albums_per_page"><?php _e('Gallery groups per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="albums_per_page" id="albums_per_page" value="<?php echo $row->albums_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of galleries/gallery groups to display per page. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="album_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_images_per_page" id="album_images_per_page" value="<?php echo $row->album_images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
			<div class="wd-group">
				<label class="wd-label" for="compact_album_sort_by"><?php _e('Order Gallery group by', BWG()->prefix); ?></label>
				<div class="wd-width-65">
					<select name="compact_album_sort_by" id="compact_album_sort_by">
						<option value="order" <?php if ($row->compact_album_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
						<option value="name" <?php if ($row->compact_album_sort_by == 'name') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
						<option value="random" <?php if ($row->compact_album_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				</div>
				<div class="wd-width-30">
					<select name="compact_album_order_by" id="compact_album_order_by">
						<option value="asc" <?php if ($row->compact_album_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->compact_album_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
				<p class="description"><?php _e("Select the parameter and order direction to sort the gallery group images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
			</div>
            <div class="wd-group">
				<label class="wd-label" for="album_sort_by"><?php _e('Order images by', BWG()->prefix); ?></label>
				<div class="wd-width-65">
					<select name="album_sort_by" id="album_sort_by">
						<option value="order" <?php if ($row->album_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
						<option value="alt" <?php if ($row->album_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
						<option value="date" <?php if ($row->album_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
						<option value="filename" <?php if ($row->album_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
						<option value="size" <?php if ($row->album_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
						<option value="random" <?php if ($row->album_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				</div>
				<div class="wd-width-30">
					<select name="album_order_by" id="album_order_by">
						<option value="asc" <?php if ($row->album_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->album_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_show_search_box" id="album_show_search_box_1" value="1" <?php if ($row->album_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_album_search_box_width', 'album_show_search_box_1'); bwg_enable_disable('', 'tr_album_search_box_placeholder', 'album_show_search_box_1')" /><label for="album_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_show_search_box" id="album_show_search_box_0" value="0" <?php if (!$row->album_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_album_search_box_width', 'album_show_search_box_0'); bwg_enable_disable('none', 'tr_album_search_box_placeholder', 'album_show_search_box_0')" /><label for="album_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="album_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="album_placeholder" id="album_placeholder" value="<?php echo $row->album_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="album_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_search_box_width" id="album_search_box_width" value="<?php echo $row->album_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_show_sort_images" id="album_show_sort_images_1" value="1" <?php if ($row->album_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_show_sort_images" id="album_show_sort_images_0" value="0" <?php if (!$row->album_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_show_tag_box" id="album_show_tag_box_1" value="1" <?php if ($row->album_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_show_tag_box" id="album_show_tag_box_0" value="0" <?php if (!$row->album_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery group or gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_album_name" id="show_album_name_enable_1" value="1" <?php if ($row->show_album_name) echo 'checked="checked"'; ?> /><label for="show_album_name_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_album_name" id="show_album_name_enable_0" value="0" <?php if (!$row->show_album_name) echo 'checked="checked"'; ?> /><label for="show_album_name_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the title of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery group or gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_show_gallery_description" id="album_show_gallery_description_1" value="1" <?php if ($row->album_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_show_gallery_description" id="album_show_gallery_description_0" value="0" <?php if (!$row->album_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the description of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_title_show_hover" id="album_title_show_hover_1" value="hover" <?php if ($row->album_title_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="album_title_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="album_title_show_hover" id="album_title_show_hover_0" value="show" <?php if ($row->album_title_show_hover == "show") echo 'checked="checked"'; ?> /><label for="album_title_show_hover_0" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                <input type="radio" name="album_title_show_hover" id="album_title_show_hover_2" value="none" <?php if ($row->album_title_show_hover == "none") echo 'checked="checked"'; ?> /><label for="album_title_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of galleries/gallery groups, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label" for="album_view_type"><?php _e('Gallery view type', BWG()->prefix); ?></label>
              <select name="album_view_type" id="album_view_type" <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?>>
                <option value="thumbnail" <?php if ($row->album_view_type == 'thumbnail') echo 'selected="selected"'; ?>><?php _e('Thumbnail', BWG()->prefix); ?></option>
                <option value="masonry" <?php if ($row->album_view_type == 'masonry') echo 'selected="selected"'; ?>><?php _e('Masonry', BWG()->prefix); ?></option>
                <option value="mosaic" <?php if ($row->album_view_type == 'mosaic') echo 'selected="selected"'; ?>><?php _e('Mosaic', BWG()->prefix); ?></option>
                <option value="slideshow" <?php if ($row->album_view_type == 'slideshow') echo 'selected="selected"'; ?>><?php _e('Slideshow', BWG()->prefix); ?></option>
                <option value="image_browser" <?php if ($row->album_view_type == 'image_browser') echo 'selected="selected"'; ?>><?php _e('Image Browser', BWG()->prefix); ?></option>
                <option value="blog_style" <?php if ($row->album_view_type == 'blog_style') echo 'selected="selected"'; ?>><?php _e('Blog Style', BWG()->prefix); ?></option>
                <option value="carousel" <?php if ($row->album_view_type == 'carousel') echo 'selected="selected"'; ?>><?php _e('Carousel', BWG()->prefix); ?></option>
              </select>
              <p class="description"><?php _e('Choose the display type for gallery groups, Thumbnails, Masonry, Mosaic, Slideshow, Image browser, Blog style or Carousel.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_mosaic">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Mosaic gallery type', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_mosaic" id="album_mosaic_0" value="vertical" <?php if ($row->album_mosaic == "vertical") echo 'checked="checked"'; ?> /><label for="album_mosaic_0" class="wd-radio-label"><?php _e('Vertical', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_mosaic" id="album_mosaic_1" value="horizontal" <?php if ($row->album_mosaic == "horizontal") echo 'checked="checked"'; ?> /><label for="album_mosaic_1" class="wd-radio-label"><?php _e('Horizontal', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Select the type of Mosaic galleries, Vertical or Horizontal.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_resizable_mosaic">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Resizable mosaic', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_resizable_mosaic" id="album_resizable_mosaic_1" value="1" <?php if ($row->album_resizable_mosaic == "1") echo 'checked="checked"'; ?> /><label for="album_resizable_mosaic_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_resizable_mosaic" id="album_resizable_mosaic_0" value="0" <?php if ($row->album_resizable_mosaic == "0") echo 'checked="checked"'; ?> /><label for="album_resizable_mosaic_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('If this setting is enabled, Photo Gallery resizes all thumbnail images on Mosaic galleries, without modifying their initial display.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_mosaic_total_width">
            <div class="wd-group">
              <label class="wd-label" for="album_mosaic_total_width"><?php _e('Width of mosaic galleries', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="album_mosaic_total_width" id="album_mosaic_total_width" value="<?php echo $row->album_mosaic_total_width; ?>" min="0" /><span>%</span>
              </div>
              <p class="description"><?php _e('The total width of mosaic galleries as a percentage of container\'s width.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_image_title_show_hover" id="album_image_title_show_hover_1" value="hover" <?php if ($row->album_image_title_show_hover == "hover") echo 'checked="checked"'; ?> />
                <label for="album_image_title_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="album_image_title_show_hover" id="album_image_title_show_hover_0" value="show" <?php if ($row->album_image_title_show_hover == "show") echo 'checked="checked"'; ?> />
                <label id="for_album_image_title_show_hover_0" for="album_image_title_show_hover_0" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                <input type="radio" name="album_image_title_show_hover" id="album_image_title_show_hover_2" value="none" <?php if ($row->album_image_title_show_hover == "none") echo 'checked="checked"'; ?> />
                <label for="album_image_title_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Play icon on video thumbnails', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_play_icon" id="album_play_icon_yes" value="1" <?php if ($row->album_play_icon) echo 'checked="checked"'; ?> /><label for="album_play_icon_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_play_icon" id="album_play_icon_no" value="0" <?php if (!$row->album_play_icon) echo 'checked="checked"'; ?> /><label for="album_play_icon_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this option to add a Play button on thumbnails of videos.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_gallery_download" id="album_gallery_download_1" value="1" <?php if ($row->album_gallery_download) echo 'checked="checked"'; ?> /><label for="album_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_gallery_download" id="album_gallery_download_0" value="0" <?php if (!$row->album_gallery_download) echo 'checked="checked"'; ?> /><label for="album_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
			        <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
          <?php
          if (function_exists('BWGEC')) {
            ?>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <label for="album_ecommerce_icon_show_hover_1" class="wd-radio-label"><input type="radio" name="album_ecommerce_icon_show_hover" id="album_ecommerce_icon_show_hover_1" value="hover" <?php if ($row->album_ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <label id="for_album_ecommerce_icon_show_hover_0" for="album_ecommerce_icon_show_hover_0" class="wd-radio-label"><input type="radio" name="album_ecommerce_icon_show_hover" id="album_ecommerce_icon_show_hover_0" value="show" <?php if ($row->album_ecommerce_icon_show_hover == "show") echo 'checked="checked"'; ?> /><?php _e('Always show', BWG()->prefix); ?></label>
                  <label for="album_ecommerce_icon_show_hover_2" class="wd-radio-label"><input type="radio" name="album_ecommerce_icon_show_hover" id="album_ecommerce_icon_show_hover_2" value="none" <?php if ($row->album_ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div id="album_masonry_preview_options" class="bwg-pro-views album_options wd-box-content wd-width-100 bwg-flex-wrap">
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_column_number"><?php _e('Number of gallery group columns', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_column_number" id="album_masonry_column_number" value="<?php echo $row->album_masonry_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of columns in gallery groups. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_thumb_width"><?php _e('Gallery group thumbnail width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_thumb_width" id="album_masonry_thumb_width" value="<?php echo $row->album_masonry_thumb_width; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('Specify the dimensions of thumbnails in gallery groups.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_image_column_number"><?php _e('Number of image columns', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_image_column_number" id="album_masonry_image_column_number" value="<?php echo $row->album_masonry_image_column_number; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Set the maximum number of image columns in galleries. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_image_thumb_width"><?php _e('Thumbnail width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_image_thumb_width" id="album_masonry_image_thumb_width" value="<?php echo $row->album_masonry_image_thumb_width; ?>" min="0" /><span>px</span>
              </div>
              <p class="description"><?php _e('The default dimensions of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_enable_page" id="album_masonry_enable_page_0" value="0" <?php if ($row->album_masonry_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_masonry_enable_page_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_enable_page" id="album_masonry_enable_page_1" value="1" <?php if ($row->album_masonry_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_masonry_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_enable_page" id="album_masonry_enable_page_2" value="2" <?php if ($row->album_masonry_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_masonry_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_enable_page" id="album_masonry_enable_page_3" value="3" <?php if ($row->album_masonry_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_masonry_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
              </div>
              <p class="description" id="album_masonry_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_masonry_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_masonry_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
              <p class="description" id="album_masonry_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_albums_masonry_per_page">
            <div class="wd-group">
              <label class="wd-label" for="albums_masonry_per_page"><?php _e('Gallery groups per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="albums_masonry_per_page" id="albums_masonry_per_page" value="<?php echo $row->albums_masonry_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of galleries/gallery groups to display per page. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_masonry_images_per_page">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_images_per_page" id="album_masonry_images_per_page" value="<?php echo $row->album_masonry_images_per_page; ?>" min="0" />
              </div>
              <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
			<div class="wd-group">
				<label class="wd-label" for="masonry_album_sort_by"><?php _e('Order Gallery group by', BWG()->prefix); ?></label>
				<div class="wd-width-65">
					<select name="masonry_album_sort_by" id="masonry_album_sort_by">
						<option value="order" <?php if ($row->masonry_album_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
						<option value="name" <?php if ($row->masonry_album_sort_by == 'name') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
						<option value="random" <?php if ($row->masonry_album_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				</div>
				<div class="wd-width-30">
					<select name="masonry_album_order_by" id="masonry_album_order_by">
						<option value="asc" <?php if ($row->masonry_album_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->masonry_album_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
				<p class="description"><?php _e("Select the parameter and order direction to sort the gallery group images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
			</div>
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_sort_by"><?php _e('Order images by', BWG()->prefix); ?></label>
				<div class="wd-width-65">
				  <select name="album_masonry_sort_by" id="album_masonry_sort_by">
					<option value="order" <?php if ($row->album_masonry_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
					<option value="alt" <?php if ($row->album_masonry_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
					<option value="date" <?php if ($row->album_masonry_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
					<option value="filename" <?php if ($row->album_masonry_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
					<option value="size" <?php if ($row->album_masonry_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
					<option value="random" <?php if ($row->album_masonry_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
				  </select>
				</div>
				<div class="wd-width-30">
					<select name="album_masonry_order_by" id="album_masonry_order_by">
						<option value="asc" <?php if ($row->album_masonry_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->album_masonry_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
              <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_show_search_box" id="album_masonry_show_search_box_1" value="1" <?php if ($row->album_masonry_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_album_masonry_search_box_width', 'album_masonry_show_search_box_1'); bwg_enable_disable('', 'tr_album_masonry_search_box_placeholder', 'album_masonry_show_search_box_1')" /><label for="album_masonry_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_show_search_box" id="album_masonry_show_search_box_0" value="0" <?php if (!$row->album_masonry_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_album_masonry_search_box_width', 'album_masonry_show_search_box_0'); bwg_enable_disable('none', 'tr_album_masonry_search_box_placeholder', 'album_masonry_show_search_box_0')" /><label for="album_masonry_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_masonry_search_box_placeholder">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="text" name="album_masonry_placeholder" id="album_masonry_placeholder" value="<?php echo $row->album_masonry_placeholder; ?>"  />
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100" id="tr_album_masonry_search_box_width">
            <div class="wd-group">
              <label class="wd-label" for="album_masonry_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="number" name="album_masonry_search_box_width" id="album_masonry_search_box_width" value="<?php echo $row->album_masonry_search_box_width; ?>" min="0" /><span>px</span>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_show_sort_images" id="album_masonry_show_sort_images_1" value="1" <?php if ($row->album_masonry_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_masonry_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_show_sort_images" id="album_masonry_show_sort_images_0" value="0" <?php if (!$row->album_masonry_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_masonry_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_show_tag_box" id="album_masonry_show_tag_box_1" value="1" <?php if ($row->album_masonry_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_masonry_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_show_tag_box" id="album_masonry_show_tag_box_0" value="0" <?php if (!$row->album_masonry_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_masonry_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
            </div>
          </div>
        </div>
        <div class="wd-box-content wd-width-33">
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery group or gallery title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="show_album_masonry_name" id="show_album_masonry_name_enable_1" value="1" <?php if ($row->show_album_masonry_name) echo 'checked="checked"'; ?> /><label for="show_album_masonry_name_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="show_album_masonry_name" id="show_album_masonry_name_enable_0" value="0" <?php if (!$row->show_album_masonry_name) echo 'checked="checked"'; ?> /><label for="show_album_masonry_name_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the title of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show gallery group or gallery description', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_show_gallery_description" id="album_masonry_show_gallery_description_1" value="1" <?php if ($row->album_masonry_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_masonry_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_show_gallery_description" id="album_masonry_show_gallery_description_0" value="0" <?php if (!$row->album_masonry_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_masonry_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Display the description of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="album_masonry_image_title" id="album_masonry_image_title_0" value="hover" <?php if ($row->album_masonry_image_title == "hover") echo 'checked="checked"'; ?> /><label for="album_masonry_image_title_0" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_image_title" id="album_masonry_image_title_1" value="show" <?php if ($row->album_masonry_image_title == "show") echo 'checked="checked"'; ?> /><label for="album_masonry_image_title_1" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                <input type="radio" name="album_masonry_image_title" id="album_masonry_image_title_2" value="none" <?php if ($row->album_masonry_image_title == "none") echo 'checked="checked"'; ?> /><label for="album_masonry_image_title_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
            </div>
          </div>
          <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_masonry_gallery_download" id="album_masonry_gallery_download_1" value="1" <?php if ($row->album_masonry_gallery_download) echo 'checked="checked"'; ?> /><label for="album_masonry_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_masonry_gallery_download" id="album_masonry_gallery_download_0" value="0" <?php if (!$row->album_masonry_gallery_download) echo 'checked="checked"'; ?> /><label for="album_masonry_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
              <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
			        <?php
              if ( !$zipArchiveClass) {
                echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
              }
              ?>
            </div>
          </div>
          <?php
          if (function_exists('BWGEC')) {
            ?>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_masonry_ecommerce_icon_show_hover" id="album_masonry_ecommerce_icon_show_hover_1" value="hover" <?php if ($row->album_masonry_ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><label for="album_masonry_ecommerce_icon_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <input type="radio" name="album_masonry_ecommerce_icon_show_hover" id="album_masonry_ecommerce_icon_show_hover_2" value="none" <?php if ($row->album_masonry_ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><label for="album_masonry_ecommerce_icon_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div id="album_extended_preview_options" class="album_options wd-box-content wd-width-100 bwg-flex-wrap">
          <div class="wd-box-content wd-width-33">
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label" for="extended_album_height"><?php _e('Extended gallery group height', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="extended_album_height" id="extended_album_height" value="<?php echo $row->extended_album_height; ?>" min="0" /><span>px</span>
                </div>
                <p class="description"><?php _e('Set the height of blocks in Extended gallery groups.', BWG()->prefix); ?></p>
              </div>
            </div>
			      <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label" for="extended_album_column_number"><?php _e('Number of columns', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="extended_album_column_number" id="extended_album_column_number_1" value="1" <?php if ($row->extended_album_column_number == 1) echo 'checked="checked"'; ?> /><label for="extended_album_column_number_1" class="wd-radio-label"><?php _e('1 column', BWG()->prefix); ?></label>
				          <input type="radio" name="extended_album_column_number" id="extended_album_column_number_2" value="2" <?php if ($row->extended_album_column_number == 2) echo 'checked="checked"'; ?> /><label for="extended_album_column_number_2" class="wd-radio-label"><?php _e('2 column', BWG()->prefix); ?></label>
                  <input type="radio" name="extended_album_column_number" id="extended_album_column_number_3" value="3" <?php if ($row->extended_album_column_number == 3) echo 'checked="checked"'; ?> /><label for="extended_album_column_number_3" class="wd-radio-label"><?php _e('3 column', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Set the maximum number of columns.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_thumb_width"><?php _e('Gallery group thumbnail dimensions', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="album_extended_thumb_width" id="album_extended_thumb_width" value="<?php echo $row->album_extended_thumb_width; ?>" min="0" /><span>x</span>
                  <input type="number" name="album_extended_thumb_height" id="album_extended_thumb_height" value="<?php echo $row->album_extended_thumb_height; ?>" min="0" /><span>px</span>
                </div>
                <p class="description"><?php _e('Specify the dimensions of thumbnails in gallery groups.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_image_column_number"><?php _e('Number of image columns', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="album_extended_image_column_number" id="album_extended_image_column_number" value="<?php echo $row->album_extended_image_column_number; ?>" min="0" />
                </div>
                <p class="description"><?php _e('Set the maximum number of image columns in galleries. Note, that the parent container needs to be large enough to display all columns.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100" id="tr_album_extended_thumbnail_dimensions">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_image_thumb_width"><?php _e('Thumbnail dimensions', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="album_extended_image_thumb_width" id="album_extended_image_thumb_width" value="<?php echo $row->album_extended_image_thumb_width; ?>" min="0" /><span>x</span>
                  <input type="number" name="album_extended_image_thumb_height" id="album_extended_image_thumb_height" value="<?php echo $row->album_extended_image_thumb_height; ?>" min="0" /><span>px</span>
                </div>
                <p class="description"><?php _e('The default dimensions of thumbnails which will display on published galleries.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Pagination', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_enable_page" id="album_extended_enable_page_0" value="0" <?php if ($row->album_extended_enable_page == '0') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_extended_enable_page_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_enable_page" id="album_extended_enable_page_1" value="1" <?php if ($row->album_extended_enable_page == '1') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_extended_enable_page_1" class="wd-radio-label"><?php _e('Simple', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_enable_page" id="album_extended_enable_page_2" value="2" <?php if ($row->album_extended_enable_page == '2') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_extended_enable_page_2" class="wd-radio-label"><?php _e('Load More', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_enable_page" id="album_extended_enable_page_3" value="3" <?php if ($row->album_extended_enable_page == '3') echo 'checked="checked"'; ?> onClick="bwg_pagination_description(this);" /><label for="album_extended_enable_page_3" class="wd-radio-label"><?php _e('Scroll Load', BWG()->prefix); ?></label>
                </div>
                <p class="description" id="album_extended_enable_page_0_description"><?php _e('This option removes all types of pagination from your galleries.', BWG()->prefix); ?></p>
                <p class="description" id="album_extended_enable_page_1_description"><?php _e('Activating this option will add page numbers and next/previous buttons to your galleries.', BWG()->prefix); ?></p>
                <p class="description" id="album_extended_enable_page_2_description"><?php _e('Adding a Load More button, you can let users display a new set of images from your galleries.', BWG()->prefix); ?></p>
                <p class="description" id="album_extended_enable_page_3_description"><?php _e('With this option, users can load new images of your galleries simply by scrolling down.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100" id="tr_albums_extended_per_page">
              <div class="wd-group">
                <label class="wd-label" for="albums_extended_per_page"><?php _e('Gallery groups per page', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="albums_extended_per_page" id="albums_extended_per_page" value="<?php echo $row->albums_extended_per_page; ?>" min="0" />
                </div>
                <p class="description"><?php _e('Specify the number of galleries/gallery groups to display per page. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100" id="tr_album_extended_images_per_page">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_images_per_page"><?php _e('Images per page', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="album_extended_images_per_page" id="album_extended_images_per_page" value="<?php echo $row->album_extended_images_per_page; ?>" min="0" />
                </div>
                <p class="description"><?php _e('Specify the number of images to display per page on galleries. Setting this option to 0 shows all items.', BWG()->prefix); ?></p>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-33">
            <div class="wd-box-content wd-width-100">
				<div class="wd-group">
					<label class="wd-label" for="extended_album_sort_by"><?php _e('Order Gallery group by', BWG()->prefix); ?></label>
					<div class="wd-width-65">
						<select name="extended_album_sort_by" id="extended_album_sort_by">
							<option value="order" <?php if ($row->extended_album_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
							<option value="name" <?php if ($row->extended_album_sort_by == 'name') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
							<option value="random" <?php if ($row->extended_album_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
						</select>
					</div>
					<div class="wd-width-30">
						<select name="extended_album_order_by" id="extended_album_order_by">
							<option value="asc" <?php if ($row->extended_album_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
							<option value="desc" <?php if ($row->extended_album_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
						</select>
					</div>
					<p class="description"><?php _e("Select the parameter and order direction to sort the gallery group images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
				</div>
              <div class="wd-group">
				<div class="wd-width-65">
					<label class="wd-label" for="album_extended_sort_by"><?php _e('Order images by', BWG()->prefix); ?></label>
					<select name="album_extended_sort_by" id="album_extended_sort_by">
					  <option value="order" <?php if ($row->album_extended_sort_by == 'order') echo 'selected="selected"'; ?>><?php _e('Default', BWG()->prefix); ?></option>
					  <option value="alt" <?php if ($row->album_extended_sort_by == 'alt') echo 'selected="selected"'; ?>><?php _e('Title', BWG()->prefix); ?></option>
					  <option value="date" <?php if ($row->album_extended_sort_by == 'date') echo 'selected="selected"'; ?>><?php _e('Date', BWG()->prefix); ?></option>
					  <option value="filename" <?php if ($row->album_extended_sort_by == 'filename') echo 'selected="selected"'; ?>><?php _e('Filename', BWG()->prefix); ?></option>
					  <option value="size" <?php if ($row->album_extended_sort_by == 'size') echo 'selected="selected"'; ?>><?php _e('Size', BWG()->prefix); ?></option>
					  <option value="random" <?php if ($row->album_extended_sort_by == 'random') echo 'selected="selected"'; ?>><?php _e('Random', BWG()->prefix); ?></option>
					</select>
				</div>
				<div class="wd-width-30">
					<select name="album_extended_order_by" id="album_extended_order_by">
						<option value="asc" <?php if ($row->album_extended_order_by == 'asc') echo 'selected="selected"'; ?>><?php _e('Ascending', BWG()->prefix); ?></option>
						<option value="desc" <?php if ($row->album_extended_order_by == 'desc') echo 'selected="selected"'; ?>><?php _e('Descending', BWG()->prefix); ?></option>
					</select>
				</div>
                <p class="description"><?php _e("Select the parameter and order direction to sort the gallery images with. E.g. Title and Ascending.", BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show search box', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_show_search_box" id="album_extended_show_search_box_1" value="1" <?php if ($row->album_extended_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_album_extended_search_box_width', 'album_extended_show_search_box_1'); bwg_enable_disable('', 'tr_album_extended_search_box_placeholder', 'album_extended_show_search_box_1')" /><label for="album_extended_show_search_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_show_search_box" id="album_extended_show_search_box_0" value="0" <?php if (!$row->album_extended_show_search_box) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_album_extended_search_box_width', 'album_extended_show_search_box_0'); bwg_enable_disable('none', 'tr_album_extended_search_box_placeholder', 'album_extended_show_search_box_0')" /><label for="album_extended_show_search_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Enable this option to display a search box with your gallery or gallery group.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100" id="tr_album_extended_search_box_placeholder">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_placeholder"><?php _e('Add placeholder to search', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="text" name="album_extended_placeholder" id="album_extended_placeholder" value="<?php echo $row->album_extended_placeholder; ?>"  />
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-100" id="tr_album_extended_search_box_width">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_search_box_width"><?php _e('Search box maximum width', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="number" name="album_extended_search_box_width" id="album_extended_search_box_width" value="<?php echo $row->album_extended_search_box_width; ?>" min="0" /><span>px</span>
                </div>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show "Order by" dropdown list', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_show_sort_images" id="album_extended_show_sort_images_1" value="1" <?php if ($row->album_extended_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_extended_show_sort_images_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_show_sort_images" id="album_extended_show_sort_images_0" value="0" <?php if (!$row->album_extended_show_sort_images) echo 'checked="checked"'; ?> /><label for="album_extended_show_sort_images_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Activate this dropdown box to let users browse your gallery images with different ordering options.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show tag box', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_show_tag_box" id="album_extended_show_tag_box_1" value="1" <?php if ($row->album_extended_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_extended_show_tag_box_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_show_tag_box" id="album_extended_show_tag_box_0" value="0" <?php if (!$row->album_extended_show_tag_box) echo 'checked="checked"'; ?> /><label for="album_extended_show_tag_box_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Enable Tag Box to allow users to filter the gallery images by their tags.', BWG()->prefix); ?></p>
              </div>
            </div>
          </div>
          <div class="wd-box-content wd-width-33">
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show gallery group or gallery title', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="show_album_extended_name" id="show_album_extended_name_enable_1" value="1" <?php if ($row->show_album_extended_name) echo 'checked="checked"'; ?> /><label for="show_album_extended_name_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="show_album_extended_name" id="show_album_extended_name_enable_0" value="0" <?php if (!$row->show_album_extended_name) echo 'checked="checked"'; ?> /><label for="show_album_extended_name_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Display the title of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show gallery group or gallery description', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_show_gallery_description" id="album_extended_show_gallery_description_1" value="1" <?php if ($row->album_extended_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_extended_show_gallery_description_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_show_gallery_description" id="album_extended_show_gallery_description_0" value="0" <?php if (!$row->album_extended_show_gallery_description) echo 'checked="checked"'; ?> /><label for="album_extended_show_gallery_description_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Display the description of displayed gallery or gallery group by enabling this setting.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show extended gallery group description', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="extended_album_description_enable" id="extended_album_description_enable_1" value="1" <?php if ($row->extended_album_description_enable) echo 'checked="checked"'; ?> /><label for="extended_album_description_enable_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="extended_album_description_enable" id="extended_album_description_enable_0" value="0" <?php if (!$row->extended_album_description_enable) echo 'checked="checked"'; ?> /><label for="extended_album_description_enable_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Enable this option to show descriptions of galleries/gallery groups in Extended view.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_view_type"><?php _e('Gallery view type', BWG()->prefix); ?></label>
                <select name="album_extended_view_type" id="album_extended_view_type" <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?>>
                  <option value="thumbnail" <?php if ($row->album_extended_view_type == 'thumbnail') echo 'selected="selected"'; ?>><?php _e('Thumbnail', BWG()->prefix); ?></option>
                  <option value="masonry" <?php if ($row->album_extended_view_type == 'masonry') echo 'selected="selected"'; ?>><?php _e('Masonry', BWG()->prefix); ?></option>
                  <option value="mosaic" <?php if ($row->album_extended_view_type == 'mosaic') echo 'selected="selected"'; ?>><?php _e('Mosaic', BWG()->prefix); ?></option>
                  <option value="slideshow" <?php if ($row->album_extended_view_type == 'slideshow') echo 'selected="selected"'; ?>><?php _e('Slideshow', BWG()->prefix); ?></option>
                  <option value="image_browser" <?php if ($row->album_extended_view_type == 'image_browser') echo 'selected="selected"'; ?>><?php _e('Image Browser', BWG()->prefix); ?></option>
                  <option value="blog_style" <?php if ($row->album_extended_view_type == 'blog_style') echo 'selected="selected"'; ?>><?php _e('Blog Style', BWG()->prefix); ?></option>
                  <option value="carousel" <?php if ($row->album_extended_view_type == 'carousel') echo 'selected="selected"'; ?>><?php _e('Carousel', BWG()->prefix); ?></option>
                </select>
                <p class="description"><?php _e('Choose the display type for gallery groups, Thumbnails, Masonry, Mosaic, Slideshow, Image browser, Blog style or Carousel.', BWG()->prefix); ?></p>
                <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              </div>
            </div>
            <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_extended_mosaic">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Mosaic gallery type', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_extended_mosaic" id="album_extended_mosaic_0" value="vertical" <?php if ($row->album_extended_mosaic == "vertical") echo 'checked="checked"'; ?> /><label for="album_extended_mosaic_0" class="wd-radio-label"><?php _e('Vertical', BWG()->prefix); ?></label>
                  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_extended_mosaic" id="album_extended_mosaic_1" value="horizontal" <?php if ($row->album_extended_mosaic == "horizontal") echo 'checked="checked"'; ?> /><label for="album_extended_mosaic_1" class="wd-radio-label"><?php _e('Horizontal', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Select the type of Mosaic galleries, Vertical or Horizontal.', BWG()->prefix); ?></p>
                <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              </div>
            </div>
            <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_extended_resizable_mosaic">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Resizable mosaic', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_extended_resizable_mosaic" id="album_extended_resizable_mosaic_1" value="1" <?php if ($row->album_extended_resizable_mosaic == "1") echo 'checked="checked"'; ?> /><label for="album_extended_resizable_mosaic_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="album_extended_resizable_mosaic" id="album_extended_resizable_mosaic_0" value="0" <?php if ($row->album_extended_resizable_mosaic == "0") echo 'checked="checked"'; ?> /><label for="album_extended_resizable_mosaic_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('If this setting is enabled, Photo Gallery resizes all thumbnail images on Mosaic galleries, without modifying their initial display.', BWG()->prefix); ?></p>
                <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              </div>
            </div>
            <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_album_extended_mosaic_total_width">
              <div class="wd-group">
                <label class="wd-label" for="album_extended_mosaic_total_width"><?php _e('Width of mosaic galleries', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="album_extended_mosaic_total_width" id="album_extended_mosaic_total_width" value="<?php echo $row->album_extended_mosaic_total_width; ?>" min="0" /><span>%</span>
                </div>
                <p class="description"><?php _e('The total width of mosaic galleries as a percentage of container\'s width.', BWG()->prefix); ?></p>
                <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show image title', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_image_title_show_hover" id="album_extended_image_title_show_hover_1" value="hover" <?php if ($row->album_extended_image_title_show_hover == "hover") echo 'checked="checked"'; ?> />
                  <label for="album_extended_image_title_show_hover_1" class="wd-radio-label"><?php _e('Show on hover', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_image_title_show_hover" id="album_extended_image_title_show_hover_0" value="show" <?php if ($row->album_extended_image_title_show_hover == "show") echo 'checked="checked"'; ?> />
                  <label id="for_album_extended_image_title_show_hover_0" for="album_extended_image_title_show_hover_0" class="wd-radio-label"><?php _e('Always show', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_image_title_show_hover" id="album_extended_image_title_show_hover_2" value="none" <?php if ($row->album_extended_image_title_show_hover == "none") echo 'checked="checked"'; ?> />
                  <label for="album_extended_image_title_show_hover_2" class="wd-radio-label"><?php _e("Don't show", BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Choose to show/hide titles of images, or display them on hover.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Show Play icon on video thumbnails', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input type="radio" name="album_extended_play_icon" id="album_extended_play_icon_yes" value="1" <?php if ($row->album_extended_play_icon) echo 'checked="checked"'; ?> /><label for="album_extended_play_icon_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" name="album_extended_play_icon" id="album_extended_play_icon_no" value="0" <?php if (!$row->album_extended_play_icon) echo 'checked="checked"'; ?> /><label for="album_extended_play_icon_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Activate this option to add a Play button on thumbnails of videos.', BWG()->prefix); ?></p>
              </div>
            </div>
            <div class="wd-box-content wd-width-100 <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Enable bulk download button', BWG()->prefix); ?></label>
                <div class="bwg-flex">
                  <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_extended_gallery_download" id="album_extended_gallery_download_1" value="1" <?php if ($row->album_extended_gallery_download) echo 'checked="checked"'; ?> /><label for="album_extended_gallery_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input <?php echo ( !$zipArchiveClass ) ? 'disabled="disabled"' : ( ( BWG()->is_pro ) ? '' : 'disabled="disabled"' ); ?> type="radio" name="album_extended_gallery_download" id="album_extended_gallery_download_0" value="0" <?php if (!$row->album_extended_gallery_download) echo 'checked="checked"'; ?> /><label for="album_extended_gallery_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
                </div>
                <p class="description"><?php _e('Activate this setting to let users download all images of your gallery with a click.', BWG()->prefix); ?></p>
                <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
                <?php
                  if ( !$zipArchiveClass) {
                  echo WDWLibrary::message_id(0, __('Photo Gallery Export will not work correctly, as ZipArchive PHP extension is disabled on your website. Please contact your hosting provider and ask them to enable it.', 'pgi'),'error');
                  }
                ?>
              </div>
            </div>
            <?php
            if (function_exists('BWGEC')) {
              ?>
              <div class="wd-box-content wd-width-100">
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Show ecommerce icon', BWG()->prefix); ?></label>
                  <div class="bwg-flex">
                    <label for="album_extended_ecommerce_icon_show_hover_1" class="wd-radio-label"><input type="radio" name="album_extended_ecommerce_icon_show_hover" id="album_extended_ecommerce_icon_show_hover_1" value="hover" <?php if ($row->album_extended_ecommerce_icon_show_hover == "hover") echo 'checked="checked"'; ?> /><?php _e('Show on hover', BWG()->prefix); ?></label>
                    <label id="for_album_extended_ecommerce_icon_show_hover_0" for="album_extended_ecommerce_icon_show_hover_0" class="wd-radio-label"><input type="radio" name="album_extended_ecommerce_icon_show_hover" id="album_extended_ecommerce_icon_show_hover_0" value="show" <?php if ($row->album_extended_ecommerce_icon_show_hover == "show") echo 'checked="checked"'; ?> /><?php _e('Always show', BWG()->prefix); ?></label>
                    <label for="album_extended_ecommerce_icon_show_hover_2" class="wd-radio-label"><input type="radio" name="album_extended_ecommerce_icon_show_hover" id="album_extended_ecommerce_icon_show_hover_2" value="none" <?php if ($row->album_extended_ecommerce_icon_show_hover == "none") echo 'checked="checked"'; ?> /><?php _e("Don't show", BWG()->prefix); ?></label>
                  </div>
                  <p class="description"><?php _e('Choose to show/hide ecommerce icon, or display them on hover.', BWG()->prefix); ?></p>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
        </div>
    <?php
  }

  public static function lightbox_options($row) {
    $effects = self::get_effects();
    ?>
    <div class="wd-box-content wd-width-100 bwg-flex-wrap">
      <div class="wd-box-content wd-width-33">
        <?php  if( !isset($row->lightbox_shortcode) ) { ?>
        <div class="wd-box-content wd-width-100">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Image click action', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="thumb_click_action" id="thumb_click_action_1" value="open_lightbox" <?php if ($row->thumb_click_action == 'open_lightbox') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_1" class="wd-radio-label"><?php _e('Open lightbox', BWG()->prefix); ?></label>
              <input type="radio" name="thumb_click_action" id="thumb_click_action_2" value="redirect_to_url" <?php if ($row->thumb_click_action == 'redirect_to_url') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_2" class="wd-radio-label"><?php _e('Redirect to url', BWG()->prefix); ?></label>
              <input type="radio" name="thumb_click_action" id="thumb_click_action_3" value="do_nothing" <?php if ($row->thumb_click_action == 'do_nothing') echo 'checked="checked"'; ?> onClick="bwg_thumb_click_action();" /><label for="thumb_click_action_3" class="wd-radio-label"><?php _e('Do Nothing', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Select the action which runs after clicking on gallery thumbnails.', BWG()->prefix); ?></p>
          </div>
          <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-redirect" id="tr_thumb_link_target">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Open in a new window', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="thumb_link_target" id="thumb_link_target_yes" value="1" <?php if ($row->thumb_link_target) echo 'checked="checked"'; ?> /><label for="thumb_link_target_yes" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="thumb_link_target" id="thumb_link_target_no" value="0" <?php if (!$row->thumb_link_target) echo 'checked="checked"'; ?> /><label for="thumb_link_target_no" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
            </div>
          </div>
        </div>
        <?php  } ?>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_full_width">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Full-width lightbox', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_fullscreen" id="popup_fullscreen_1" value="1" <?php if ($row->popup_fullscreen) echo 'checked="checked"'; ?> onchange="bwg_enable_disable('none', 'tr_popup_dimensions', 'popup_fullscreen_1')" /><label for="popup_fullscreen_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_fullscreen" id="popup_fullscreen_0" value="0" <?php if (!$row->popup_fullscreen) echo 'checked="checked"'; ?> onchange="bwg_enable_disable('', 'tr_popup_dimensions', 'popup_fullscreen_0')" /><label for="popup_fullscreen_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Image lightbox will appear full-width if this setting is activated.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_dimensions">
          <div class="wd-group">
            <label class="wd-label" for="popup_width"><?php _e('Lightbox dimensions', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="number" name="popup_width" id="popup_width" value="<?php echo $row->popup_width; ?>" min="0" /><span>x</span>
              <input type="number" name="popup_height" id="popup_height" value="<?php echo $row->popup_height; ?>" min="0" /><span>px</span>
            </div>
            <p class="description"><?php _e('Set the dimensions of image lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox">
          <div class="wd-group">
            <label class="wd-label" for="popup_type"><?php _e('Lightbox effect', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <select name="popup_type" id="popup_type">
                <?php
                foreach ($effects as $key => $effect) {
                  ?>
                  <option value="<?php echo $key; ?>"
                    <?php echo (!BWG()->is_pro && $key != 'none' && $key != 'fade') ? 'disabled="disabled" title="' . __('This effect is disabled in free version.', BWG()->prefix) . '"' : ''; ?>
                    <?php if ($row->popup_type == $key) echo 'selected="selected"'; ?>><?php echo __($effect, BWG()->prefix); ?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            <p class="description"><?php _e('Select the animation effect for image lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox">
          <div class="wd-group">
            <label class="wd-label" for="popup_effect_duration"><?php _e('Effect duration', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="number" name="popup_effect_duration" id="popup_effect_duration" value="<?php echo $row->popup_effect_duration; ?>" min="0" step="0.1" /><span>sec.</span>
            </div>
            <p class="description"><?php _e('Set the duration of lightbox animation effect.', BWG()->prefix) ?></p>
            <p class="description"><?php _e('Note, that the value of Effect Duration can not be greater than 1/4 of Time Interval.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_autoplay">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Lightbox autoplay', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_autoplay" id="popup_autoplay_1" value="1" <?php if ($row->popup_autoplay) echo 'checked="checked"'; ?> /><label for="popup_autoplay_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_autoplay" id="popup_autoplay_0" value="0" <?php if (!$row->popup_autoplay) echo 'checked="checked"'; ?> /><label for="popup_autoplay_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate this option to autoplay images in gallery lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox">
          <div class="wd-group">
            <label class="wd-label" for="popup_interval"><?php _e('Time interval', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="number" name="popup_interval" id="popup_interval" value="<?php echo $row->popup_interval; ?>" min="0" step="0.1" /><span>sec.</span>
            </div>
            <p class="description"><?php _e('Specify the time interval of autoplay in Photo Gallery lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable filmstrip', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_filmstrip" id="popup_enable_filmstrip_1" value="1" <?php if ($row->popup_enable_filmstrip ) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_popup_filmstrip_height', 'popup_enable_filmstrip_1')" /><label for="popup_enable_filmstrip_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_filmstrip" id="popup_enable_filmstrip_0" value="0" <?php if (!$row->popup_enable_filmstrip ) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_popup_filmstrip_height', 'popup_enable_filmstrip_0')" /><label for="popup_enable_filmstrip_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Add a filmstrip with image thumbnails to the lightbox of your galleries.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_filmstrip_height ">
          <div class="wd-group">
            <label class="wd-label" for="popup_filmstrip_height"><?php _e('Filmstrip size', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="number" name="popup_filmstrip_height" id="popup_filmstrip_height" value="<?php echo $row->popup_filmstrip_height; ?>" min="0" /><span>px</span>
            </div>
            <p class="description"><?php _e('Set the size of your filmstrip. If the filmstrip is horizontal, this indicates its height, whereas for vertical filmstrips it sets the width.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
      </div>
      <div class="wd-box-content wd-width-33">
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable control buttons', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_enable_ctrl_btn" id="popup_enable_ctrl_btn_1" value="1" <?php if ($row->popup_enable_ctrl_btn) echo 'checked="checked"'; ?>
                     onClick="bwg_enable_disable('', 'tr_popup_fullscreen', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_info', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_comment', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_facebook', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_twitter', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_google', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_pinterest', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_tumblr', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_comment_moderation', 'comment_moderation_1');
                            bwg_enable_disable('', 'tr_popup_email', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_captcha', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_download', 'popup_enable_ctrl_btn_1');
                            bwg_enable_disable('', 'tr_popup_fullsize_image', 'popup_enable_ctrl_btn_1');" /><label for="popup_enable_ctrl_btn_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_enable_ctrl_btn" id="popup_enable_ctrl_btn_0" value="0" <?php if (!$row->popup_enable_ctrl_btn) echo 'checked="checked"'; ?>
                     onClick="bwg_enable_disable('none', 'tr_popup_fullscreen', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_info', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_comment', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_facebook', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_twitter', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_google', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_pinterest', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_tumblr', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_comment_moderation', 'comment_moderation_0');
                            bwg_enable_disable('none', 'tr_popup_email', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_captcha', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_download', 'popup_enable_ctrl_btn_0');
                            bwg_enable_disable('none', 'tr_popup_fullsize_image', 'popup_enable_ctrl_btn_0');" /><label for="popup_enable_ctrl_btn_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enable this option to show control buttons on Photo Gallery lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_fullscreen">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable fullscreen button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_enable_fullscreen" id="popup_enable_fullscreen_1" value="1" <?php if ($row->popup_enable_fullscreen) echo 'checked="checked"'; ?> /><label for="popup_enable_fullscreen_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_enable_fullscreen" id="popup_enable_fullscreen_0" value="0" <?php if (!$row->popup_enable_fullscreen) echo 'checked="checked"'; ?> /><label for="popup_enable_fullscreen_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate this setting to add Fullscreen button to lightbox control buttons.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_comment">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable comments', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_comment" id="popup_enable_comment_1" value="1" <?php if ($row->popup_enable_comment) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_comment_moderation', 'popup_enable_comment_1');
                                                                                                                                                                                            bwg_enable_disable('', 'tr_popup_email', 'popup_enable_comment_1');
                                                                                                                                                                                            bwg_enable_disable('', 'tr_popup_captcha', 'popup_enable_comment_1');" /><label for="popup_enable_comment_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_comment" id="popup_enable_comment_0" value="0" <?php if (!$row->popup_enable_comment) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_comment_moderation', 'popup_enable_comment_0');
                                                                                                                                                                                             bwg_enable_disable('none', 'tr_popup_email', 'popup_enable_comment_0');
                                                                                                                                                                                             bwg_enable_disable('none', 'tr_popup_captcha', 'popup_enable_comment_0');" /><label for="popup_enable_comment_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Let users to leave comments on images by enabling comments section of lightbox.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_email">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Email for comments', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_email" id="popup_enable_email_1" value="1" <?php if ($row->popup_enable_email) echo 'checked="checked"'; ?> /><label for="popup_enable_email_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_email" id="popup_enable_email_0" value="0" <?php if (!$row->popup_enable_email) echo 'checked="checked"'; ?> /><label for="popup_enable_email_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate this option to display email address field in comments section.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo (BWG()->is_pro && !$row->gdpr_compliance) ? '' : ' bwg-disabled-option '; ?>" id="tr_popup_captcha">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Captcha for comments', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo (BWG()->is_pro && !$row->gdpr_compliance) ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_captcha" id="popup_enable_captcha_1" value="1" <?php if ($row->popup_enable_captcha && !$row->gdpr_compliance) echo 'checked="checked"'; ?> /><label for="popup_enable_captcha_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo (BWG()->is_pro && !$row->gdpr_compliance) ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_captcha" id="popup_enable_captcha_0" value="0" <?php if (!$row->popup_enable_captcha || $row->gdpr_compliance) echo 'checked="checked"'; ?> /><label for="popup_enable_captcha_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enable this setting to place Captcha word verification in comments section.', BWG()->prefix) ?></p>
            <p class="description"><?php _e('Note, this option cannot be used with GDPR compliance.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_comment_moderation">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable comments moderation', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="comment_moderation" id="comment_moderation_1" value="1" <?php if ($row->comment_moderation) echo 'checked="checked"'; ?> /><label for="comment_moderation_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="comment_moderation" id="comment_moderation_0" value="0" <?php if (!$row->comment_moderation) echo 'checked="checked"'; ?> /><label for="comment_moderation_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Moderate each comment left on images by activating this setting.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_info">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show image info', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_enable_info" id="popup_enable_info_1" value="1" <?php if ($row->popup_enable_info) echo 'checked="checked"'; ?> /><label for="popup_enable_info_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_enable_info" id="popup_enable_info_0" value="0" <?php if (!$row->popup_enable_info) echo 'checked="checked"'; ?> /><label for="popup_enable_info_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate this setting to show Info button among lightbox control buttons.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_info_always_show">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Display info by default', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_info_always_show" id="popup_info_always_show_1" value="1" <?php if ($row->popup_info_always_show) echo 'checked="checked"'; ?> /><label for="popup_info_always_show_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_info_always_show" id="popup_info_always_show_0" value="0" <?php if (!$row->popup_info_always_show) echo 'checked="checked"'; ?> /><label for="popup_info_always_show_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enabling this option will let you show image title and description on lightbox by default.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_info_full_width">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Full width info', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_info_full_width" id="popup_info_full_width_1" value="1" <?php if ($row->popup_info_full_width) echo 'checked="checked"'; ?>  /><label for="popup_info_full_width_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_info_full_width" id="popup_info_full_width_0" value="0" <?php if (!$row->popup_info_full_width) echo 'checked="checked"'; ?>  /><label for="popup_info_full_width_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Display info box with the full width of the lightbox by enabling this option.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Next / Previous buttons', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="autohide_lightbox_navigation" id="autohide_lightbox_navigation_1" value="1" <?php if ($row->autohide_lightbox_navigation ) echo 'checked="checked"'; ?> /><label for="autohide_lightbox_navigation_1" class="wd-radio-label"><?php _e('On hover', BWG()->prefix); ?></label>
              <input type="radio" name="autohide_lightbox_navigation" id="autohide_lightbox_navigation_0" value="0" <?php if (!$row->autohide_lightbox_navigation ) echo 'checked="checked"'; ?> /><label for="autohide_lightbox_navigation_0" class="wd-radio-label"><?php _e('Always', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Choose to display Next/Previous buttons of Photo Gallery lightbox on hover or always.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_hit_counter">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Display views counter', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_hit_counter" id="popup_hit_counter_1" value="1" <?php if ($row->popup_hit_counter) echo 'checked="checked"'; ?> /><label for="popup_hit_counter_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_hit_counter" id="popup_hit_counter_0" value="0" <?php if (!$row->popup_hit_counter) echo 'checked="checked"'; ?> /><label for="popup_hit_counter_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Show the number of views, when a gallery image was opened in lightbox.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_rate">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable rating', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_rate" id="popup_enable_rate_1" value="1" <?php if ($row->popup_enable_rate) echo 'checked="checked"'; ?> /><label for="popup_enable_rate_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_rate" id="popup_enable_rate_0" value="0" <?php if (!$row->popup_enable_rate) echo 'checked="checked"'; ?> /><label for="popup_enable_rate_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Allow users to rate your images by adding rating feature to Photo Gallery lightbox.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
      </div>
      <div class="wd-box-content wd-width-33">
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_fullsize_image">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Display Original Image button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_enable_fullsize_image" id="popup_enable_fullsize_image_1" value="1" <?php if ($row->popup_enable_fullsize_image) echo 'checked="checked"'; ?> /><label for="popup_enable_fullsize_image_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_enable_fullsize_image" id="popup_enable_fullsize_image_0" value="0" <?php if (!$row->popup_enable_fullsize_image) echo 'checked="checked"'; ?> /><label for="popup_enable_fullsize_image_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Let users view original versions of your images by enabling this button.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_download">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show download button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="popup_enable_download" id="popup_enable_download_1" value="1" <?php if ($row->popup_enable_download) echo 'checked="checked"'; ?> /><label for="popup_enable_download_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="popup_enable_download" id="popup_enable_download_0" value="0" <?php if (!$row->popup_enable_download) echo 'checked="checked"'; ?> /><label for="popup_enable_download_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('This option will allow users to download gallery images while viewing them in lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_image_count">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show image counter', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="show_image_counts" id="show_image_counts_current_image_number_1" value="1" <?php if ($row->show_image_counts) echo 'checked="checked"'; ?> /><label for="show_image_counts_current_image_number_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="show_image_counts" id="show_image_counts_current_image_number_0" value="0" <?php if (!$row->show_image_counts) echo 'checked="checked"'; ?> /><label for="show_image_counts_current_image_number_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enable this option to display image counter on Photo Gallery lightbox.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_image_cycle">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable looping', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input type="radio" name="enable_loop" id="enable_loop_1" value="1" <?php if ($row->enable_loop) echo 'checked="checked"'; ?> /><label for="enable_loop_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input type="radio" name="enable_loop" id="enable_loop_0" value="0" <?php if (!$row->enable_loop) echo 'checked="checked"'; ?> /><label for="enable_loop_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate looping to start lightbox navigation from the beginning when users reach its last image.', BWG()->prefix) ?></p>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Enable', BWG()->prefix); ?> AddThis</label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="enable_addthis" id="enable_addthis_1" value="1" <?php if ($row->enable_addthis ) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('', 'tr_addthis_profile_id', 'enable_addthis_1')" /><label for="enable_addthis_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="enable_addthis" id="enable_addthis_0" value="0" <?php if (!$row->enable_addthis ) echo 'checked="checked"'; ?> onClick="bwg_enable_disable('none', 'tr_addthis_profile_id', 'enable_addthis_0')" /><label for="enable_addthis_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Display AddThis on Photo Gallery lightbox by activating this option.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_addthis_profile_id">
          <div class="wd-group">
            <label class="wd-label" for="addthis_profile_id">AddThis <?php _e('profile ID', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="text" name="addthis_profile_id" id="addthis_profile_id" value="<?php echo $row->addthis_profile_id; ?>" />
            </div>
            <p class="description"><?php _e('Provide the ID of your profile to connect to AddThis.', BWG()->prefix); ?><br><?php echo sprintf(__('Create an account %s.', BWG()->prefix), '<a href="https://www.addthis.com/register" target="_blank">' . __('here', BWG()->prefix) . '</a>'); ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_facebook">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Facebook button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_facebook" id="popup_enable_facebook_1" value="1" <?php if ($row->popup_enable_facebook) echo 'checked="checked"'; ?> /><label for="popup_enable_facebook_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_facebook" id="popup_enable_facebook_0" value="0" <?php if (!$row->popup_enable_facebook) echo 'checked="checked"'; ?> /><label for="popup_enable_facebook_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enabling this setting will add Facebook sharing button to Photo Gallery lightbox.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_twitter">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Twitter button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_twitter" id="popup_enable_twitter_1" value="1" <?php if ($row->popup_enable_twitter) echo 'checked="checked"'; ?> /><label for="popup_enable_twitter_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_twitter" id="popup_enable_twitter_0" value="0" <?php if (!$row->popup_enable_twitter) echo 'checked="checked"'; ?> /><label for="popup_enable_twitter_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Enable this setting to add Tweet button to Photo Gallery lightbox.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_pinterest">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Pinterest button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_pinterest" id="popup_enable_pinterest_1" value="1" <?php if ($row->popup_enable_pinterest) echo 'checked="checked"'; ?> /><label for="popup_enable_pinterest_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_pinterest" id="popup_enable_pinterest_0" value="0" <?php if (!$row->popup_enable_pinterest) echo 'checked="checked"'; ?> /><label for="popup_enable_pinterest_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Activate Pin button of Photo Gallery lightbox by enabling this setting.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox <?php echo BWG()->is_pro ? '' : ' bwg-disabled-option'; ?>" id="tr_popup_tumblr">
          <div class="wd-group">
            <label class="wd-label"><?php _e('Show Tumblr button', BWG()->prefix); ?></label>
            <div class="bwg-flex">
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_tumblr" id="popup_enable_tumblr_1" value="1" <?php if ($row->popup_enable_tumblr) echo 'checked="checked"'; ?> /><label for="popup_enable_tumblr_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
              <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="popup_enable_tumblr" id="popup_enable_tumblr_0" value="0" <?php if (!$row->popup_enable_tumblr) echo 'checked="checked"'; ?> /><label for="popup_enable_tumblr_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
            </div>
            <p class="description"><?php _e('Allow users to share images on Tumblr from Photo Gallery lightbox by activating this setting.', BWG()->prefix) ?></p>
            <?php if ( !BWG()->is_pro ) { ?><p class="description spider_free_version"><?php echo BWG()->free_msg; ?></p><?php } ?>
          </div>
        </div>
        <?php
        if ( function_exists('BWGEC') ) {
          ?>
          <div class="wd-box-content wd-width-100 bwg-lightbox bwg-lightbox-lightbox" id="tr_popup_ecommerce">
            <div class="wd-group">
              <label class="wd-label"><?php _e('Show Ecommerce button', BWG()->prefix); ?></label>
              <div class="bwg-flex">
                <input type="radio" name="popup_enable_ecommerce" id="popup_enable_ecommerce_1" value="1" <?php if ($row->popup_enable_ecommerce) echo 'checked="checked"'; ?> /><label for="popup_enable_ecommerce_1" class="wd-radio-label"><?php _e('Yes', BWG()->prefix); ?></label>
                <input type="radio" name="popup_enable_ecommerce" id="popup_enable_ecommerce_0" value="0" <?php if (!$row->popup_enable_ecommerce) echo 'checked="checked"'; ?> /><label for="popup_enable_ecommerce_0" class="wd-radio-label"><?php _e('No', BWG()->prefix); ?></label>
              </div>
              <p class="description"><?php _e('Enable this option to display ecommerce icon on Photo Gallery lightbox', BWG()->prefix) ?></p>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  }
}
