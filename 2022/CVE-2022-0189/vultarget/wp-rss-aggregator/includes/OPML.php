<?php

/**
 * The WPRSS_OPML Class represents an OPML document.
 * It is constructed using a file, which it parses upon creation.
 *
 * @since 3.3
 * @package WPRSSAggregator
 */
class WPRSS_OPML {


	public $file;
	public $head;
	public $body;
	
	/**
	 * Constructor: Parses the given XML file as an OPML
	 *
	 * @since 3.3
	 * @param $file The file to be parsed into a WPRSS_OPML object
	 */
	public function __construct( $file ) {
		// Initialize Properties
		$this->file = $file;
		$this->head = array();
		$this->body = array();
		// Begin Parsing
		try {
			// Wrap file data in a SimpleXMLElement
			$xml = simplexml_load_file( $this->file );
			
			// Check if the head section exists before parsing it
			if ( $xml->head )
				$this->head = WPRSS_OPML::parse_opml_head( $xml->head->children() );
			// Check if the body section exists before parsing it
			if ( $xml->body )
				$this->body = WPRSS_OPML::parse_opml_body_element( $xml->body->outline );

		} catch (Exception $e) {
			// If an exception is caught. Throw an error message
			throw new Exception(
			    __('An error occurred: The file might not be a valid OPML file or is corrupt. ', 'wprss'),
                1
            );
		}
	}


	/**
	 * Parses the given variable as an OPML head section
	 *
	 * @since 3.3
	 * @param $head The head section of an OPML file ( SimpleXMLElement )
	 * @return array An associative array containing SimpleXMLElements for each tag in the given head section.
	 */
	private static function parse_opml_head( $head ) {
		$parsed_data = array();

		foreach ( $head as $property ) {
			$parsed_data[ $property->getName() ] = $property;
		}

		return $parsed_data;
	}


	/**
	 * Iterates through the elements given OPML/XML data and parses
	 * each element, and recurses where necessary, into an associative
	 * array that mimics OPML structure.
	 * @since 1.0
	 * @return array The Parsed OPML as an associative array
	 */
	private static function parse_opml_body_element( $element ) {
		$parsed_data = array();

		// Iterates through each child element in the given element
		foreach ( $element as $child_element ) {

			// If child element has type attribute set to 'rss' or has the 'xmlUrl' attribute
			if ( $child_element['type'] === 'rss' || isset( $child_element['xmlUrl'] ) ) {
				// Parse the child element and add it to the parsed data array
				$parsed_data[] = WPRSS_OPML::SimpleXMLElement_to_OPMLElement( $child_element );
			}
			// Otherwise, if the child element has children 'outline' elements
			elseif ( $child_element->outline ) {
				// Recurse using this child element as parameter, and add the result to the parsed data array
				$parsed_data[ (string) $child_element['text'] ] = WPRSS_OPML::parse_opml_body_element( $child_element->outline );
			}

		}

		return $parsed_data;
	}


	/**
	 * Parses a SimpleXMLElement Object into an OPML element compliant
	 * associative array.
	 * @since 1.0
	 * @return array An array in OPML element format
	 */
	private static function SimpleXMLElement_to_OPMLElement( $element ) {
		// Copy attribute data from $element to a new associative array
		$result = array(
			'text' => (string) $element['text'],
			'title' => (string) $element['title'],
			'htmlUrl' => (string) $element['htmlUrl'],
			'xmlUrl' => (string) $element['xmlUrl'],
			'description' => (string) $element['description']
		);

		// Check for category attribute
		if ( isset( $element['category'] ) ) {
			// split categories by comma, and trim each category string
			$result['categories'] = array_map( 'trim', explode(',', $element['category']) );
		}
		
		/*
		 * Check for existence of htmlUrl and xmlUrl.
		 * If not found, use lowercased attribute names.
		 */
		if ( !isset( $element['htmlUrl'] ) )
			$result['htmlUrl'] = $element['htmlurl'];
		if ( !isset( $element['xmlUrl'] ) )
			$result['xmlUrl'] = $element['xmlurl'];

		// Return the result OPML Element
		return $result;
	}

}

?>
