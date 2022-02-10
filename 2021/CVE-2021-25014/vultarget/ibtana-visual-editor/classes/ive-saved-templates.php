<?php self::ibtana_visual_editor_banner_head(); ?>

<div class="wrap">

  <?php $_wpnonce = wp_create_nonce( '_wpnonce' ); ?>

  <?php
  $args = array(
    'post_type'   =>  'ibtana_template'
  );

  $query = new WP_Query( $args );

  $admin_ajax = admin_url( 'admin-ajax.php' );

  $iepa_key = get_option( str_replace( '-', '_', 'ibtana-ecommerce-product-addons' ) . '_license_key' );
  $is_iepa_activated = false;
  if ( $iepa_key ) {
    if ( isset( $iepa_key['license_key'] ) && isset( $iepa_key['license_status'] ) ) {
      if ( ( $iepa_key['license_key'] != '' ) && ( $iepa_key['license_status'] == 1 ) ) {
        $is_iepa_activated = true;
      }
    }
  }
  ?>

  <h1 class="wp-heading-inline">
    <?php esc_html_e( 'Ibtana templates', 'ibtana-visual-editor' ); ?>
  </h1>

  <hr class="wp-header-end">

  <h2 class="screen-reader-text">
    <?php esc_html_e( 'Filter posts list', 'ibtana-visual-editor' ); ?>
  </h2>

  <form id="posts-filter" method="get">

    <input type="hidden" name="post_status" class="post_status_page" value="all">
    <input type="hidden" name="post_type" class="post_type_page" value="ibtana_template">



    <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $_wpnonce; ?>">
    <input type="hidden" name="_wp_http_referer" value="edit.php?post_type=ibtana_template">

    <?php if ( $is_iepa_activated == true ): ?>
      <div class="tablenav top">
        <div class="alignleft actions bulkactions">
          <label for="bulk-action-selector-top" class="screen-reader-text">
            <?php esc_html_e( 'Select bulk action', 'ibtana-visual-editor' ); ?>
          </label>
          <select name="action" id="bulk-action-selector-top">
            <option value="-1"><?php esc_html_e( 'Bulk actions', 'ibtana-visual-editor' ); ?></option>
            <option value="delete"><?php esc_html_e( 'Delete permanently', 'ibtana-visual-editor' ); ?></option>
          </select>
          <input type="submit" id="doaction" class="button action" value="<?php esc_attr_e( 'Apply', 'ibtana-visual-editor' ); ?>">
        </div>
        <div class="tablenav-pages one-page">
          <span class="displaying-num">
            <?php esc_html_e( count( $query->posts ) . ' item(s)', 'ibtana-visual-editor' ); ?>
          </span>
        </div>
        <br class="clear">
      </div>
    <?php else: ?>
      <div class="top ive-top-item-count">
        <div class="tablenav-pages one-page">
          <span class="displaying-num">
            <?php esc_html_e( count( $query->posts ) . ' item(s)', 'ibtana-visual-editor' ); ?>
          </span>
        </div>
      </div>
    <?php endif; ?>


    <h2 class="screen-reader-text">
      <?php esc_html_e( 'Posts list', 'ibtana-visual-editor' ); ?>
    </h2>

    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <tr>

          <?php if ( $is_iepa_activated == true ): ?>
            <td id="cb" class="manage-column column-cb check-column">
              <label class="screen-reader-text" for="cb-select-all-1">
                <?php esc_html_e( 'Select All', 'ibtana-visual-editor' ); ?>
              </label>
              <input id="cb-select-all-1" type="checkbox">
            </td>
          <?php endif; ?>

          <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
            <a>
              <span>
                <?php esc_html_e( 'Title', 'ibtana-visual-editor' ); ?>
              </span>
            </a>
          </th>
          <th scope="col" id="taxonomy-ibtana_template_type" colspan="2" class="manage-column column-taxonomy-ibtana_template_type">
            <?php esc_html_e( 'Ibtana Template Type', 'ibtana-visual-editor' ); ?>
          </th>
        </tr>
      </thead>

      <tbody id="the-list">

        <?php if ( $query->posts ): ?>

          <?php foreach ( $query->posts as $ibtana_template_post ): ?>

            <tr id="post-<?php echo $ibtana_template_post->ID; ?>" class="iedit author-self level-0 post-<?php echo $ibtana_template_post->ID; ?> type-ibtana_template status-publish hentry entry">

              <?php if ( $is_iepa_activated == true ): ?>
                <th scope="row" class="check-column">
                  <label class="screen-reader-text" for="cb-select-<?php echo $ibtana_template_post->ID; ?>">
                    <?php _e( 'Select ' . $ibtana_template_post->post_title, 'ibtana-visual-editor' ); ?>
                  </label>
                  <input id="cb-select-<?php echo $ibtana_template_post->ID; ?>" type="checkbox" name="post[]" value="<?php echo $ibtana_template_post->ID; ?>">
                </th>
              <?php endif; ?>


              <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">

                <strong>
                  <a class="row-title" href="post.php?post=<?php echo $ibtana_template_post->ID; ?>&amp;action=edit" aria-label="“<?php echo $ibtana_template_post->post_title; ?>” (Edit)">
                    <?php _e( $ibtana_template_post->post_title, 'ibtana-visual-editor' ); ?>
                  </a>
                </strong>

                <div class="row-actions">

                  <span class="edit">
                    <a href="post.php?post=<?php echo $ibtana_template_post->ID; ?>&amp;action=edit" aria-label="Edit “<?php echo $ibtana_template_post->post_title; ?>”">
                      <?php esc_html_e( 'Edit', 'ibtana-visual-editor' ); ?>
                    </a> |
                  </span>


                  <?php if ( $is_iepa_activated == true ): ?>
                  <span class="trash">
                    <a post-id="<?php echo $ibtana_template_post->ID; ?>" class="submitdelete ive-submitdelete" aria-label="Delete “<?php echo $ibtana_template_post->post_title; ?>”">
                      <?php esc_html_e( 'Delete', 'ibtana-visual-editor' ); ?>
                    </a> |
                  </span>
                  <?php endif; ?>


                  <?php foreach ( wp_get_post_terms( $ibtana_template_post->ID, 'ibtana_template_type' ) as $ibtana_template_type ): ?>
                    <?php if ( $ibtana_template_type->name == 'Ibtana Page Template' ): ?>
                      <span class="view">
                        <a target="_blank" href="<?php echo get_permalink( $ibtana_template_post->ID ); ?>" rel="bookmark" aria-label="Preview “Untitled”">
                          <?php esc_html_e( 'Preview', 'ibtana-visual-editor' ); ?>
                        </a>
                      </span>
                      <?php break; ?>
                    <?php endif; ?>
                  <?php endforeach; ?>

                </div>

              </td>

              <td class="taxonomy-ibtana_template_type column-taxonomy-ibtana_template_type" data-colname="Ibtana Template Type" colspan="2">
                <?php foreach ( wp_get_post_terms( $ibtana_template_post->ID, 'ibtana_template_type' ) as $ibtana_template_type ): ?>
                  <a><?php _e( $ibtana_template_type->name, 'ibtana-visual-editor' ); ?></a>
                <?php endforeach; ?>
              </td>

            </tr>

          <?php endforeach; ?>

        <?php else: ?>

          <tr class="no-items">
            <td class="colspanchange" colspan="<?php echo ( $is_iepa_activated == true ) ? '4' : '3'; ?>">
              <?php esc_html_e( 'No ibtana templates found.', 'ibtana-visual-editor' ); ?>
            </td>
          </tr>

        <?php endif; ?>

      </tbody>

      <tfoot>
        <tr>

          <?php if ( $is_iepa_activated == true ): ?>
            <td class="manage-column column-cb check-column">
              <label class="screen-reader-text" for="cb-select-all-2">
                <?php esc_html_e( 'Select All', 'ibtana-visual-editor' ); ?>
              </label>
              <input id="cb-select-all-2" type="checkbox">
            </td>
          <?php endif; ?>

          <th scope="col" class="manage-column column-title column-primary sortable desc">
            <a>
              <?php esc_html_e( 'Title', 'ibtana-visual-editor' ); ?>
            </a>
          </th>
          <th scope="col" colspan="2" class="manage-column column-taxonomy-ibtana_template_type">
            <?php esc_html_e( 'Ibtana Template Type', 'ibtana-visual-editor' ); ?>
          </th>
        </tr>
      </tfoot>

    </table>

    <?php if ( $is_iepa_activated == true ): ?>
      <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
          <label for="bulk-action-selector-bottom" class="screen-reader-text">
            <?php esc_html_e( 'Select bulk action', 'ibtana-visual-editor' ); ?>
          </label>
          <select name="action2" id="bulk-action-selector-bottom">
            <option value="-1"><?php esc_html_e( 'Bulk actions', 'ibtana-visual-editor' ); ?></option>
            <option value="delete"><?php esc_html_e( 'Delete permanently', 'ibtana-visual-editor' ); ?></option>
          </select>
          <input type="submit" id="doaction2" class="button action" value="<?php esc_attr_e( 'Apply', 'ibtana-visual-editor' ); ?>">
        </div>
        <div class="tablenav-pages one-page">
          <span class="displaying-num">
            <?php esc_html_e( count( $query->posts ) . ' item(s)', 'ibtana-visual-editor' ); ?>
          </span>
        </div>
        <br class="clear">
      </div>
    <?php else: ?>
      <div class="bottom ive-bottom-item-count">
        <div class="tablenav-pages one-page">
          <span class="displaying-num">
            <?php esc_html_e( count( $query->posts ) . ' item(s)', 'ibtana-visual-editor' ); ?>
          </span>
        </div>
      </div>
    <?php endif; ?>


  </form>

  <div id="ajax-response"></div>
  <div class="clear"></div>
