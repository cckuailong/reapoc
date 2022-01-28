<?php
class AddTagsView_bwg extends AdminView_bwg {
  public function __construct() {
    // Register and include styles and scripts.
    BWG()->register_admin_scripts();
    wp_print_styles(BWG()->prefix . '_tables');
    wp_print_scripts(BWG()->prefix . '_admin');
  }

  /**
   * Display page.
   *
   * @param $params
   */
  public function display( $params = array() ) {
    ob_start();
    $params['page_url'] = add_query_arg(array(
                                          'action' => 'addTags_' . BWG()->prefix,
                                          'bwg_width' => '785',
                                          'bwg_height' => '550',
                                          'TB_iframe' => '1',
                                        ), admin_url('admin-ajax.php'));
    echo $this->body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_tags',
      'name' => BWG()->prefix . '_tags',
      'class' => BWG()->prefix . '_tags wd-form wp-core-ui media-frame',
      'action' => $params['page_url'],
    );
    echo $this->form(ob_get_clean(), $form_attr);

    wp_print_scripts('common'); // Check all.

    die();
  }

  /**
   * Generate page body.
   *
   * @param $params
   */
  public function body( $params = array() ) {
	  ?>
	<div class="wd-table-container">
		<?php
    $image_id = WDWLibrary::get('image_id', 0);
    echo $this->title( array(
               'title' => $params['page_title'],
               'title_class' => 'wd-header',
               'add_new_button' => FALSE,
                'popup_window' => TRUE,
               )
    );
    $params['page_url'] = add_query_arg(array('image_id' => $image_id), $params['page_url']);
		?>
		<div class="wp-search-wrap">
			<?php echo $this->search(); ?>
			<div class="tablenav top">
				<?php echo $this->pagination($params['page_url'], $params['total'], $params['items_per_page']); ?>
			</div>
		</div>
      <div>
      <table class="adminlist table table-striped wp-list-table widefat fixed pages">
        <thead>
        <td id="cb" class="column-cb check-column">
          <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all', BWG()->prefix); ?></label>
          <input id="check_all" type="checkbox" onclick="spider_check_all(this)" />
        </td>
        <?php echo WDWLibrary::ordering('name', $params['orderby'], $params['order'], __('Name', BWG()->prefix), $params['page_url'], 'column-primary'); ?>
        </thead>
        <tbody id="tbody_arr">
        <?php
        if ( $params['rows'] ) {
          foreach ( $params['rows'] as $row ) {
            $alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
            ?>
            <tr id="tr_<?php echo $row->id; ?>" <?php echo $alternate; ?>>
              <th class="check-column">
                <input class="tags"
                       type="checkbox"
                       id="check_<?php echo $row->id; ?>"
                       name="check[<?php echo $row->id; ?>]"
                       onclick="spider_check_all(this)"
                       data-id="<?php echo $row->id; ?>"
                       data-name="<?php echo $row->name; ?>" />
              </th>
              <td class="column-primary column-title" data-colname="<?php _e('Name', BWG()->prefix); ?>">
                <a class="cursor-pointer" onclick="<?php echo $image_id ? 'window.parent.bwg_add_tag(\'' . $image_id . '\', [\'' . $row->id . '\'],[\'' . htmlspecialchars(addslashes($row->name)) . '\'])' : 'bwg_bulk_add_tags(\'' . $row->id . '\', \'' . 'add' . '\')'; ?>;" id="a_<?php echo $row->id; ?>">
                  <?php echo $row->name; ?>
                </a>
              </td>
            </tr>
            <?php
          }
        }
        else {
          echo WDWLibrary::no_items('tags', 2);
        }
        ?>
        </tbody>
      </table>
    </div>
    </div>
	<input id="image_id" name="image_id" type="hidden" value="<?php echo $image_id; ?>" />
	<div class="media-frame-toolbar">
		<div class="media-toolbar">
		  <div class="media-toolbar-primary search-form">
      <button class="button media-button button button-large media-button-insert" type="button" onclick="<?php echo $image_id ? 'bwg_remove_tags(\'' . $image_id . '\')' : 'bwg_bulk_add_tags(\'' . '' . '\',\'' . 'remove' . '\')'; ?>"><?php _e('Remove from image', BWG()->prefix); ?></button>
			<button class="button media-button button-primary button-large media-button-insert" type="button" onclick="<?php echo $image_id ? 'bwg_add_tags(\'' . $image_id . '\')' : 'bwg_bulk_add_tags(\'' . '' . '\',\'' . 'add' . '\')'; ?>"><?php _e('Add to image', BWG()->prefix); ?></button>
		  </div>
		</div>
	</div>
	<script>
	jQuery(window).on('load',function(){
		jQuery("#loading_div", window.parent.document).hide();
	});
  </script>
    <?php
  }
}
