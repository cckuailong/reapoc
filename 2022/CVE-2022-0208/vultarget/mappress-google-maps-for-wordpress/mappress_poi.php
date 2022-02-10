<?php
class Mappress_Poi extends Mappress_Obj {
	var $address,
		$body = '',
		$correctedAddress,
		$iconid,
		$point = array('lat' => 0, 'lng' => 0),
		$poly,
		$postid,
		$props = array(),
		$kml,
		$thumbnail,
		$title = '',
		$type,
		$url,
		$viewport;              // array('sw' => array('lat' => 0, 'lng' => 0), 'ne' => array('lat' => 0, 'lng' => 0))

	function __sleep() {
		return array('address', 'body', 'correctedAddress', 'iconid', 'point', 'poly', 'kml', 'title', 'type', 'viewport');
	}

	function __construct($atts = '') {
		parent::__construct($atts);
	}

	/**
	* Geocode an address using http
	*
	* @param mixed $auto true = automatically update the poi, false = return raw geocoding results
	* @return true if auto=true and success | WP_Error on failure
	*/
	function geocode() {
		if (!Mappress::$pro)
			return new WP_Error('geocode', 'MapPress Pro required for geocoding');

		// If point has a lat/lng then no geocoding
		if (!empty($this->point['lat']) && !empty($this->point['lng'])) {
			$this->viewport = null;
		} else {
			$location = Mappress_Geocoder::geocode($this->address);

			if (is_wp_error($location))
				return $location;

			$this->point = array('lat' => $location->lat, 'lng' => $location->lng);
			$this->address = $location->formatted_address;
			$this->viewport = $location->viewport;
		}

		// Guess a default title / body - use address if available or lat, lng if not
		if (empty($this->title) && empty($this->body)) {
			if ($this->address) {
				$parsed = Mappress_Geocoder::parse_address($this->address);
				$this->title = $parsed[0];
				$this->body = (isset($parsed[1])) ? $parsed[1] : "";
			} else {
				$this->title = $this->point['lat'] . ',' . $this->point['lng'];
			}
		}
	}

	/**
	* Fast excerpt for a poi
	*/
	function get_post_excerpt($post) {
		// Fast excerpts: similar to wp_trim_excerpt() in formatting.php, but without (slow) call to get_the_content()
		$raw = ($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
		$text = strip_shortcodes($raw);
		$excerpt_length = 55;
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$excerpt = wp_trim_words( $text, $excerpt_length, $excerpt_more );
		return apply_filters('mappress_poi_excerpt', $excerpt, $raw);
	}

	/**
	* Get thumbnails if needed.  If dimensions are specified, size 'medium-large' is used to allow scaling w/o pixellation
	*
	* @param mixed $post
	*/
	function get_thumbnail($post) {
		$force_size = (Mappress::$options->thumbWidth && Mappress::$options->thumbHeight);
		$size = (Mappress::$options->thumbSize && !$force_size) ? Mappress::$options->thumbSize : 'medium_large';
		$style = ($force_size) ? sprintf("width: %spx; height : %spx;", Mappress::$options->thumbWidth, Mappress::$options->thumbHeight) : null;
		return get_the_post_thumbnail($post, $size, array('style' => $style));			// Slow due to get_post_thumbnail_id()
	}
}
?>