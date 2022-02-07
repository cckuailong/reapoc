<?php

/**
 * A snippet object
 *
 * @since   2.4.0
 * @package Code_Snippets
 *
 * @property int           $id                 The database ID
 * @property string        $name               The display name
 * @property string        $desc               The formatted description
 * @property string        $code               The executable code
 * @property array         $tags               An array of the tags
 * @property string        $scope              The scope name
 * @property int           $priority           Execution priority
 * @property bool          $active             The active status
 * @property bool          $network            true if is multisite-wide snippet, false if site-wide
 * @property bool          $shared_network     Whether the snippet is a shared network snippet
 * @property string        $modified           The date and time when the snippet data was most recently saved to the database.
 *
 * @property-read array    $tags_list          The tags in string list format
 * @property-read string   $scope_icon         The dashicon used to represent the current scope
 * @property-read int      $modified_timestamp The last modification date in Unix timestamp format.
 * @property-read DateTime $modified_local     The last modification date in the local timezone.
 */
class Code_Snippet {

	/**
	 * MySQL datetime format (YYYY-MM-DD hh:mm:ss)
	 */
	const DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Default value used for a datetime variable.
	 */
	const DEFAULT_DATE = '0000-00-00 00:00:00';

	/**
	 * The snippet metadata fields.
	 * Initialized with default values.
	 * @var array
	 */
	private $fields = array(
		'id'             => 0,
		'name'           => '',
		'desc'           => '',
		'code'           => '',
		'tags'           => array(),
		'scope'          => 'global',
		'active'         => false,
		'priority'       => 10,
		'network'        => null,
		'shared_network' => null,
		'created'        => null,
		'modified'       => null,
	);

	/**
	 * List of field aliases
	 * @var array
	 */
	private static $field_aliases = array(
		'description' => 'desc',
	);

	/**
	 * Constructor function
	 *
	 * @param array|object $fields Initial snippet fields
	 */
	public function __construct( $fields = null ) {
		$this->set_fields( $fields );
	}

	/**
	 * Set all of the snippet fields from an array or object.
	 * Invalid fields will be ignored
	 *
	 * @param array|object $fields List of fields
	 */
	public function set_fields( $fields ) {

		/* Only accept arrays or objects */
		if ( ! $fields || is_string( $fields ) ) {
			return;
		}

		/* Convert objects into arrays */
		if ( is_object( $fields ) ) {
			$fields = get_object_vars( $fields );
		}

		/* Loop through the passed fields and set them */
		foreach ( $fields as $field => $value ) {
			$this->set_field( $field, $value );
		}
	}

	/**
	 * Retrieve all snippet fields
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Internal function for validating the name of a field
	 *
	 * @param string $field A field name
	 *
	 * @return string The validated field name
	 */
	private function validate_field_name( $field ) {

		/* If a field alias is set, remap it to the valid field name */
		if ( isset( self::$field_aliases[ $field ] ) ) {
			return self::$field_aliases[ $field ];
		}

		return $field;
	}

	/**
	 * Check if a field is set
	 *
	 * @param string $field The field name
	 *
	 * @return bool Whether the field is set
	 */
	public function __isset( $field ) {
		$field = $this->validate_field_name( $field );

		return isset( $this->fields[ $field ] ) || method_exists( $this, 'get_' . $field );
	}

	/**
	 * Retrieve a field's value
	 *
	 * @param string $field The field name
	 *
	 * @return mixed The field value
	 */
	public function __get( $field ) {
		$field = $this->validate_field_name( $field );

		if ( method_exists( $this, 'get_' . $field ) ) {
			return call_user_func( array( $this, 'get_' . $field ) );
		}

		return $this->fields[ $field ];
	}

