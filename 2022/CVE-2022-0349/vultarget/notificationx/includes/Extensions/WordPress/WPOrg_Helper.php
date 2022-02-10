<?php

namespace NotificationX\Extensions\WordPress;

class WPOrg_Helper {

    public $plugin_information;
    public $theme_information;

    protected function get_links( $html, $strip_tags = false ) {

		$links = array();

		$doc       = new \DOMDocument();
		$doc->loadHTML( $html );
		$linkTags  = $doc->getElementsByTagName( 'a' );

		foreach ( $linkTags as $tag ) {
			if ( $strip_tags ) {
				$links[] = trim( strip_tags( $tag->ownerDocument->saveXML( $tag ) ) );
			} else {
				$links[] = $tag->ownerDocument->saveXML( $tag );
			}
		}

		return $links;

    }
    
    protected function get_link_href( $html ) {

		$doc       = new \DOMDocument();
		$doc->loadHTML( $html );
		$linkhrefs = array();
		$linkTags  = $doc->getElementsByTagName( 'a' );

		foreach ( $linkTags as $tag ) {
			$linkhrefs[] = $tag->getAttribute( 'href' );
		}

		if ( ! empty( $linkhrefs ) ) {
			return $linkhrefs[0];
		} else{
			return '';
		}

    }
    
	protected function get_image_src( $html ) {

		$doc        = new \DOMDocument();
		$doc->loadHTML( $html );
		$imagepaths = array();
		$imageTags  = $doc->getElementsByTagName( 'img' );

		foreach ( $imageTags as $tag ) {
			$imagepaths[] = $tag->getAttribute( 'src' );
		}

		if ( ! empty( $imagepaths ) ) {
			return $imagepaths[0];
		} else{
			return '';
		}

    }
    
	protected function get_node_content( $html, $class ) {

		$dom     = new \DOMDocument();
		$dom->loadHTML( $html );
		$finder  = new \DomXPath( $dom );
		$nodes   = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
		$content = '';

		foreach ( $nodes as $element ) {
			$content = $element->ownerDocument->saveXML( $element );
		}

		return trim( strip_tags( $content ) );

    }
    
	protected function get_tag_content( $html, $search ) {

		$doc        = new \DOMDocument();
		$doc->loadHTML( $html );
		$titlepaths = array();
		$titleTags  = $doc->getElementsByTagName( $search );

		foreach ( $titleTags as $tag ) {
			$titlepaths[] = $tag->ownerDocument->saveXML( $tag );
		}

		if ( ! empty( $titlepaths ) ) {
			return trim( strip_tags( $titlepaths[0] ) );
		} else{
			return '';
		}

	}

	protected function get_rating_content( $html, $class ) {

		$dom     = new \DOMDocument();
		$dom->loadHTML( $html );
		$finder  = new \DomXPath( $dom );
		$nodes   = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");

		$content = '';
		foreach ( $nodes as $element ) {
			$content = $element->getAttribute('data-rating');
		}

		return trim( strip_tags( $content ) );

	}

    protected function extract_review_data( $review ){
        $data  = array();
		$links = $this->get_links( $review, true );

		$data['username']         = isset( $links[1] ) ? $links[1] : '';
		// $data['username']['text'] = isset( $links[1] ) ? $links[1] : '';
		// $data['username']['href'] = $this->get_link_href( $review );
		$data['avatar']['src']    = $this->get_image_src( $review );
		$data['content']          = iconv("UTF-8", 'ISO-8859-1', $this->get_node_content( $review, 'review-body' ));
		$data['plugin_name']      = $this->plugin_information->name;
		$data['title']            = iconv("UTF-8", 'ISO-8859-1', $this->get_tag_content( $review, 'h4' ));
		$data['timestamp']        = strtotime( iconv("UTF-8", 'ISO-8859-1', $this->get_node_content( $review, 'review-date' )) );
		$data['rating']           = $this->get_rating_content( $review, 'wporg-ratings' );

		return $data;
    }

