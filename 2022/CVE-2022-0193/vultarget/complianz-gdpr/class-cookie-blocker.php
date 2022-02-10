<?php
defined( 'ABSPATH' ) or die( "you do not have access to this page!" );

if ( ! class_exists( 'cmplz_cookie_blocker' ) ) {
	class cmplz_cookie_blocker {
		private static $_this;
		public $script_tags = array();
		public $iframe_tags = array();

		function __construct() {

			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;

		}

		static function this() {
			return self::$_this;

		}


		/**
		 * Apply the mixed content fixer.
		 *
		 * @since  1.0
		 *
		 * @access public
		 *
		 */

		public function filter_buffer( $buffer ) {
			if ( cmplz_is_amp() ) {
				$buffer = apply_filters( 'cmplz_cookieblocker_amp',  $buffer );
			} else {
				$buffer = $this->replace_tags( $buffer );
			}

			return $buffer;
		}



		/**
		 * Start buffering the output
		 *
		 * @since  1.0
		 *
		 * @access public
		 *
		 */

		public function start_buffer() {
			/**
			 * Don't activate the cookie blocker is AMP is active, but the AMP integration is not enabled
			 * This problem only occurs for manually included iframes, not for WP generated embeds
			 */

			if ( cmplz_is_amp_endpoint() && !cmplz_amp_integration_active() ) {
				return;
			}

			ob_start( array( $this, "filter_buffer" ) );
		}

		/**
		 * Flush the output buffer
		 *
		 * @since  1.0
		 *
		 * @access public
		 *
		 */

		public function end_buffer() {

			/**
			 * Don't activate the cookie blocker is AMP is active, but the AMP integration is not enabled
			 */

			if ( cmplz_is_amp_endpoint() && !cmplz_amp_integration_active() ) {
				return;
			}

			if ( ob_get_length() ) {
				ob_end_flush();
			}
		}

		/**
		 * Just before the page is sent to the visitor's browser, remove all tracked third party scripts
		 *
		 * @since  1.0
		 *
		 * @access public
		 *
		 */


		public function replace_tags( $output ) {
			/**
			 * Get style tags
			 *
			 * */
			$known_style_tags = apply_filters( 'cmplz_known_style_tags', COMPLIANZ::$config->style_tags );

			/**
			 * Get script tags, and add custom user scrripts
			 *
			 * */

			$known_script_tags = COMPLIANZ::$config->script_tags;
			$custom_scripts    = cmplz_strip_spaces( cmplz_get_value( 'thirdparty_scripts' ) );
			if ( ! empty( $custom_scripts ) && strlen( $custom_scripts ) > 0 ) {
				$custom_scripts    = array_filter(explode( ',', $custom_scripts ));
				$known_script_tags = array_merge( $known_script_tags, $custom_scripts );
			}
			$known_script_tags = apply_filters( 'cmplz_known_script_tags', $known_script_tags );

			/**
			 * Get dependencies between scripts
			 * */
			$dependencies = apply_filters( 'cmplz_dependencies', COMPLIANZ::$config->dependencies );

			/**
			 * Get async list tags
			 *
			 * */

			$async_list = apply_filters( 'cmplz_known_async_tags', COMPLIANZ::$config->async_list );

			/**
			 * Get iframe tags, and add custom user iframes
			 *
			 * */

			$known_iframe_tags = COMPLIANZ::$config->iframe_tags;
			$custom_iframes = cmplz_strip_spaces( cmplz_get_value( 'thirdparty_iframes' ) );
			if ( ! empty( $custom_iframes ) && strlen( $custom_iframes ) > 0 ) {
				$custom_iframes    = explode( ',', $custom_iframes );
				$known_iframe_tags = array_merge( $known_iframe_tags, $custom_iframes );
			}

			$known_iframe_tags = apply_filters( 'cmplz_known_iframe_tags', $known_iframe_tags );
			$iframe_tags_not_including = apply_filters( 'cmplz_iframe_tags_not_including', array() );

			//not meant as a "real" URL pattern, just a loose match for URL type strings.
			//edit: instagram uses ;width, so we need to allow ; as well.
			$url_pattern = '([\w.,;ß@?^=%&:()\/~+#!\-*]*?)';

			/**
			 * Handle scripts loaded with dns prefetch
			 *
			 *
			 * */

			/*            $prefetch_pattern = '/<link rel=[\'|"]dns-prefetch[\'|"] href=[\'|"](\X*?)[\'|"][^>]*?>/i';*/
//            if (preg_match_all($prefetch_pattern, $output, $matches, PREG_PATTERN_ORDER)) {
//                foreach($matches[1] as $key => $prefetch_url){
//                    $total_match = $matches[0][$key];
//                    if (cmplz_strpos_arr($prefetch_url, $known_script_tags) !== false) {
//                        $new = $this->replace_href($total_match);
//                        $output = str_replace($total_match, $new, $output);
//                    }
//                }
//            }


			/**
			 * Handle images from third party services, e.g. google maps
			 *
			 *
			 * */

			$image_tags = COMPLIANZ::$config->image_tags;
			$image_tags = apply_filters( 'cmplz_image_tags', $image_tags );
			$image_pattern = '/<img.*?src=[\'|"](\X*?)[\'|"].*?>/s'; //matches multiline with s operater, for FB pixel
			if ( preg_match_all( $image_pattern, $output, $matches, PREG_PATTERN_ORDER )
			) {
				foreach ( $matches[1] as $key => $image_url ) {
					$total_match = $matches[0][ $key ];
					$found = cmplz_strpos_arr( $image_url, $image_tags );
					if ( $found !== false ) {
						$placeholder = cmplz_placeholder( false, $image_url );
						$new = $total_match;
						$new = $this->add_data( $new, 'img', 'src-cmplz', $image_url );
						//remove lazy loading for images, as it is breaking on activation
						$new = str_replace('loading="lazy"', 'data-deferlazy="1"', $new );
						$new = $this->add_class( $new, 'img', apply_filters( 'cmplz_image_class', 'cmplz-image', $total_match, $found ) );
						$new = $this->replace_src( $new, apply_filters( 'cmplz_source_placeholder', $placeholder ) );
						$new = apply_filters('cmplz_image_html', $new, $image_url);

						if ( cmplz_use_placeholder( $image_url ) ) {
							$new = $this->add_class( $new, 'img', " cmplz-placeholder-element " );
							$new = '<div>' . $new . '</div>';
						}
						$output = str_replace( $total_match, $new, $output );
					}
				}
			}

			/**
			 * Handle styles (e.g. google fonts)
			 * fonts.google.com has currently been removed in favor of plugin recommendation
			 *
			 * */
			$style_pattern = '/<link rel=[\'|"]stylesheet[\'|"].*?href=[\'|"](\X*?)[\'|"][^>]*?>/i';
			if ( preg_match_all( $style_pattern, $output, $matches, PREG_PATTERN_ORDER ) ) {
				foreach ( $matches[1] as $key => $style_url ) {
					$total_match = $matches[0][ $key ];
					if ( cmplz_strpos_arr( $style_url, $known_style_tags ) !== false ) {
						$new    = $this->replace_href( $total_match );
						$new    = $this->add_class( $new, 'link', 'cmplz-style-element' );
						$output = str_replace( $total_match, $new, $output );
					}

				}
			}

			/**
			 * Handle iframes from third parties
			 *
			 *
			 * */
			//the iframes URL pattern allows for a space, which may be included in a Google Maps embed.
			$url_pattern_iframes = '([\w.,;@?^=%&:()\/~+#!\- *]*?)';
			$iframe_pattern = '/<(iframe)[^>].*?src=[\'"](http:\/\/|https:\/\/|\/\/)' . $url_pattern_iframes . '[\'"].*?>.*?<\/iframe>/is';
			if ( preg_match_all( $iframe_pattern, $output, $matches, PREG_PATTERN_ORDER ) ) {
				foreach ( $matches[0] as $key => $total_match ) {
					$iframe_src = $matches[2][ $key ] . $matches[3][ $key ];
					if ( cmplz_strpos_arr( $iframe_src, $known_iframe_tags ) !== false ) {
						$is_video = $this->is_video( $iframe_src );
						$placeholder = cmplz_placeholder( false, $iframe_src );
						$service_name = cmplz_get_service_by_src( $iframe_src );
						$new         = $total_match;
						$new         = preg_replace( '~<iframe\\s~i', '<iframe data-src-cmplz="' . $iframe_src . '" ', $new , 1 ); // make sure we replace it only once

						//remove lazy loading for iframes, as it is breaking on activation
						$new = str_replace('loading="lazy"', 'data-deferlazy="1"', $new );

						//check if we can skip blocking this array if a specific string is included
						if ( cmplz_strpos_arr($iframe_src, $iframe_tags_not_including )) continue;

						//we insert video/no-video class for specific video styling
						if ( $is_video ) {
							$video_class = apply_filters( 'cmplz_video_class', 'cmplz-video cmplz-hidden' );
						} else {
							$video_class = apply_filters( 'cmplz_video_class', 'cmplz-no-video' );
						}

						$new = $this->replace_src( $new, apply_filters( 'cmplz_source_placeholder', 'about:blank' ) );
						$new = $this->add_class( $new, 'iframe', "cmplz-iframe cmplz-iframe-styles $video_class " );

						if ( cmplz_use_placeholder( $iframe_src ) ) {
							$new = $this->add_class( $new, 'iframe', " cmplz-placeholder-element " );
							$new = $this->add_data( $new, 'iframe', 'placeholder-image', $placeholder );
							$new = $this->add_data( $new, 'iframe', 'service', $service_name );

							//allow for integrations to override html
							$new = apply_filters( 'cmplz_iframe_html', $new );

							//make sure there is a parent element which contains this iframe only, to attach the placeholder to
							if ( ! $is_video
							     && ! $this->no_div( $iframe_src )
							) {
								$new = '<div>' . $new . '</div>';
							}
						}

						$output = str_replace( $total_match, $new, $output );

					}
				}

			}

			/**
			 * set non iframe placeholders
			 *
			 *
			 * */
			if ( cmplz_use_placeholder() ) {
				$placeholder_markers = apply_filters( 'cmplz_placeholder_markers', COMPLIANZ::$config->placeholder_markers );
				foreach ( $placeholder_markers as $type => $markers ) {
					if ( ! is_array( $markers ) ) {
						$markers = array( $markers );
					}
					foreach ( $markers as $marker ) {
						$placeholder_pattern
							= '/<(a|section|div|blockquote|twitter-widget)*[^>]*class=[\'" ]*[^>]*('
							  . $marker . ')[\'" ].*?>/is';
						if ( preg_match_all( $placeholder_pattern, $output,
							$matches, PREG_PATTERN_ORDER )
						) {
							foreach ( $matches[0] as $key => $html_match ) {
								$el = $matches[1][ $key ];
								if ( ! empty( $el ) ) {
									$placeholder = cmplz_placeholder( $type,
										$marker );
									$new_html    = $this->add_data( $html_match,
										$el, 'placeholder-image',
										$placeholder );
									$new_html    = $this->add_class( $new_html,
										$el,
										'cmplz-noframe cmplz-placeholder-element' );
									$output      = str_replace( $html_match,
										$new_html, $output );
								}
							}
						}
					}
				}
			}


			/**
			 * Handle scripts from third parties
			 *
			 *
			 * */

			$script_pattern = '/(<script.*?>)(\X*?)<\/script>/is';
			$index          = 0;
			if ( preg_match_all( $script_pattern, $output, $matches,
				PREG_PATTERN_ORDER )
			) {
				foreach ( $matches[1] as $key => $script_open ) {
					//we don't block scripts with the cmplz-native class
					if ( strpos( $script_open, 'cmplz-native' ) !== false ) {
						continue;
					}

					//exclude ld+json
					if ( strpos( $script_open, 'application/ld+json' )
					     !== false
					) {
						continue;
					}
					$total_match = $matches[0][ $key ];
					$content     = $matches[2][ $key ];
					//if there is inline script here, it has some content
					if ( ! empty( $content ) ) {

						if ( strpos( $content, 'avia_preview' ) !== false ) {
							continue;
						}
						$found = cmplz_strpos_arr( $content, $known_script_tags );

						if ( $found !== false ) {
							$new = $total_match;
							$new = $this->add_class( $new, 'script', apply_filters( 'cmplz_script_class', 'cmplz-script', $total_match, $found ) );

							//native scripts don't have to be blocked
							if ( strpos( $new, 'cmplz-native' ) === false ) {
								$new = $this->set_javascript_to_plain( $new );

								$waitfor = cmplz_strpos_arr( $content, $dependencies );
								if ( $waitfor !== false ) {
									$new = $this->add_data( $new, 'script', 'waitfor', $waitfor );
								}
							}
							$output = str_replace( $total_match, $new, $output );
						}
					}

					//when script contains src
					$script_src_pattern = '/<script [^>]*?src=[\'"]' . $url_pattern . '[\'"].*?>/is';
					if ( preg_match_all( $script_src_pattern, $total_match, $src_matches, PREG_PATTERN_ORDER )
					) {
						foreach ( $src_matches[1] as $src_key => $script_src ) {
							$script_src = $src_matches[1][ $src_key ];
							$found = cmplz_strpos_arr( $script_src, $known_script_tags );
							if ( $found !== false ) {
								$new = $total_match;
								$new = $this->add_class( $new, 'script', apply_filters( 'cmplz_script_class', 'cmplz-script', $total_match, $found ) );

								//native scripts don't have to be blocked
								if ( strpos( $new, 'cmplz-native' ) === false
								) {
									$new = $this->set_javascript_to_plain( $new );
									if ( cmplz_strpos_arr( $found, $async_list )
									) {
										$index ++;
										$new = $this->add_data( $new, 'script', 'post_scribe_id', 'cmplz-ps-' . $index );
										if ( cmplz_has_async_documentwrite_scripts() ) {
											$new .= '<div class="cmplz-blocked-content-container"><div class="cmplz-blocked-content-notice cmplz-accept-marketing">'
											        . apply_filters( 'cmplz_accept_cookies_blocked_content',
													cmplz_get_value( 'blocked_content_text' ) )
											        . '</div><div id="cmplz-ps-'
											        . $index . '"><img src="'
											        . cmplz_placeholder( 'div' )
											        . '"></div></div>';
										}
									}

									//maybe add dependency
									$waitfor = cmplz_strpos_arr( $script_src, $dependencies );
									if ( $waitfor !== false ) {
										$new = $this->add_data( $new, 'script', 'waitfor', $waitfor );
									}
								}

								$output = str_replace( $total_match, $new, $output );
							}


						}
					}
				}
			}

			//add a marker so we can recognize if this function is active on the front-end
			$output = str_replace( "<body ", '<body data-cmplz=1 ', $output );

			return apply_filters('cmplz_cookie_blocker_output', $output);
		}

		/**
		 * Set the javascript attribute of a script element to plain
		 *
		 * @param string $script
		 *
		 * @return string
		 */

		private function set_javascript_to_plain( $script ) {
			//check if it's already set to plain
			if ( strpos( $script, 'text/plain')!== false ) return $script;

			$pattern = '/<script[^>].*?\K(type=[\'|\"]text\/javascript[\'|\"])(?=.*>)/i';
			preg_match( $pattern, $script, $matches );
			if ( $matches ) {
				$script = preg_replace( $pattern, 'type="text/plain"', $script,
					1 );
			} else {
				$pos = strpos( $script, "<script" );
				if ( $pos !== false ) {
					$script = substr_replace( $script,
						'<script type="text/plain"', $pos,
						strlen( "<script" ) );
				}
			}

			return $script;
		}

		private function remove_src( $script ) {
			$pattern
				    = '/src=[\'"](http:\/\/|https:\/\/)([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?)[\'"]/i';
			$script = preg_replace( $pattern, '', $script );

			return $script;
		}

		/**
		 * replace the src attribute with a placeholder of choice
		 *
		 * @param string $script
		 * @param string $new_src
		 *
		 * @return string
		 */

		private function replace_src( $script, $new_src ) {
			$pattern
				     = '/src=[\'"](http:\/\/|https:\/\/|\/\/)([\s\w.,@!?^=%&:\/~+#-;]*[\w@!?^=%&\/~+#-;]?)[\'"]/i';
			$new_src = ' src="' . $new_src . '" ';
			preg_match( $pattern, $script, $matches );
			$script = preg_replace( $pattern, $new_src, $script );

			return $script;
		}

		/**
		 * replace the href attribute with a data-href attribute
		 *
		 * @param string $link
		 *
		 * @return string
		 */

		private function replace_href( $link ) {
			return str_replace( 'href=', 'href="#" data-href=', $link );
		}

		/**
		 * Add a class to an HTML element
		 *
		 * @param string $html
		 * @param string $el
		 * @param string $class
		 *
		 * @return string
		 */

		public function add_class( $html, $el, $class ) {
			$classes = array_filter( explode(' ', $class) );
			preg_match( '/<' . $el . '[^\>]*[^\>\S]+\K(class=")(.*)"/i', $html, $matches );
			if ( $matches ) {
				foreach ($classes as $class){
					//check if class is already added
					if (strpos($matches[2], $class) === false && strlen(trim($class))>0) {
						$html = preg_replace( '/<' . $el . '[^\>]*[^\>\S]+\K(class=")/i', 'class="' . esc_attr($class) . ' ', $html, 1 );
					}
				}

			} else {
				$pos = strpos( $html, "<$el" );
				if ( $pos !== false ) {
					$html = substr_replace( $html,
						'<' . $el . ' class="' . esc_attr($class) . '"', $pos,
						strlen( "<$el" ) );
				}
			}

			return $html;
		}

		/**
		 * Add a data attribute to an html element
		 *
		 * @param string $html
		 * @param string $el
		 * @param string $id
		 * @param string $content
		 *
		 * @return string $html
		 */

		public function add_data( $html, $el, $id, $content ) {
			$content = esc_attr( $content );
			$id      = esc_attr( $id );

			$pos = strpos( $html, "<$el" );
			if ( $pos !== false ) {
				$html = substr_replace( $html,
					'<' . $el . ' data-' . $id . '="' . $content . '"', $pos,
					strlen( "<$el" ) );
			}

			return $html;
		}

		/**
		 * Check if this iframe source is a video
		 *
		 * @param $iframe_src
		 *
		 * @return bool
		 */

		private function is_video( $iframe_src ) {
			if ( strpos( $iframe_src, 'dailymotion' ) !== false
			     || strpos( $iframe_src, 'youtube' ) !== false
			     || strpos( $iframe_src, 'vimeo' ) !== false
			) {
				return true;
			}

			return false;
		}

		/**
		 * Check if this iframe source is soundcloud
		 *
		 * @param $iframe_src
		 *
		 * @return bool
		 */

		private function no_div( $iframe_src ) {
			if ( strpos( $iframe_src, 'soundcloud' ) !== false ) {
				return true;
			}

			return false;
		}

	}
}