	/**
	 * Set the value of a field
	 *
	 * @param string $field The field name
	 * @param mixed  $value The field value
	 */
	public function __set( $field, $value ) {
		$field = $this->validate_field_name( $field );

		if ( ! $this->is_allowed_field( $field ) ) {

			if ( WP_DEBUG ) {
				trigger_error( 'Trying to set invalid property on Snippets class: ' . esc_html( $field ), E_WARNING );
			}

			return;
		}

		/* Check if the field value should be filtered */
		if ( method_exists( $this, 'prepare_' . $field ) ) {
			$value = call_user_func( array( $this, 'prepare_' . $field ), $value );
		}

		$this->fields[ $field ] = $value;
	}

	/**
	 * Retrieve the list of fields allowed to be written to
	 *
	 * @return array
	 */
	public function get_allowed_fields() {
		return array_keys( $this->fields ) + array_keys( self::$field_aliases );
	}

	/**
	 * Determine whether a field is allowed to be written to
	 *
	 * @param string $field The field name
	 *
	 * @return bool true if the is allowed, false if invalid
	 */
	public function is_allowed_field( $field ) {
		return array_key_exists( $field, $this->fields ) || array_key_exists( $field, self::$field_aliases );
	}

	/**
	 * Safely set the value for a field.
	 * If the field name is invalid, false will be returned instead of an error thrown
	 *
	 * @param string $field The field name
	 * @param mixed  $value The field value
	 *
	 * @return bool true if the field was set successfully, false if the field name is invalid
	 */
	public function set_field( $field, $value ) {
		if ( ! $this->is_allowed_field( $field ) ) {
			return false;
		}

		$this->__set( $field, $value );

		return true;
	}

	/**
	 * Prepare the ID by ensuring it is an absolute integer
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	private function prepare_id( $id ) {
		return absint( $id );
	}

	/**
	 * Prepare the code by removing php tags from beginning and end
	 *
	 * @param string $code
	 *
	 * @return string
	 */
	private function prepare_code( $code ) {

		/* Remove <?php and <? from beginning of snippet */
		$code = preg_replace( '|^[\s]*<\?(php)?|', '', $code );

		/* Remove ?> from end of snippet */
		$code = preg_replace( '|\?>[\s]*$|', '', $code );

		return $code;
	}

	/**
	 * Prepare the scope by ensuring that it is a valid choice
	 *
	 * @param int|string $scope The field as provided
	 *
	 * @return string The field in the correct format
	 */
	private function prepare_scope( $scope ) {
		$scopes = self::get_all_scopes();

		if ( in_array( $scope, $scopes, true ) ) {
			return $scope;
		}

		if ( is_numeric( $scope ) && isset( $scopes[ $scope ] ) ) {
			return $scopes[ $scope ];
		}

		return $this->fields['scope'];
	}

	/**
	 * Prepare the snippet tags by ensuring they are in the correct format
	 *
	 * @param string|array $tags The tags as provided
	 *
	 * @return array The tags as an array
	 */
	private function prepare_tags( $tags ) {
		return code_snippets_build_tags_array( $tags );
	}

	/**
	 * Prepare the active field by ensuring it is the correct type
	 *
	 * @param bool|int $active The field as provided
	 *
	 * @return bool The field in the correct format
	 */
	private function prepare_active( $active ) {

		if ( is_bool( $active ) ) {
			return $active;
		}

		return $active ? true : false;
	}

	/**
	 * Prepare the priority field by ensuring it is an integer
	 *
	 * @param int $priority
	 *
	 * @return int
	 */
	private function prepare_priority( $priority ) {
		return intval( $priority );
	}

	/**
	 * If $network is anything other than true, set it to false
	 *
	 * @param bool $network The provided field
	 *
	 * @return bool The filtered field
	 */
	private function prepare_network( $network ) {

		if ( null === $network && function_exists( 'is_network_admin' ) ) {
			return is_network_admin();
		}

		return true === $network;
	}