    public function extract_reviews_from_html( $data, $plugin_slug = '' ){
        $extracted_reviews = array();

		$dom           = new \DOMDocument();
		$slug = $data['slug'];
		$icons = $data['icons'];
        $dom->loadHTML('<?xml encoding="UTF-8">' . $data['reviews']);
        
        $finder        = new \DomXPath( $dom );
        $nodes         = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' review ')]");
		
		$conditioned_filter = apply_filters( 'nx_wp_reviews_rating_condition', 3 );
		
        foreach ( $nodes as $node ) {
            $raw_review = $node->ownerDocument->saveXML( $node );
			$review     = $this->extract_review_data( $raw_review );
			if( isset( $review['rating'] ) && intval( $review['rating'] ) < $conditioned_filter ) {
				continue;
			}
			if( $plugin_slug ) {
				$review['link'] = 'https://wordpress.org/plugins/' . $plugin_slug;
			}
			$review['slug'] = $slug;
			$review['icons'] = $icons;
            $extracted_reviews[] = $review;
		}

		unset( $data['reviews'] );
		if( ! empty( $data ) && ! empty( $extracted_reviews ) ) {
			return array_merge( $data, array( 'reviews' => $extracted_reviews ) );
		}
		return array();
    }

    public function get_plugin_reviews( $plugin_slug ){
        if( ! function_exists('plugins_api') ) {
            require_once ABSPATH . '/wp-admin/includes/plugin-install.php';
        }

		$this->plugin_information = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'reviews' => true, 'icons' => true ) ) );
		
		$data = array();
		$data['slug'] = isset( $this->plugin_information->slug ) ? $this->plugin_information->slug : '';
		$data['icons'] = isset( $this->plugin_information->icons ) ? $this->plugin_information->icons : ''; 
		$data['name'] = isset( $this->plugin_information->name ) ? $this->plugin_information->name : '';
		$data['ratings'] = isset( $this->plugin_information->ratings ) ? $this->plugin_information->ratings : '';
		$data['rated'] = isset( $this->plugin_information->num_ratings ) ? $this->plugin_information->num_ratings : '';
		$data['reviews'] = isset( $this->plugin_information->sections ) ? $this->plugin_information->sections['reviews'] : '';
        return $data;
	}
	
	// TODO: Themes Review ( Upcoming )
	public function get_theme_reviews( $theme_slug ){
        if( ! function_exists('themes_api') ) {
            require_once ABSPATH . '/wp-admin/includes/themes.php';
        }
        return [];
    }

    public function get_plugin_stats( $plugin_slug ){
        if( ! function_exists('plugins_api') ) {
            require_once ABSPATH . '/wp-admin/includes/plugin-install.php';
        }

		$this->plugin_information = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug, 'fields' => array( 'downloaded' => true, 'icons' => true, 'historical_summary' => true, 'active_installs' => true ) ) );
		$new_data = [];

		if( is_wp_error( $this->plugin_information ) ) {
			return $new_data;
		}
		
		$needed_key = array(
			'name', 'slug', 'num_ratings', 'rating', 'homepage', 'version', 'downloaded', 'icons', 'active_installs', 'author_profile', 'author'
		);

		foreach( $needed_key as $key => $value ) {
			if( isset( $this->plugin_information->$value ) ) {
				$new_data[ $value ] = $this->plugin_information->$value;
			}
		}

		if( isset( $new_data['homepage'] ) ) {
			$new_data['link'] = $new_data['homepage'];
			unset( $new_data['homepage'] );
		}

        return $new_data;
	}

    public function get_theme_stats( $theme_slug ){
        if( ! function_exists('themes_api') ) {
            require_once ABSPATH . '/wp-admin/includes/theme.php';
        }

		$this->theme_information = themes_api( 'theme_information', array( 'slug' => $theme_slug, 'fields' => array( 'downloaded' => true, 'sections' => true, 'theme_url' => true, 'photon_screenshots' => true, 'screenshot_url' => true, 'active_installs' => true ) ) );

		$new_data = array();
		if( is_wp_error( $this->theme_information ) ) {
			return $new_data;
		}
		$needed_key = array(
			'name', 'slug', 'num_ratings', 'rating', 'homepage', 'version', 'downloaded', 'screenshot_url', 'active_installs' 
		);

		foreach( $needed_key as $key => $value ) {
			if( isset( $this->theme_information->$value ) ) {
				$new_data[ $value ] = $this->theme_information->$value;
			}
		}

		if( isset( $new_data['homepage'] ) ) {
			$new_data['link'] = $new_data['homepage'];
			unset( $new_data['homepage'] );
		}

		return $new_data;
	}
}