</div>


<script type="text/javascript">
  (function($) {

    $( '#posts-filter table' ).on( 'click', '.ive-submitdelete', function() {
      var $this_card  = $(this);
      var post_id = $( this ).attr('post-id');
      jQuery( $this_card ).css( 'opacity', 0.5 );
      jQuery.post(
        '<?php echo esc_url( $admin_ajax ); ?>',
        {
          action:   'ive_delete_saved_single_ibtana_template',
          post_id:  post_id,
          wpnonce:  "<?php echo wp_create_nonce( 'ive_whizzie_nonce' ) ?>"
        },
        function( ive_saved_ibtana_template ) {
          if ( ive_saved_ibtana_template.status === false ) {
          } else {
            $this_card.closest( 'tr[id*="post"]' ).remove();
          }
        }
      );
    });

    $( '#posts-filter' ).on( 'submit', function( e ) {
      e.preventDefault();

      var bulk_action_selector_val  = $('#bulk-action-selector-top').val();

      if ( "-1" == bulk_action_selector_val ) {
        return;
      }

      var post_ids = [];
      var post_checkboxes = document.querySelectorAll( 'input[name="post[]"]:checked' );

      for (var i = 0; i < post_checkboxes.length; i++) {
        var post_checkbox = post_checkboxes[i];
        var post_checkbox_id = $(post_checkbox).val();
        post_ids.push( post_checkbox_id );
      }

      if ( !post_ids.length ) {
        return;
      }

      jQuery.post(
        '<?php echo esc_url( $admin_ajax ); ?>', {
          action:   'ive_delete_saved_all_ibtana_templates',
          post_ids:  post_ids,
          wpnonce:  "<?php echo wp_create_nonce( 'ive_whizzie_nonce' ) ?>"
        }, function( res ) {
          location.reload( true );
        }
      );

    } );

  })(jQuery);
</script>