	/**
	 * Prepare the modification field by ensuring it is in the correct format.
	 *
	 * @param DateTime|string $modified
	 *
	 * @return string
	 */
	private function prepare_modified( $modified ) {

		/* if the supplied value is a DateTime object, convert it to string representation */
		if ( $modified instanceof DateTime ) {
			return $modified->format( self::DATE_FORMAT );
		}

		/* if the supplied value is probably a timestamp, attempt to convert it to a string */
		if ( is_numeric( $modified ) ) {
			return gmdate( self::DATE_FORMAT, $modified );
		}

		/* if the supplied value is a string, check it is not just the default value */
		if ( is_string( $modified ) && self::DEFAULT_DATE !== $modified ) {
			return $modified;
		}

		/* otherwise, discard the supplied value */
		return null;
	}

	/**
	 * Update the last modification date to the current date and time.
	 */
	public function update_modified() {
		$this->modified = gmdate( Code_Snippet::DATE_FORMAT );
	}

	/**
	 * Retrieve the tags in list format
	 * @return string The tags separated by a comma and a space
	 */
	private function get_tags_list() {
		return implode( ', ', $this->fields['tags'] );
	}

	/**
	 * Retrieve a list of all available scopes
	 * @return array
	 */
	public static function get_all_scopes() {
		return array( 'global', 'admin', 'front-end', 'single-use' );
	}

	/**
	 * Retrieve a list of all scope icons
	 * @return array
	 */
	public static function get_scope_icons() {
		return array(
			'global'     => 'admin-site',
			'admin'      => 'admin-tools',
			'front-end'  => 'admin-appearance',
			'single-use' => 'clock',
		);
	}

	/**
	 * Retrieve the string representation of the scope
	 * @return string The name of the scope
	 */
	private function get_scope_name() {
		return $this->scope;
	}

	/**
	 * Retrieve the icon used for the current scope
	 * @return string a dashicon name
	 */
	private function get_scope_icon() {
		$icons = self::get_scope_icons();

		return $icons[ $this->scope ];
	}

	/**
	 * Determine if the snippet is a shared network snippet
	 * @return bool
	 */
	private function get_shared_network() {

		if ( isset( $this->fields['shared_network'] ) ) {
			return $this->fields['shared_network'];
		}

		if ( ! is_multisite() || ! $this->fields['network'] ) {
			$this->fields['shared_network'] = false;
		} else {
			$shared_network_snippets = get_site_option( 'shared_network_snippets', array() );
			$this->fields['shared_network'] = in_array( $this->fields['id'], $shared_network_snippets, true );
		}

		return $this->fields['shared_network'];
	}

	/**
	 * Retrieve the snippet modification date as a timestamp.
	 *
	 * @return int Timestamp value.
	 */
	private function get_modified_timestamp() {
		$datetime = DateTime::createFromFormat( self::DATE_FORMAT, $this->modified, new DateTimeZone( 'UTC' ) );
		return $datetime ? $datetime->getTimestamp() : 0;
	}

	/**
	 * Retrieve the modification time in the local timezone.
	 *
	 * @return DateTime
	 */
	private function get_modified_local() {

		if ( function_exists( 'wp_timezone' ) ) {
			$timezone = wp_timezone();
		} else {
			$timezone = get_option( 'timezone_string' );

			/* calculate the timezone manually if it is not available */
			if ( ! $timezone ) {
				$offset = (float) get_option( 'gmt_offset' );
				$hours = (int) $offset;
				$minutes = ( $offset - $hours ) * 60;

				$sign = ( $offset < 0 ) ? '-' : '+';
				$timezone = sprintf( '%s%02d:%02d', $sign, abs( $hours ), abs( $minutes ) );
			}

			$timezone = new DateTimeZone( $timezone );
		}

		$datetime = DateTime::createFromFormat( self::DATE_FORMAT, $this->modified, new DateTimeZone( 'UTC' ) );
		$datetime->setTimezone( $timezone );
		return $datetime;
	}
}
