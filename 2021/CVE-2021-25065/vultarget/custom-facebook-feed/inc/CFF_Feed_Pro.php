<?php
/**
 * Class CFF_Feed_Pro
 */

namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Feed_Pro{
	/**
	 * @var string
	 */
	private $regular_feed_transient_name;

	/**
	 * @var array
	 */
	private $post_data;

	/**
	 * @var array
	 */
	private $next_pages;

	/**
	 * @var bool
	 */
	private $should_paginate;

	/**
	 * @var int
	 */
	private $num_api_calls;

	/**
	 * @var bool
	 */
	private $should_use_backup;

	/**
	 * @var array
	 */
	private $report;

	private $resized_images;

	private $is_customizer;

	protected $one_post_found;

	public function __construct( $transient_name, $is_customizer = false ) {
		$this->regular_feed_transient_name = $transient_name;

		$this->post_data = array();
		$this->next_pages = array();
		$this->should_paginate = true;

		// this is a count of how many api calls have been made for each feed
		// type and term.
		// By default the limit is 10
		$this->num_api_calls = 0;
		$this->max_api_calls = 10;
		$this->should_use_backup = false;

		// used for errors and the sbi_debug report
		$this->report = array();

		$this->resized_images = array();

		$this->one_post_found = false;
		$this->is_customizer = $is_customizer;
	}

	public function get_post_data() {
		return $this->post_data;
	}

	public function set_post_data( $post_data ) {
		$this->post_data = $post_data;
	}

	public function get_next_pages() {
		return $this->next_pages;
	}

	public function need_posts( $num, $offset = 0 ) {
		$num_existing_posts = is_array( $this->post_data ) ? count( $this->post_data ) : 0;
		$num_needed_for_page = (int)$num + (int)$offset;

		($num_existing_posts < $num_needed_for_page) ? $this->add_report( 'need more posts' ) : $this->add_report( 'have enough posts' );

		return ($num_existing_posts < $num_needed_for_page);
	}

	public function can_get_more_posts() {
		$one_type_and_term_has_more_ages = $this->next_pages !== false;
		$max_concurrent_api_calls_not_met = $this->num_api_calls < $this->max_api_calls;
		$max_concurrent_api_calls_not_met ? $this->add_report( 'max conccurrent requests not met' ) : $this->add_report( 'max concurrent met' );
		$one_type_and_term_has_more_ages ? $this->add_report( 'more pages available' ) : $this->add_report( 'no next page' );

		return ($one_type_and_term_has_more_ages && $max_concurrent_api_calls_not_met);
	}

	public function add_remote_posts( $settings ) {
		$new_post_sets = array();
		$next_pages = $this->next_pages;

		$settings['include_extras'] = true;

		$one_post_found = false;
		$next_page_found = false;

		if ( ! empty( $next_pages ) && $next_pages !== '{}' ) {

			$next_pages = json_decode( str_replace( array( '\"', '&quot;' ), '"', $next_pages ), true );
			$new_post_sets = CFF_Shortcode::cff_get_json_data( $settings, $next_pages, '', $this->is_customizer );

		} else {
			$new_post_sets = CFF_Shortcode::cff_get_json_data( $settings, null, '', $this->is_customizer );
		}

		$reporter = CFF_Utils::cff_is_pro_version() ? \cff_main_pro()->cff_error_reporter : \cff_main()->cff_error_reporter;
		if ( ! $reporter->are_critical_errors()
		     && isset( $settings['sources'] )
		     && is_array( $settings['sources'] ) ) {
			foreach ( $settings['sources'] as $source ) {
				if ( ! empty( $source['error'] ) ) {
					\CustomFacebookFeed\Builder\CFF_Source::clear_error( $source['account_id'] );
				}
			}
		}

		if ( ! empty( $new_post_sets ) ) {
			$next_pages = CFF_Shortcode::cff_get_next_url_parts( $new_post_sets );
			if ( ! empty( $next_pages ) && $next_pages !== '{}' ) {
				$next_page_found = true;
			}
		}

		$posts = $this->merge_posts( $new_post_sets, $settings );

		$posts = $this->filter_posts( $posts, $settings );


		if ( isset( $posts[0] ) ) {
			$one_post_found = true;
		} else {
			$next_page_found = false;
		}

		if ( ! empty( $this->post_data ) && is_array( $this->post_data ) ) {
			$posts = array_merge( $this->post_data, $posts );
		} elseif ( $one_post_found ) {
			$this->one_post_found = true;
		}

		$this->post_data = $posts;



		if ( isset( $next_page_found ) && $next_page_found ) {
			$this->next_pages = $next_pages;
		} else {
			$this->next_pages = false;
		}
	}

	public function set_next_pages( $next_pages ) {
		$this->next_pages = $next_pages;
	}

	private function merge_posts( $post_sets, $settings ) {
		$merged_posts = array();
		$settings['sortby'] = isset( $settings['sortby'] ) ? $settings['sortby'] : 'date';

		$i = 0;
		foreach ( $post_sets as $post_set ) {
			$post_data = [];
			if ( isset( $post_set->data ) ) {
				$post_data = $post_set->data;
			} elseif ( isset( $post_set ) ) {
				$post_data = [$post_set];
			}
			if ( isset( $post_data[ $i ] )
			     && (isset( $post_data[ $i ]->id ) || isset( $post_data[ $i ]->created_time) ) ) {
				$merged_posts = array_merge( $merged_posts, $post_data );
			}
			$i ++;
		}


		return $merged_posts;
	}

	public function should_use_pagination( $settings, $offset = 0 ) {
		if ( $settings['minnum'] < 1 ) {
			return false;
		}
		$posts_available = count( $this->post_data ) - ($offset + $settings['num']);
		$show_loadmore_button_by_settings = ($settings['showbutton'] == 'on' || $settings['showbutton'] == 'true' || $settings['showbutton'] == true ) && $settings['showbutton'] !== 'false';

		if ( $show_loadmore_button_by_settings ) {
			if ( $posts_available > 0 ) {
				$this->add_report( 'do pagination, posts available' );
				return true;
			}
			$pages = $this->next_pages;

			if ( $pages && ! $this->should_use_backup() ) {
				foreach ( $pages as $page ) {
					if ( ! empty( $page ) ) {
						return true;
					}
				}
			}

		}

		$this->add_report( 'no pagination, no posts available' );

		return false;
	}

	public function add_report( $to_add ) {
		$this->report[] = $to_add;
	}

	public function get_report() {
		return $this->report;
	}

	protected function filter_posts( $post_set, $settings = array() ) {

		if ( isset( $settings['filter'] ) ) {
			$settings['includewords'] = $settings['filter'];
		}

		if ( isset( $settings['exfilter'] ) ) {
			$settings['excludewords'] = $settings['exfilter'];
		}

		if ( empty( $settings['includewords'] )
		     && empty( $settings['excludewords'] )
		     && empty( $settings['whitelist'] )
		     && empty( $settings['hidephotos'] ) ) {
			return $post_set;
		}

		$includewords = ! empty( $settings['includewords'] ) ? explode( ',', $settings['includewords'] ) : array();
		$excludewords = ! empty( $settings['excludewords'] ) ? explode( ',', $settings['excludewords'] ) : array();
		$hide_photos = ! empty( $settings['hidephotos'] ) && empty( $settings['doingModerationMode'] ) ? explode( ',', str_replace( ' ', '', $settings['hidephotos'] ) ) : array();
		$white_list = false;
		$media_filter = false;

		$filtered_posts = array();
		foreach ( $post_set as $post ) {
			$keep_post = false;
			$caption = CFF_Parse::get_message( $post );

			$padded_caption = ' ' . str_replace( array( '+', '%0A' ), ' ',  urlencode( str_replace( array( '#', '@' ), array( ' HASHTAG', ' MENTION' ), strtolower( $caption ) ) ) ) . ' ';
			$id = CFF_Parse::get_post_id( $post );

			$is_hidden = false;
			$passes_media_filter = true;
			if ( ! empty( $hide_photos )
			     && (in_array( $id, $hide_photos, true ) || in_array( 'cff_' . $id, $hide_photos, true )) ) {
				$is_hidden = true;
				if ( $white_list ) {
					if ( in_array( $id, $white_list, true ) || in_array( 'cff_' . $id, $white_list, true ) ) {
						$is_hidden = false;
					}
				}
			}

			if ( $media_filter ) {
				$media_type = '';
				if ( $media_filter === 'videos' ) {
					if ( $media_type !== 'video' ) {
						$passes_media_filter = false;
					}
				} else {
					if ( $media_type === 'video' ) {
						$passes_media_filter = false;
					}
				}
			}

			// any blocked photos will not pass any additional filters so don't bother processing
			if ( ! $is_hidden && $passes_media_filter ) {
				$is_on_white_list = false;
				$has_includeword = false;
				$has_excludeword = false;
				$passes_word_filter = false;

				if ( $white_list ) {
					if ( in_array( $id, $white_list, true ) || in_array( 'cff_' . $id, $white_list, true ) ) {
						$is_on_white_list = true;
					}
				} elseif ( ! empty( $includewords ) || ! empty( $excludewords ) ) {
					if ( ! empty( $includewords ) ) {
						foreach ( $includewords as $includeword ) {
							if ( ! empty( $includeword ) ) {
								$converted_includeword = trim( str_replace( '+', ' ', urlencode( str_replace( array( '#', '@' ), array( ' HASHTAG', ' MENTION' ), strtolower( $includeword ) ) ) ) );

								if ( preg_match( '/\b' . $converted_includeword . '\b/i', $padded_caption, $matches ) ) {
									$has_includeword = true;
								}
							}
						}
					}

					if ( ! empty( $excludewords ) ) {
						foreach ( $excludewords as $excludeword ) {
							if ( ! empty( $excludeword ) ) {
								$converted_excludeword = trim( str_replace('+', ' ', urlencode( str_replace( array( '#', '@' ), array( ' HASHTAG', ' MENTION' ), strtolower( $excludeword ) ) ) ) );
								if ( preg_match('/\b'.$converted_excludeword.'\b/i', $padded_caption, $matches ) ) {
									$has_excludeword = true;
								}
							}
						}
					}
					if ( ! empty( $excludewords ) && ! empty( $includewords ) ) {
						$passes_word_filter = $has_includeword && ! $has_excludeword;
					} elseif ( ! empty( $includewords ) ) {
						$passes_word_filter = $has_includeword;
					} else {
						$passes_word_filter = !$has_excludeword;
					}

				} else {
					// no other filters so it belongs in the feed
					$keep_post = true;
				}

				if ( $is_on_white_list || $passes_word_filter ) {
					$keep_post = true;
				}
			}

			$keep_post = apply_filters( 'cff_passes_filter', $keep_post, $post, $settings );
			if ( $keep_post ) {
				$filtered_posts[] = $post;
			}

		}

		return $filtered_posts;
	}

	protected function handle_no_posts_found( $settings = array(), $feed_types_and_terms = array() ) {

	}

	protected function remove_duplicate_posts() {
		$posts = $this->post_data;
		$ids_in_feed = array();
		$non_duplicate_posts = array();
		$removed = array();

		foreach ( $posts as $post ) {
			$post_id = CFF_Parse::get_post_id( $post );
			if ( ! in_array( $post_id, $ids_in_feed, true ) ) {
				$ids_in_feed[] = $post_id;
				$non_duplicate_posts[] = $post;
			} else {
				$removed[] = $post_id;
			}
		}

		$this->add_report( 'removed duplicates: ' . implode(', ', $removed ) );
		$this->set_post_data( $non_duplicate_posts );
	}

}