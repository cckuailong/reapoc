<?php
/**
 * View class
 * Loads views
 */

namespace underDEV\Utils;

class View {

	/**
	 * Files class
	 * @var object
	 */
	private $files;

	/**
	 * Views dir name
	 * @var string
	 */
	private $views_dir;

	/**
	 * View vars
	 * @var array
	 */
	private $vars = array();

	/**
	 * Locate template
	 * If this is a string the templates will be loaded
	 * from this dir in theme first
	 * @var boolean
	 */
	private $locate_template;

	/**
	 * Class constructor
	 * @param Files $files Utils\Files instance
	 */
	public function __construct( Files $files, $views_dir = 'views', $locate_template = false ) {

		$this->files           = $files;
		$this->views_dir       = $views_dir;
		$this->locate_template = $locate_template;

	}

	/**
	 * Alters the views directory
	 * @param string $dir directory name
	 * @return this
	 */
	public function set_views_dir( $dir ) {

		$this->views_dir = $dir;

		return $this;

	}

	/**
	 * Sets var
	 * @param  string $var_name  var slug
	 * @param  mixed  $var_value var value
	 * @param  bool   $override  override var if it already exists
	 * @return this
	 */
	public function set_var( $var_name = null, $var_value = null, $override = false ) {

		if ( $var_name === null ) {
			return $this;
		}

		if ( ! $override && $this->get_var( $var_name ) !== null ) {
			trigger_error( 'Variable ' . $var_name . ' already exists, skipping', E_USER_NOTICE );
			return $this;
		}

		$this->vars[ $var_name ] = $var_value;

		return $this;

	}

	/**
	 * Sets many vars at once
	 * @param array $vars array of vars in format: var name => var value
	 * @return $this
	 */
	public function set_vars( $vars ) {

		if ( ! is_array( $vars ) ) {
			trigger_error( 'Variables to set should be in an array', E_USER_NOTICE );
			return $this;
		}

		foreach ( $vars as $var_name => $var_value ) {
			$this->set_var( $var_name, $var_value );
		}

		return $this;

	}

	/**
	 * Gets the var
	 * @param  string $var_name var name
	 * @return mixed            var value or null
	 */
	public function get_var( $var_name ) {

		return isset( $this->vars[ $var_name ] ) ? $this->vars[ $var_name ] : null;

	}

	/**
	 * Removes var
	 * @param  string $var_name var name
	 * @return this
	 */
	public function remove_var( $var_name ) {

		if ( isset( $this->vars[ $var_name ] ) ) {
			unset( $this->vars[ $var_name ] );
		}

		return $this;

	}

	/**
	 * Gets view file and includes it
	 * @param  string $part file with
	 * @return this
	 */
	public function get_view( $part ) {

		include( $this->get_view_path( $part ) );

		return $this;

	}

	/**
	 * Gets view file full path
	 * @param  string $part file with
	 * @return string
	 */
	public function get_view_path( $part ) {

		// try to locate the template in theme
		if ( $this->locate_template ) {
			$file_path = locate_template( $this->locate_template . '/' . $part . '.php', false, false );
		} else {
			$file_path = '';
		}

		// template not located, load from the plugin
		if ( $file_path == '' ) {

			$file_path = $this->files->file_path( array(
				$this->views_dir,
				$part . '.php'
			) );

		}

		return $file_path;

	}

}
