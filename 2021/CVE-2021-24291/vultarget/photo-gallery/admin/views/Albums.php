<?php

/**
 * Class AlbumsView_bwg
 */
class AlbumsView_bwg extends AdminView_bwg {

  public function __construct() {
    wp_enqueue_script(BWG()->prefix . '_jquery.ui.touch-punch.min');
    parent::__construct();
  }

  /**
   * Display page.
   *
   * @param $params
   */
	public function display( $params = array() ) {
    wp_enqueue_script(BWG()->prefix . '_jquery.ui.touch-punch.min');
		ob_start();
		echo $this->body($params);
		// Pass the content to form.
		$form_attr = array(
		  'id' => BWG()->prefix . '_albums',
		  'name' => BWG()->prefix . '_albums',
		  'class' => BWG()->prefix . '_albums wd-form',
		  'action' => add_query_arg(array( 'page' => 'albums_' . BWG()->prefix ), 'admin.php'),
		);
		echo $this->form(ob_get_clean(), $form_attr);
	}

	/**
	* Generate page body.
	*
	* @param $params
	*/
	public function body( $params = array() ) {
		echo $this->title( array(
							'title' => $params['page_title'],
							'title_class' => 'wd-header',
							'add_new_button' => array(
								'href' => add_query_arg(array( 'page' => $params['page'], 'task' => 'edit' ), admin_url('admin.php')),
							),
              'add_new_button_text' => __('Add new group', BWG()->prefix),
              'how_to_button' => true,
						  )
						);
		echo $this->search();
		?>
		<div class="tablenav top">
		<?php
			echo $this->bulk_actions($params['actions'], TRUE);
			echo $this->pagination($params['page_url'], $params['total'], $params['items_per_page']);
		?>
		</div>
		<table class="images_table adminlist table table-striped wp-list-table widefat fixed pages media bwg-gallery-lists">
			<thead class="alternate">
				<td id="cb" class="column-cb check-column">
				  <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all', BWG()->prefix); ?></label>
					<input id="check_all" type="checkbox" onclick="spider_check_all(this)" />
				</td>
				<?php echo WDWLibrary::ordering('name', $params['orderby'], $params['order'], __('Title', BWG()->prefix), $params['page_url'], 'column-primary'); ?>
				<?php echo WDWLibrary::ordering('author', $params['orderby'], $params['order'], __('Author', BWG()->prefix), $params['page_url']); ?>
			</thead>
			<tbody>
			<?php
      if ( $params['rows'] ) {
        $alternate = 'alternate';
        foreach ( $params['rows'] as $row ) {
					$user = get_userdata($row->author);
					$alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
					$edit_url = add_query_arg(array(
												  'page' => $params['page'],
												  'task' => 'edit',
												  'current_id' => $row->id,
												), admin_url('admin.php'));
					$publish_url = add_query_arg(array(
													 'task' => ($row->published ? 'unpublish' : 'publish'),
													 'current_id' => $row->id,
												   ), $params['page_url']);
					$duplicate_url = add_query_arg(array( 'task' => 'duplicate', 'current_id' => $row->id ), $params['page_url']);
					$delete_url = add_query_arg(array( 'task' => 'delete', 'current_id' => $row->id ), $params['page_url']);
					$preview_url =  WDWLibrary::get_custom_post_permalink( array('slug' => $row->slug, 'post_type' => 'album' ));
					$preview_image = BWG()->plugin_url . '/images/no-image.png';
					if ( !empty($row->preview_image) ) {
						$preview_image = BWG()->upload_url . $row->preview_image;
					}
					if ( !empty($row->random_preview_image)) {
						$preview_image = BWG()->upload_url . $row->random_preview_image;
						if ( WDWLibrary::check_external_link($row->random_preview_image) ) {
							$preview_image = $row->random_preview_image;
						}
					}
				  ?>
					<tr id="tr_<?php echo $row->id; ?>" <?php echo $alternate; ?>>
						<th class="check-column">
							<input type="checkbox" id="check_<?php echo $row->id; ?>" name="check[<?php echo $row->id; ?>]" onclick="spider_check_all(this)" />
						</th>
						<td class="column-primary column-title" data-colname="<?php _e('Title', BWG()->prefix); ?>">
							<strong class="has-media-icon">
								<a href="<?php echo $edit_url; ?>">
								  <span class="media-icon image-icon">
									<img class="preview-image" title="<?php echo esc_attr( $row->name ); ?>" src="<?php echo esc_url( $preview_image ); ?>" width="60" height="60" />
								  </span>
								  <?php echo esc_html( $row->name ); ?>
								</a>
								<?php if ( !$row->published ) { ?>
								  — <span class="post-state"><?php _e('Unpublished', BWG()->prefix); ?></span>
								<?php } ?>
							</strong>
							<div class="row-actions">
								<span><a href="<?php echo $edit_url; ?>"><?php _e('Edit', BWG()->prefix); ?></a> |</span>
								<span><a href="<?php echo $publish_url; ?>"><?php echo ($row->published ? __('Unpublish', BWG()->prefix) : __('Publish', BWG()->prefix)); ?></a> |</span>
								<span><a href="<?php echo $duplicate_url; ?>"><?php _e('Duplicate', BWG()->prefix); ?></a> |</span>
								<span class="trash"><a onclick="if (!confirm('<?php echo addslashes(__('Do you want to delete selected item?', BWG()->prefix)); ?>')) {return false;}" href="<?php echo $delete_url; ?>"><?php _e('Delete', BWG()->prefix); ?></a> |</span>
								<span><a href="<?php echo esc_url( $preview_url ); ?>" target="_blank"><?php _e('Preview', BWG()->prefix); ?></a></span>
							</div>
							<button class="toggle-row" type="button">
								<span class="screen-reader-text"><?php _e('Show more details', BWG()->prefix); ?></span>
							</button>
						</td>
						<td data-colname="<?php _e('Author', BWG()->prefix); ?>"><?php echo ( $user ) ? $user->display_name : ''; ?></td>
					</tr>
				<?php
				}
			}
			else {
				echo WDWLibrary::no_items('gallery groups', 3);
			}
			?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<?php echo $this->pagination($params['page_url'], $params['total'], $params['items_per_page']); ?>
		</div>
  	<?php
  }

