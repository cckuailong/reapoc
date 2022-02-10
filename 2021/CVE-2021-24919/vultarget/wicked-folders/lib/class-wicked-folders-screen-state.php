<?php

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Holds details about a screen's state.
 */
final class Wicked_Folders_Screen_State {

    public $screen_id           	= false;
    public $user_id             	= false;
    public $folder              	= false;
	public $folder_type 			= 'Wicked_Folders_Term_Folder';
    public $expanded_folders    	= array( '0' );
    public $tree_pane_width     	= 292;
	public $hide_assigned_items 	= true;
	public $orderby 				= 'wicked_folder_order';
	public $order 					= 'asc';
	public $is_folder_pane_visible 	= true;
	public $lang 					= false;
	public $sort_mode 				= 'custom';

    public function __construct( $screen_id, $user_id, $lang = false ) {
        $this->screen_id    = $screen_id;
        $this->user_id      = $user_id;
		$this->lang 		= $lang;

        $state = get_user_meta( $user_id, 'wicked_folders_plugin_state', true );

        if ( isset( $state['screens'][ $screen_id ] ) ) {

            $screen_state = $state['screens'][ $screen_id ];

            if ( isset( $screen_state['folder'] ) ) {
                $this->folder = ( string ) $screen_state['folder'];
            }

			if ( isset( $screen_state['folder_type'] ) ) {
                $this->folder_type = ( string ) $screen_state['folder_type'];
            }

            if ( ! empty( $screen_state['expanded_folders'] ) ) {
                $this->expanded_folders = ( array ) $screen_state['expanded_folders'];
            }

            if ( isset( $screen_state['tree_pane_width'] ) ) {
                $this->tree_pane_width = ( int ) $screen_state['tree_pane_width'];
            }

			if ( isset( $screen_state['hide_assigned_items'] ) ) {
				$this->hide_assigned_items = ( bool ) $screen_state['hide_assigned_items'];
			}

			if ( isset( $screen_state['orderby'] ) ) {
				$this->orderby = $screen_state['orderby'];
			}

			if ( isset( $screen_state['order'] ) ) {
				$this->order = $screen_state['order'];
			}

			if ( isset( $screen_state['is_folder_pane_visible'] ) ) {
				$this->is_folder_pane_visible = $screen_state['is_folder_pane_visible'];
			}

			if ( isset( $screen_state['sort_mode'] ) ) {
				$this->sort_mode = $screen_state['sort_mode'];
			}

			// Is there a language variation specified?
			if ( $this->lang ) {
				// Is there a folder available for the language?
				if ( isset( $screen_state['langs'][ $this->lang ]['folder'] ) ) {
					$this->folder = $screen_state['langs'][ $this->lang ]['folder'];
				} else {
					// No folder found for the language so default to 'All Folders'
					$this->folder = '0';
				}

				if ( isset( $screen_state['langs'][ $this->lang ]['folder_type'] ) ) {
					$this->folder_type = $screen_state['langs'][ $this->lang ]['folder_type'];
				} else {
					$this->folder_type = 'Wicked_Folders_Term_Folder';
				}

				if ( isset( $screen_state['langs'][ $this->lang ]['expanded_folders'] ) ) {
					$this->expanded_folders = ( array ) $screen_state['langs'][ $this->lang ]['expanded_folders'];
				} else {
					$this->expanded_folders = array( '0' );
				}
			}
        }

		$this->expanded_folders = array_unique( $this->expanded_folders );

		// Filter tree pane width
		$this->tree_pane_width = apply_filters( 'wicked_folders_screen_state_tree_pane_width', $this->tree_pane_width, $this );

		/**
		 * Give others a chance to override the constructed screen state object.
		 *
		 * @since 2.18.4
		 *
		 * @param object $state
		 *  The current screen state instance.
		 */
		apply_filters( 'wicked_folders_construct_screen_state', $this );

        return $this;

    }

	public function save() {

		$states = ( array ) get_user_meta( $this->user_id, 'wicked_folders_plugin_state', true );
		$existing_state = isset( $states['screens'][ $this->screen_id ] ) ? $states['screens'][ $this->screen_id ] : array();
		$state = array(
			'tree_pane_width' 			=> $this->tree_pane_width,
			'folder' 					=> $this->folder,
			'expanded_folders' 			=> $this->expanded_folders,
			'hide_assigned_items' 		=> $this->hide_assigned_items,
			'folder_type' 				=> $this->folder_type,
			'orderby' 					=> $this->orderby,
			'order' 					=> $this->order,
			'is_folder_pane_visible' 	=> $this->is_folder_pane_visible,
			'sort_mode' 				=> $this->sort_mode,
		);

		$state = array_merge( $existing_state, $state );

		if ( ! isset( $state['langs'] ) ) {
			$state['langs'] = array();
		}

		if ( ! isset( $states['screens'][ $this->screen_id ] ) ) {
			$states['screens'][ $this->screen_id ] = array();
		}

		if ( $this->lang ) {
			$state['langs'][ $this->lang ] = array(
				'folder' 			=> $this->folder,
				'folder_type' 		=> $this->folder_type,
				'expanded_folders' 	=> $this->expanded_folders,
			);
		}

		$states['screens'][ $this->screen_id ] = $state;

		update_user_meta( $this->user_id, 'wicked_folders_plugin_state', $states );

	}

}
