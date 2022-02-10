<?php

/**
 * Import notification from old plugin.
 *
 * @since 1.0
 */
class BNFW_Import {
	const EMAIL_OPTION = 'bnfw_custom_email_settings';
	const SETTING_OPTION = 'bnfw_settings';
	private $events = array(
		'create_term',
		'publish_post',
		'comment_post',
		'user_register',
		'trackback_post',
		'pingback_post',
		'lostpassword_post',
	);

	/**
	 * Import notification from old plugin.
	 *
	 * @since 1.0
	 */
	public function import() {
		global $wp_roles;
		$roles = $wp_roles->get_names();

		if ( $this->import_needed() ) {
			$old_events = get_option( self::SETTING_OPTION );
			foreach ( $old_events as $event => $value ) {
				if ( '1' == $value ) {
					$event_array = explode( '-', $event );
					if ( 2 == count( $event_array ) ) {
						if ( in_array( $event_array[0], $this->events ) && in_array( $event_array[1], array_keys( $roles ) ) ) {
							$event_array[1] = $roles[ $event_array[1] ];
							$this->insert_notification( $event_array );
						}
					}
				}
			}
			// delete the old options
			$this->delete_option();
		}
	}

	/**
	 * Check if import is needed.
	 *
	 * @since 1.0
	 * @return unknown
	 */
	private function import_needed() {
		if ( get_option( self::EMAIL_OPTION ) && get_option( self::SETTING_OPTION ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Insert notification.
	 *
	 * @param mixed $event
	 */
	private function insert_notification( $event ) {
		$post = array(
			'post_title' => $event[0] . esc_html__( ' for ', 'bnfw' ) . $event[1] . esc_html__( ' (Auto Imported)', 'bnfw' ),
			'post_type' => BNFW_Notification::POST_TYPE,
			'post_content' => '',
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $post );
		if ( $post_id > 0 ) {
			$content = $this->map_notification_content( $event[0] );
			$setting = array(
				'notification' => $this->map_notification( $event[0] ),
				'user-roles'   => array( $event[1] ),
				'users'        => array(),
				'subject'      => $content['subject'],
				'message'      => $content['body'],
			);

			foreach ( $setting as $key => $value ) {
				update_post_meta( $post_id, BNFW_Notification::META_KEY_PREFIX . $key, $value );
			}
		}
	}

	/**
	 * Map old notification type to new notification type.
	 *
	 * @param mixed $event_name
	 *
	 * @return unknown
	 */
	private function map_notification( $event_name ) {
		switch ( $event_name ) {
			case 'create_term':
				return 'new-category';
				break;
			case 'publish_post':
				return 'new-post';
				break;
			case 'comment_post':
				return 'new-comment';
				break;
			case 'user_register':
				return 'new-user';
				break;
			case 'trackback_post':
				return 'new-trackback';
				break;
			case 'pingback_post':
				return 'new-pingback';
				break;
			case 'lostpassword_post':
				return 'user-password';
				break;
		}
	}

	/**
	 * Map content from old plugin.
	 *
	 * @param unknown $event_name
	 *
	 * @return unknown
	 */
	private function map_notification_content( $event_name ) {
		$content = array();
		if ( ! isset( $this->content_map ) ) {
			$this->parse_content();
		}

		return $this->content_map[ $event_name ];
	}

	/**
	 * Parse content from old plugins setting.
	 *
	 * @since 1.0
	 */
	private function parse_content() {
		$old_content = get_option( self::EMAIL_OPTION );
		$content_map = array();
		foreach ( $old_content as $key => $value ) {
			$key_array = explode( '-', $key );
			if ( 3 == count( $key_array ) ) {
				$content_map[ $key_array[2] ][ $key_array[1] ] = $value;
			}
		}
		$this->content_map = $content_map;
	}

	/**
	 * Delete old plugin database options.
	 *
	 * @since 1.0
	 */
	private function delete_option() {
		delete_option( self::EMAIL_OPTION );
		delete_option( self::SETTING_OPTION );
	}
}

?>
