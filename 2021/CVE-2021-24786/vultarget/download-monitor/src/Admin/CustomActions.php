<?php

class DLM_Custom_Actions {

	/**
	 * Setup custom actions
	 */
	public function setup() {
		add_filter( 'request', array( $this, 'sort_columns' ) );

		add_action( "restrict_manage_posts", array( $this, "downloads_by_category" ) );
		add_action( 'delete_post', array( $this, 'delete_post' ) );

		// bulk and quick edit
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_save_post' ), 10, 2 );
	}

	/**
	 * downloads_by_category function.
	 *
	 * @access public
	 *
	 * @param int $show_counts (default: 1)
	 * @param int $hierarchical (default: 1)
	 * @param int $show_uncategorized (default: 1)
	 * @param string $orderby (default: '')
	 *
	 * @return void
	 */
	public function downloads_by_category( $show_counts = 1, $hierarchical = 1, $show_uncategorized = 1, $orderby = '' ) {
		global $typenow, $wp_query;

		if ( $typenow != 'dlm_download' ) {
			return;
		}

		$r                 = array();
		$r['pad_counts']   = 1;
		$r['hierarchical'] = $hierarchical;
		$r['hide_empty']   = 1;
		$r['show_count']   = $show_counts;
		$r['selected']     = ( isset( $wp_query->query['dlm_download_category'] ) ) ? $wp_query->query['dlm_download_category'] : '';

		$r['menu_order'] = false;

		if ( $orderby == 'order' ) {
			$r['menu_order'] = 'asc';
		} elseif ( $orderby ) {
			$r['orderby'] = $orderby;
		}

		$terms = get_terms( 'dlm_download_category', $r );

		if ( ! $terms ) {
			return;
		}

		$output = "<select name='dlm_download_category' id='dropdown_dlm_download_category'>";
		$output .= '<option value="" ' . selected( isset( $_GET['dlm_download_category'] ) ? $_GET['dlm_download_category'] : '', '', false ) . '>' . __( 'Select a category', 'download-monitor' ) . '</option>';
		$output .= $this->walk_category_dropdown_tree( $terms, 0, $r );
		$output .= "</select>";

		echo $output;
	}

	/**
	 * Walk the Product Categories.
	 *
	 * @access public
	 * @return string
	 */
	private function walk_category_dropdown_tree() {
		$args = func_get_args();

		// the user's options are the third parameter
		if ( empty( $args[2]['walker'] ) || ! is_a( $args[2]['walker'], 'Walker' ) ) {
			$walker = new DLM_Category_Walker();
		} else {
			$walker = $args[2]['walker'];
		}

		return call_user_func_array( array( $walker, 'walk' ), $args );
	}

	/**
	 * delete_post function.
	 *
	 * @access public
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function delete_post( $id ) {
		global $wpdb;

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case 'dlm_download' :
					$versions = get_children( 'post_parent=' . $id . '&post_type=dlm_download_version' );
					if ( is_array( $versions ) && count( $versions ) > 0 ) {
						foreach ( $versions as $child ) {
							wp_delete_post( $child->ID, true );
						}
					}
					break;
			}
		}
	}

	/**
	 * sort_columns function.
	 *
	 * @access public
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function sort_columns( $vars ) {
		if ( isset( $vars['orderby'] ) ) {
			if ( 'download_id' == $vars['orderby'] ) {
				$vars['orderby'] = 'ID';
			} elseif ( 'download_count' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_download_count',
					'orderby'  => 'meta_value_num'
				) );

			} elseif ( 'featured' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_featured',
					'orderby'  => 'meta_value'
				) );

			} elseif ( 'members_only' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_members_only',
					'orderby'  => 'meta_value'
				) );

			} elseif ( 'redirect_only' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_redirect_only',
					'orderby'  => 'meta_value'
				) );
			}
		}

		return $vars;
	}

	/**
	 * Custom bulk edit - form
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function quick_edit( $column_name, $post_type ) {

		// only on our PT
		if ( 'dlm_download' != $post_type || 'featured' != $column_name ) {
			return;
		}

		// nonce field
		wp_nonce_field( 'dlm_quick_edit_nonce', 'dlm_quick_edit_nonce' );

		$this->bulk_quick_edit_fields();
	}

	/**
	 * Custom bulk edit - form
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function bulk_edit( $column_name, $post_type ) {

		// only on our PT
		if ( 'dlm_download' != $post_type || 'featured' != $column_name ) {
			return;
		}

		// nonce field
		wp_nonce_field( 'dlm_bulk_edit_nonce', 'dlm_bulk_edit_nonce' );

		$this->bulk_quick_edit_fields();
	}

	/**
	 * Output the build and quick edit fields
	 */
	private function bulk_quick_edit_fields() {
		?>
		<fieldset class="inline-edit-col-right inline-edit-col-dlm">
			<div class="inline-edit-col inline-edit-col-dlm-inner">
				<span class="title"><?php _e( 'Download Monitor Data', 'download-monitor' ); ?></span><br/>
				<label for="_featured"><input type="checkbox" name="_featured" id="_featured"
				                              value="1"/><?php _e( 'Featured download', 'download-monitor' ); ?></label>
				<label for="_members_only"><input type="checkbox" name="_members_only" id="_members_only"
				                                  value="1"/><?php _e( 'Members only', 'download-monitor' ); ?></label>
				<label for="_redirect_only"><input type="checkbox" name="_redirect_only" id="_redirect_only"
				                                   value="1"/><?php _e( 'Redirect to file', 'download-monitor' ); ?>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Quick and bulk edit saving
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 *
	 * @return int
	 */
	public function bulk_and_quick_edit_save_post( $post_id, $post ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check post type is product
		if ( 'dlm_download' != $post->post_type ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'manage_downloads', $post_id ) ) {
			return $post_id;
		}

		// handle bulk
		if ( isset( $_REQUEST['dlm_bulk_edit_nonce'] ) ) {

			// check nonce
			if ( ! wp_verify_nonce( $_REQUEST['dlm_bulk_edit_nonce'], 'dlm_bulk_edit_nonce' ) ) {
				return $post_id;
			}

			// set featured
			if ( isset( $_REQUEST['_featured'] ) ) {
				update_post_meta( $post_id, '_featured', 'yes' );
			}

			// set members only
			if ( isset( $_REQUEST['_members_only'] ) ) {
				update_post_meta( $post_id, '_members_only', 'yes' );
			}

			// set redirect only
			if ( isset( $_REQUEST['_redirect_only'] ) ) {
				update_post_meta( $post_id, '_redirect_only', 'yes' );
			}

		}

		// handle quick
		if ( isset( $_REQUEST['dlm_quick_edit_nonce'] ) ) {

			// check nonce
			if ( ! wp_verify_nonce( $_REQUEST['dlm_quick_edit_nonce'], 'dlm_quick_edit_nonce' ) ) {
				return $post_id;
			}

			// set featured
			if ( isset( $_REQUEST['_featured'] ) ) {
				update_post_meta( $post_id, '_featured', 'yes' );
			} else {
				update_post_meta( $post_id, '_featured', 'no' );
			}

			// set members only
			if ( isset( $_REQUEST['_members_only'] ) ) {
				update_post_meta( $post_id, '_members_only', 'yes' );
			} else {
				update_post_meta( $post_id, '_members_only', 'no' );
			}

			// set redirect only
			if ( isset( $_REQUEST['_redirect_only'] ) ) {
				update_post_meta( $post_id, '_redirect_only', 'yes' );
			} else {
				update_post_meta( $post_id, '_redirect_only', 'no' );
			}

		}

		return $post_id;
	}

}