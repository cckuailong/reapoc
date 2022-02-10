<?php
// Markdown - dependency
if ( !function_exists( 'Markdown' ) )
	require_once( WPRSS_INC . 'libraries/php-markdown/markdown.php' );
// The parser
if ( !class_exists( 'Baikonur_ReadmeParser' ) )
	require_once( WPRSS_INC . 'libraries/WordPress-Readme-Parser/ReadmeParser.php' );


/**
 * Parses a readme file identified by it's path.
 *
 * @since 4.6.10
 * @see Baikonur_ReadmeParser::parse_readme_contents()
 * @param string $file
 * @return \WPRSS_Readme
 */
function wprss_parse_readme( $file = null ) {
	if ( is_null( $file ) )
		$file = WPRSS_DIR . 'readme.txt';
	
	$readme = new WPRSS_Readme( $file );
	
	do_action( 'wprss_parse_readme_before', $readme );
	$readme->load();
	do_action( 'wprss_parse_readme_after', $readme );
	
	return $readme;
}

/**
 * Parses a readme file, and returns it's changelog.
 * 
 * @since 4.4
 * @see Baikonur_ReadmeParser::parse_readme_contents()
 * @return array An array containing version changelogs
 */
function wprss_parse_changelog( $file = null ) {
	if ( is_null( $file ) )
		$file = WPRSS_DIR . 'readme.txt';
	
	$file = apply_filters( 'wprss_parse_changelog_before', $file );
	$readme = wprss_parse_readme( $file );
	$changelog = apply_filters( 'wprss_parse_changelog_after', $readme->get_changelog() );
	
	return $changelog;
}


/**
 * Encapsulates data of a single WordPress readme file.
 */
class WPRSS_Readme {

	protected $_parser;
	protected $_readme;
	protected $_file_path;

	public function __construct( $data = null ) {
		$args = func_get_args();
		
		$data = isset( $args[0] ) ? array_shift( $args ) : null;
		if ( is_string( $data ) )
			$this->set_file_path( $data );
		
		$this->_construct();
	}
	
	
	protected function _construct() {
	}
	
	
	/**
	 * @param string $string The readme contents to parse
	 * @return string The parsed data
	 */
	public function parse_string( $string ) {
		$parser = $this->get_parser();
		$string = $this->_before_parse( $string );
		$result = $parser->parse( $string );
		$result = $this->_after_parse( $result );
		
		return $result;
	}


	/**
	 * @param string $file Path to readme file
	 * @return \WPRSS_Readme
	 */
	public function parse( $file ) {
		if ( !is_readable( $file ) )
			throw new Exception ( sprintf( 'Could not parse readme: file "%1$s" does not exist or is not readable' ) );
		
		$contents = file_get_contents( $file );
		$result = $this->parse_string( $contents );
		
		return $result;
	}
	
	
	protected function _before_parse( $string ) {
		return $string;
	}


	protected function _after_parse( $string ) {
		return $string;
	}
	
	
	/**
	 * Loads the contents of the specified file into the instance.
	 * Will not load if already loaded.
	 * 
	 * @param string $file Path to the file to load from
	 * @return \WPRSS_Readme This instance
	 * @throws Exception If no path is provided
	 */
	public function load( $file = null ) {
		if ( !empty( $this->_readme ) )
			return $this;
		
		if ( is_null( $file ) )
			$file = $this->get_file_path();
		
		if ( !$file )
			throw new Exception ( 'Could not load readme: file path must be provided' );
		
		$file = $this->_before_load( $file );
		$this->set_file_path( $file );
		$this->_readme = $this->parse( $file );
		$this->_after_load();
		
		return $this;
	}
	
	
	protected function _before_load( $file ) {
		return $file;
	}
	
	
	protected function _after_load() {
		return $this;
	}
	
	
	/**
	 * Sets the path to the readme file.
	 * Cannot be set again once loaded.
	 * 
	 * @param string $path The path of the file to set.
	 * @return \WPRSS_Readme This instance.
	 */
	public function set_file_path( $path ) {
		if ( !empty( $this->_readme ) )
			return $this;
		
		$this->_file_path = $path;
		return $this;
	}
	
	
	public function get_file_path() {
		return $this->_file_path;
	}
	
	
	public function has_data( $key = null ) {
		$this->load();
		
		$is_empty = empty( $this->_readme );
		if ( is_null( $key ) )
			return !$is_empty;
		
		return !$is_empty && isset( $this->_readme->{$key} );
	}
	
	
	public function get_data( $key = null, $default = null ) {
		$this->load();
		if ( is_null( $key ) )
			return $this->_readme;
		return $this->has_data( $key ) ? $this->_readme->{$key} : $default;
	}
	
	
	public function get_changelog() {
		$changelog = $this->get_data( 'changelog' );
		
		return $changelog;
	}


	/**
	 * @return WPRSS_Readme_Parser
	 */
	public function get_parser() {
		if ( is_null( $this->_parser ) ) {
			$this->_parser = new WPRSS_Readme_Parser();
			$this->_prepare_parser( $this->_parser );
		}
		
		return $this->_parser;
	}
	
	
	/**
	 * @param WPRSS_Readme_Parser $parser
	 * @return WPRSS_Readme_Parser
	 */
	protected function _prepare_parser( $parser ) {
		return $parser;
	}

}


/**
 * Padding layer for the ReadmeParser library.
 */
class WPRSS_Readme_Parser extends Baikonur_ReadmeParser {
	
	public function __construct( $data = null ) {
		// Implementing common mechanism
		$this->_construct();
	}
	
	
	protected function _construct() {
		// Parameterless initialization
	}
	
	
	/**
	 * @param string $string
	 * @return array
	 */
	public function parse( $string ) {
		$string = $this->_before_parse( $string );
		$result = self::parse_readme_contents( $string );
		$result = $this->_after_parse( $result );
		
		return $result;
	}
	
	protected function _before_parse( $string ) {
		return $string;
	}
	
	protected function _after_parse( $string ) {
		return $string;
	}
}