  /**
   * Add/Edit.
   *
   * @param  array $params .
   *
   * @return string html.
   */
  public function edit( $params = array() ) {
    wp_enqueue_script('jquery-ui-sortable');
    wp_admin_css('thickbox');
    wp_enqueue_media();
    ob_start();
    echo $this->edit_body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_albums',
      'name' => BWG()->prefix . '_albums',
      'class' => BWG()->prefix . '_albums wd-form',
      'action' => $params['form_action'],
      'current_id' => $params['id'],
    );
    echo $this->form(ob_get_clean(), $form_attr);
	}

  /**
   * Generate page edit body.
   *
   * @param $params
   */
  public function edit_body( $params = array() ) {
	  wp_enqueue_style('thickbox');
    wp_enqueue_script('thickbox');
    $row = $params['row'];
    $enable_wp_editor = isset(BWG()->options->enable_wp_editor) ? BWG()->options->enable_wp_editor : 0;
    ?>
    <div class="bwg-page-header wd-list-view-header">
      <div class="wd-page-title wd-header wd-list-view-header-left">
        <div>
          <h1 class="wp-heading-inline bwg-heading"><?php _e('Gallery Group Title', BWG()->prefix); ?></h1>
          <input type="text" id="name" name="name" value="<?php echo !empty($row->name) ? esc_attr( $row->name ) : ''; ?>">
        </div>
        <div class="bwg-page-actions">
          <?php
          if ( $params['shortcode_id'] ) {
            require BWG()->plugin_dir . '/framework/howto/howto.php';
          }
          ?>
          <button class="tw-button-primary button-large" onclick="if (spider_check_required('name', 'Title')) {return false;}; spider_set_input_value('task', 'save')">
            <?php echo ($params['id']) ? __('Update', BWG()->prefix) : __('Publish', BWG()->prefix); ?>
          </button>
          <?php if ($params['id'] && $params['preview_action']) { ?>
            <a class="tw-button-secondary tw-preview-button button-large" href="<?php echo $params['preview_action']; ?>" target="_blank"><?php _e('Preview', BWG()->prefix); ?></a>
          <?php } ?>
        </div>
      </div>
        <?php
        if (!BWG()->is_pro) {
          WDWLibrary::topbar_upgrade_ask_question();
        }?>
      <div class="bwg-clear"></div>
    </div>
    <div class="wd-table meta-box-sortables">
      <div class="wd-table-row wd-table-col-100 wd-table-col-left">
        <div class="wd-box-section">
          <div class="postbox <?php echo $params['id'] ? 'closed' : '' ?>">
            <button class="button-link handlediv" type="button" aria-expanded="true">
              <span class="screen-reader-text"><?php _e('Toggle panel:', BWG()->prefix); ?></span>
              <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
            <h2 class="hndle">
              <span><?php _e('Basic', BWG()->prefix); ?></span>
            </h2>
            <div class="inside">
              <div class="wd-box-content">
                <div class="wd-group">
                  <label class="wd-label" for="preview_image"><?php _e('Preview image', BWG()->prefix); ?></label>
                  <div>
                    <a href="<?php echo $params['add_preview_image_action']; ?>" id="button_preview_image" class="button wd-preview-image-btn thickbox thickbox-preview <?php echo ($row->preview_image == '') ? 'bwg_not-preview-image' : '' ?>" title="<?php _e('Add Preview Image', BWG()->prefix); ?>" onclick="return false;" style="<?php echo !empty($row->preview_image) ? 'display:none;' : '' ?>">
                      <span class="dashicons dashicons-camera"></span><?php _e('Add', BWG()->prefix); ?>
                    </a>
                    <img id="img_preview_image" src="<?php echo $row->preview_image ? (BWG()->upload_url . esc_url($row->preview_image)) : ''; ?>" style="<?php echo empty($row->preview_image) ? 'display:none;' : '' ?>" />
                    <span id="delete_preview_image" class="spider_delete_img dashicons dashicons-no-alt" onclick="spider_remove_url('button_preview_image', 'preview_image', 'delete_preview_image', 'img_preview_image')" style="<?php echo empty($row->preview_image) ? 'display:none;' : '' ?>"></span>
                    <input type="hidden" id="preview_image" name="preview_image" value="<?php echo esc_url($row->preview_image); ?>" />
                    <p class="description"><?php _e('Add a preview image, which will be displayed as the cover image of the gallery group when it is published in a parent gallery group.', BWG()->prefix); ?></p>
                  </div>
                </div>
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Published', BWG()->prefix); ?></label>
                  <input type="radio" class="inputbox" id="published1" name="published" <?php echo(($row->published == 1 || !$params['id']) ? 'checked="checked"' : ''); ?> value="1" />
                  <label for="published1"><?php _e('Yes', BWG()->prefix); ?></label>
                  <input type="radio" class="inputbox" id="published0" name="published" <?php echo(($row->published) ? '' : 'checked="checked"'); ?> value="0" />
                  <label for="published0"><?php _e('No', BWG()->prefix); ?></label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="wd-table-row wd-table-col-100 wd-table-col-left">
        <div class="wd-box-section">
          <div class="postbox closed">
            <button class="button-link handlediv" type="button" aria-expanded="true">
              <span class="screen-reader-text"><?php _e('Toggle panel:', BWG()->prefix); ?></span>
              <span class="toggle-indicator" aria-hidden="false"></span>
            </button>
            <h2 class="hndle">
              <span><?php _e('Advanced', BWG()->prefix); ?></span>
            </h2>
            <div class="inside">
              <div class="wd-group">
                <label class="wd-label"><?php _e('Author', BWG()->prefix); ?></label>
                <span><?php echo esc_html($row->author); ?></span>
              </div>
              <div class="wd-group">
                <label class="wd-label" for="slug"><?php _e('Slug', BWG()->prefix); ?></label>
                <input type="text" id="slug" name="slug" value="<?php echo esc_attr($row->slug); ?>">
                <input type="hidden" id="old_slug" name="old_slug" value="<?php echo esc_attr($row->slug); ?>">
              </div>
              <div class="wd-group">
                <label class="wd-label" for="description"><?php _e('Description', BWG()->prefix); ?> </label>
                <?php
                if ( user_can_richedit() && $enable_wp_editor ) {
                  wp_editor(esc_html($row->description), 'description', array(
                    'teeny' => TRUE,
                    'textarea_name' => 'description',
                    'media_buttons' => FALSE,
                    'textarea_rows' => 5,
                  ));
                }
                else {
                  ?>
                  <textarea cols="36" rows="5" id="description" name="description" class="wd-resize-vertical"><?php echo esc_html($row->description); ?></textarea>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-100 meta-box-sortables">
        <div class="wd-box-section">
          <div class="wd-box-content">
            <div class="wd-group">
              <h2 class="wd-titles"><?php _e('Galleries and Gallery Groups', BWG()->prefix); ?></h2>
              <div id="bwg_tabs" class="bwg_tabs hidden">
                <?php
                foreach ( $params['albums_galleries'] as $item ) {
                  $item->published = !$item->published ? 'dashicons-hidden' : 'bwg-hidden';
                  $item->preview_image = 'style="background-image:url(&quot;' . $item->preview_image . '&quot;)"';
                  echo $this->albumgallery_template($item);
                }
                $template = new stdClass();
                $template->alb_gal_id = '%%alb_gal_id%%';
                $template->is_album = '%%is_album%%';
                $template->preview_image = '%%preview_image%%';
                $template->name = '%%name%%';
                $template->published = '%%status%%';
                echo $this->albumgallery_template($template, TRUE);
                ?>
                <div class="bwg_subtab">
                  <div class="new_tab_image">
                    <a class="new_tab_link thickbox-preview" onclick="jQuery('#loading_div').show();" href="<?php echo $params['add_albums_galleries_action']; ?>">
                      <p id="add_album_gallery_text"><?php _e('Add', BWG()->prefix); ?></p>
                    </a>
                  </div>
                </div>
              </div>
              <input type="hidden" id="albums_galleries" name="albumgallery_ids" value="" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="loading_div" class="bwg_show"></div>
    <input type="hidden" value="<?php echo !empty($row->modified_date) ? $row->modified_date : time() ?>" id="modified_date" name="modified_date" />
	<?php
  }

  public function albumgallery_template($albumgallery_row, $template = false) {
    ob_start();
    if ($template) {
    ?>
    <div id="bwg_template">
    <?php
    }
    ?>
      <div class="bwg_subtab connectedSortable <?php echo 'bwg_subtab_' . $albumgallery_row->published; ?>" data-id="<?php echo $albumgallery_row->alb_gal_id; ?>" data-is-album="<?php echo $albumgallery_row->is_album; ?>" data-status="<?php echo $albumgallery_row->published; ?>">
        <div <?php echo $albumgallery_row->preview_image; ?> class="tab_image">
          <div class="tab_buttons">
            <div class="handle_wrap">
              <span class="bwg_move dashicons dashicons-move" title="<?php _e('Drag to re-order', BWG()->prefix); ?>"></span>
            </div>
            <div class="bwg_tab_title_wrap" title="<?php echo $albumgallery_row->name; ?>">
              <label class="bwg_tab_title" title="<?php echo $albumgallery_row->name; ?>"><?php echo $albumgallery_row->name; ?></label>
            </div>
          </div>
          <div class="overlay">
            <div class="hover_buttons">
              <span class="bwg_tab_remove dashicons dashicons-trash" title="<?php _e('Remove', BWG()->prefix); ?>" onclick="bwg_remove_album_gallery(this)"></span>
              <span class="bwg_tab_status dashicons <?php echo $albumgallery_row->published; ?>" title="<?php _e('Unpublished', BWG()->prefix); ?>"></span>
              <span class="bwg_clear"></span>
            </div>
          </div>
        </div>
      </div>
    <?php
    if ($template) {
    ?>
    </div>
    <?php
    }
    return ob_get_clean();
  }
}
