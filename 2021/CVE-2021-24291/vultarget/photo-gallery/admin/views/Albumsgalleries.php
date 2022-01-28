<?php

/**
 * Class AlbumsgalleriespView_bwg
 */
class AlbumsgalleriesView_bwg extends AdminView_bwg {
  public function __construct() {
    // Register and include styles and scripts.
    BWG()->register_admin_scripts();
    wp_print_styles(BWG()->prefix . '_tables');
    wp_print_scripts(BWG()->prefix . '_admin');
    ?>
    <script>
      var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
  }

  /**
   * Display page.
   *
   * @param $params
   */
  public function display( $params = array() ) {
    ob_start();
    $params['page_url'] = add_query_arg(array(
      'action' => 'albumsgalleries_' . BWG()->prefix,
      'album_id' => $params['album_id'],
      'width' => '785',
      'height' => '550',
      BWG()->nonce => wp_create_nonce('albumsgalleries_' . BWG()->prefix),
      'TB_iframe' => '1'
    ), admin_url('admin-ajax.php'));
    echo $this->body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_albumsgalleries',
      'name' => BWG()->prefix . '_albumsgalleries',
      'class' => BWG()->prefix . '_albumsgalleries wd-form wp-core-ui media-frame',
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
    <div id="loading_div"></div>
    <div class="wd-table-container">
	  <?php
		echo $this->title( array(
			'title' => $params['page_title'],
			'title_class' => 'wd-header',
			'add_new_button' => FALSE,
      'popup_window' => TRUE,
		  )
		);
	  ?>
    <div class="wp-search-wrap">
      <?php echo $this->search(); ?>
      <div class="tablenav top">
        <?php echo $this->pagination($params['page_url'], $params['total'], $params['items_per_page']); ?>
      </div>
    </div>
	  <div>
      <table class="wp-list-table widefat fixed pages media">
        <thead>
          <td class="sortable manage-column column-cb check-column table_small_col">
            <input id="check_all" type="checkbox" />
          </td>
          <?php echo WDWLibrary::ordering('name', $params['orderby'], $params['order'], __('Title', BWG()->prefix), $params['page_url'], 'column-primary'); ?>
          <?php echo WDWLibrary::ordering('is_album', $params['orderby'], $params['order'], __('Type', BWG()->prefix), $params['page_url']); ?>
        </thead>
        <tbody id="tbody_albums_galleries">
        <?php
        if ($params['rows']) {
          $iterator = 0;
          foreach ($params['rows'] as $row) {
            $alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
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
            <tr id="tr_<?php echo $iterator; ?>" <?php echo $alternate; ?>>
              <th class="table_small_col check-column">
                <input id="check_<?php echo $iterator; ?>" name="check_<?php echo $iterator; ?>" type="checkbox" data-id="<?php echo $row->id; ?>" data-is-album="<?php echo htmlspecialchars(addslashes($row->is_album)); ?>" data-preview-image="<?php echo esc_url( $preview_image ); ?>" data-name="<?php echo esc_attr( $row->name ); ?>" data-status="<?php echo !$row->published ? 'dashicons-hidden' : 'bwg-hidden'; ?>" />
              </th>
              <td class="column-primary column-title" data-colname="<?php _e('Title', BWG()->prefix); ?>">
                <strong class="has-media-icon">
                  <a class="wd-pointer" onclick="window.parent.bwg_add_album_gallery('<?php echo $row->id; ?>', '<?php echo $row->is_album; ?>', '<?php echo esc_url( $preview_image ); ?>', '<?php echo esc_attr( $row->name ); ?>','<?php echo !$row->published ? 'dashicons-hidden' : 'bwg-hidden' ?>')" id="a_<?php echo $iterator; ?>">
                    <span class="media-icon image-icon">
                      <img class="preview-image" title="<?php echo esc_attr( $row->name ); ?>" src="<?php echo esc_url( $preview_image ); ?>" width="60" height="60" />
                    </span>
                    <?php echo esc_html( $row->name ); ?>
                  </a>
                  <?php if ( !$row->published ) { ?>
                    — <span class="post-state"><?php _e('Unpublished', BWG()->prefix); ?></span>
                            <?php } ?>
                </strong>
                <button class="toggle-row" type="button">
                  <span class="screen-reader-text"><?php _e('Show more details', BWG()->prefix); ?></span>
                </button>
              </td>
              <td id="type_<?php echo $iterator; ?>" class="table_medium_col_uncenter" data-colname="<?php _e('Type', BWG()->prefix); ?>">
                <?php echo ($row->is_album ? __("Gallery group", BWG()->prefix) : __("Gallery", BWG()->prefix)) ; ?>
              </td>
            </tr>
            <?php
            $iterator++;
          }
        }
        else {
          echo WDWLibrary::no_items('galleries or gallery groups', 3);
        }
        ?>
        </tbody>
      </table>
    </div>
    </div>
    <div class="media-frame-toolbar">
      <div class="media-toolbar">
        <div class="media-toolbar-primary search-form">
          <button class="button media-button button-primary button-large media-button-insert" type="button" onclick="jQuery('#loading_div').show(); spider_get_items();"><?php _e('Add to Gallery Group', BWG()->prefix); ?></button>
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
