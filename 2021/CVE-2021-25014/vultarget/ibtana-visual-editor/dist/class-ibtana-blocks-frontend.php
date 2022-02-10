<?php
/**
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Ibtana Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Enqueue CSS/JS of all the blocks.
 *
 * @category class
 */
class Ibtana_Blocks_Frontend {

	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public static $gfonts = array();

	/**
	 * Google schema to add to head
	 *
	 * @var null
	 */
	public static $faq_schema = null;

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'on_init' ), 20 );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'global_inline_css' ), 101 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_inline_css' ), 20 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 90 );
		add_action( 'wp_head', array( $this, 'faq_schema' ), 91 );
		if ( ! is_admin() ) {
			add_action( 'render_block', array( $this, 'conditionally_render_block' ), 6, 2 );
		}
	}
	/**
	 * Check for logged in, logged out visibility settings.
	 *
	 * @param mixed $block_content The block content.
	 * @param array $block The block data.
	 *
	 * @return mixed Returns the block content.
	 */
	public function conditionally_render_block( $block_content, $block ) {
		if ( 'ive/rowlayout' === $block['blockName'] && isset( $block['attrs'] ) ) {
			if ( isset( $block['attrs']['loggedIn'] ) && $block['attrs']['loggedIn'] && is_user_logged_in() ) {
				$hide = true;
				if ( isset( $block['attrs']['loggedInUser'] ) && is_array( $block['attrs']['loggedInUser'] ) && ! empty( $block['attrs']['loggedInUser'] ) ) {
					$user = wp_get_current_user();
					foreach( $block['attrs']['loggedInUser'] as $key => $role ) {
						if ( in_array( $role['value'], (array) $user->roles ) ) {
							return '';
						} else {
							$hide = false;
						}
					}
				}
				if ( isset( $block['attrs']['loggedInShow'] ) && is_array( $block['attrs']['loggedInShow'] ) && ! empty( $block['attrs']['loggedInShow'] ) ) {
					$user = wp_get_current_user();
					$show_roles = array();
					foreach ( $block['attrs']['loggedInShow'] as $key => $user_rule ) {
						if ( isset( $user_rule['value'] ) && ! empty( $user_rule['value'] ) ) {
							$show_roles[] = $user_rule['value'];
						}
					}


					$match = array_intersect( $show_roles, (array) $user->roles );
					if ( count( $match ) === 0 ) {
						return '';
					} else {
						$hide = false;
					}
				}
				if ( $hide ) {
					return '';
				}
			}
			if ( isset( $block['attrs']['loggedOut'] ) && $block['attrs']['loggedOut'] && ! is_user_logged_in() ) {
				return '';
			}
		}

		return $block_content;
	}
	/**
	 * On init startup.
	 */
	public function on_init() {
		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'ive/testimonials',
			array(
				'render_callback' => array( $this, 'render_testimonials_css' ),
				'editor_script'   => 'ibtana-blocks-js',
				'editor_style'    => 'ibtana-blocks-editor-css',
			)
		);
		add_filter( 'excerpt_allowed_blocks', array( $this, 'add_blocks_to_excerpt' ), 20 );
	}
	/**
	 * Allow Columns to be looked at for inner content.
	 *
	 * @param array $allowed inner blocks.
	 */
	public function add_blocks_to_excerpt( $allowed ) {
		$allowed = array_merge( $allowed, apply_filters( 'ibtana_blocks_allowed_excerpt_blocks', array( 'ive/advancedheading' ) ) );
		return $allowed;
	}

	/**
	 * Render Inline CSS helper function
	 *
	 * @param array  $css the css for each rendered block.
	 * @param string $style_id the unique id for the rendered style.
	 * @param bool   $in_content the bool for whether or not it should run in content.
	 */
	public function render_inline_css( $css, $style_id, $in_content = false ) {
		if ( ! is_admin() ) {
			wp_register_style( $style_id, false );
			wp_enqueue_style( $style_id );
			wp_add_inline_style( $style_id, $css );
			if ( 1 === did_action( 'wp_head' ) && $in_content ) {
				wp_print_styles( $style_id );
			}
		}
	}

	/**
	 * Render Tabs Block CSS
	 *
	 * @param array  $attributes the blocks attribtues.
	 * @param string $content the blocks content.
	 */
	public function it_is_not_amp() {
		$not_amp = true;
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			$not_amp = false;
		}
		return $not_amp;
	}


	/**
	 * Render Testimonials CSS in Head
	 *
	 * @param array $attributes the blocks attribtues.
	 */
	public function render_testimonials_css_head( $attributes ) {
		if ( ! wp_style_is( 'ibtana-blocks-testimonials', 'enqueued' ) ) {
			$this->enqueue_style( 'ibtana-blocks-testimonials' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id  = 'ive-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'ibtana_blocks_render_inline_css', true, 'testimonials', $unique_id ) ) {
				$css = $this->blocks_testimonials_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Testimonials CSS
	 *
	 * @param array  $attributes the blocks attribtues.
	 * @param string $content the blocks content.
	 */
	public function render_testimonials_css( $attributes, $content ) {
		if ( ! wp_style_is( 'ibtana-blocks-testimonials', 'enqueued' ) ) {
			wp_enqueue_style( 'ibtana-blocks-testimonials' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id  = 'ive-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'ibtana_blocks_render_inline_css', true, 'testimonials', $unique_id ) ) {
				if ( isset( $attributes['layout'] ) && 'carousel' === $attributes['layout'] ) {
					if ( $this->it_is_not_amp() ) {
						wp_enqueue_style( 'ibtana-blocks-pro-slick' );
						if ( ! doing_filter( 'the_content' ) ) {
							if ( ! wp_style_is( 'ibtana-blocks-pro-slick', 'done' ) ) {
								wp_print_styles( 'ibtana-blocks-pro-slick' );
							}
						}
						wp_enqueue_script( 'ibtana-blocks-slick-init' );
					}
				}
				if ( ! doing_filter( 'the_content' ) ) {
					if ( ! wp_style_is( 'ibtana-blocks-testimonials', 'done' ) ) {
						wp_print_styles( 'ibtana-blocks-testimonials' );
					}
				}
				$css = $this->blocks_testimonials_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}


	/**
	 *
	 * Register and Enqueue block assets
	 *
	 * @since 1.0.0
	 */
	public function blocks_assets() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		$this->register_scripts();
	}
	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}

		wp_register_style( 'ibtana-blocks-testimonials', plugins_url('dist/blocks/testimonials.style.build.css', dirname(__FILE__)), array(), '1.9.3' );

		wp_localize_script(
			'ibtana-blocks-form',
			'ibtana_blocks_form_params',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'error_message' => __( 'Please fix the errors to proceed', 'ibtana-blocks' ),
				'nonce'         => wp_create_nonce( 'kb_form_nonce' ),
				'required'      => __( 'is required', 'ibtana-blocks' ),
				'mismatch'      => __( 'does not match', 'ibtana-blocks' ),
				'validation'    => __( 'is not valid', 'ibtana-blocks' ),
				'duplicate'     => __( 'requires a unique entry and this value has already been used', 'ibtana-blocks' ),
				'item'          => __( 'Item', 'ibtana-blocks' ),
			)
		);
		$recaptcha_site_key = get_option( 'ibtana_blocks_recaptcha_site_key' );
		if ( ! $recaptcha_site_key ) {
			$recaptcha_site_key = 'missingkey';
		}
		wp_register_script( 'ibtana-blocks-google-recaptcha-v3', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ), array(), false, true );
		$recaptcha_script = "grecaptcha.ready(function () { var recaptchaResponse = document.getElementById('kb_recaptcha_response'); if ( recaptchaResponse ) { grecaptcha.execute('" . esc_attr( $recaptcha_site_key ) . "', { action: 'kb_form' }).then(function (token) { recaptchaResponse.value = token; }); } var kb_recaptcha_inputs = document.getElementsByClassName('kb_recaptcha_response'); if ( ! kb_recaptcha_inputs.length ) { return; } for (var i = 0; i < kb_recaptcha_inputs.length; i++) { const e = i; grecaptcha.execute('" . esc_attr( $recaptcha_site_key ) . "', { action: 'kb_form' }).then(function (token) { kb_recaptcha_inputs[e].setAttribute('value', token); }); } });";
		wp_add_inline_script( 'ibtana-blocks-google-recaptcha-v3', $recaptcha_script, 'after' );
		//?render=explicit&onload=kbOnloadV2Callback
		wp_register_script( 'ibtana-blocks-google-recaptcha-v2', 'https://www.google.com/recaptcha/api.js?render=explicit&onload=kbOnloadV2Callback', array( 'jquery' ), false, true );

		$recaptcha_v2_script = "var kbOnloadV2Callback = function(){jQuery( '.wp-block-ibtana-form' ).find( '.ibtana-blocks-g-recaptcha-v2' ).each( function() {grecaptcha.render( jQuery( this ).attr( 'id' ), {'sitekey' : '" . esc_attr( $recaptcha_site_key ) . "',});});}";
		wp_add_inline_script( 'ibtana-blocks-google-recaptcha-v2', $recaptcha_v2_script, 'before' );

		// wp_register_script( 'ibtana-blocks-parallax-js', IBTANA_BLOCKS_URL . 'dist/ive-init-parallax.js', array( 'jarallax' ), false, true );
		wp_register_style( 'ibtana-blocks-pro-slick', plugins_url('dist/vendor/ive-blocks-slick.css', dirname(__FILE__)), array(), false );
		wp_register_script( 'ibtana-slick', plugins_url('dist/vendor/slick.min.js', dirname(__FILE__)), array( 'jquery' ), false, true );
		wp_register_script( 'ibtana-blocks-slick-init', plugins_url( 'dist/ive-slick-init.js', dirname(__FILE__) ), array( 'jquery', 'ibtana-slick' ), false, true );
		// wp_register_script( 'ibtana-blocks-video-bg', IBTANA_BLOCKS_URL . 'dist/kb-init-html-bg-video.js', array(), false, true );
		// wp_register_script( 'ibtana-blocks-masonry-init', IBTANA_BLOCKS_URL . 'dist/kb-masonry-init.js', array( 'jquery', 'masonry' ), false, true );
	}
	/**
	 * Registers and enqueue's script.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_script( $handle ) {
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_script( $handle );
	}
	/**
	 * Registers and enqueue's styles.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_style( $handle ) {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_style( $handle );
	}
	/**
	 * Hex to RGBA
	 *
	 * @param string $hex string hex code.
	 * @param number $alpha alpha number.
	 */
	public function hex2rgba( $hex, $alpha ) {
		if ( empty( $hex ) ) {
			return '';
		}
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
		return $rgba;
	}
	/**
	 * Load the faq schema in head.
	 */
	public function faq_schema() {
		if ( is_null( self::$faq_schema ) ) {
			return;
		}
		$print_faq_schema = apply_filters( 'ibtana_blocks_print_faq_schema', true );
		if ( ! $print_faq_schema ) {
			return;
		}
		echo self::$faq_schema;
	}

	/**
	 * Load the front end Google Fonts
	 */
	public function frontend_gfonts() {
		if ( empty( self::$gfonts ) ) {
			return;
		}
		$print_google_fonts = apply_filters( 'ibtana_blocks_print_google_fonts', true );
		if ( ! $print_google_fonts ) {
			return;
		}
		$link    = '';
		$subsets = array();
		foreach ( self::$gfonts as $key => $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['fontfamily'];
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['fontvariants'] );
			}
			if ( ! empty( $gfont_values['fontsubsets'] ) ) {
				foreach ( $gfont_values['fontsubsets'] as $subset ) {
					if ( ! in_array( $subset, $subsets ) ) {
						array_push( $subsets, $subset );
					}
				}
			}
		}
		if ( ! empty( $subsets ) ) {
			$link .= '&amp;subset=' . implode( ',', $subsets );
		}
		if ( apply_filters( 'ibtana_display_swap_google_fonts', true ) ) {
			$link .= '&amp;display=swap';
		}
		echo '<link href="//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ) . '" rel="stylesheet">';

	}
	/**
	 * Gets the parsed blocks, need to use this becuase wordpress 5 doesn't seem to include gutenberg_parse_blocks
	 *
	 * @param string $content string of page/post content.
	 */
	public function ibtana_parse_blocks( $content ) {
		$parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );
		if ( class_exists( $parser_class ) ) {
			$parser = new $parser_class();
			return $parser->parse( $content );
		} elseif ( function_exists( 'gutenberg_parse_blocks' ) ) {
			return gutenberg_parse_blocks( $content );
		} else {
			return false;
		}
	}
	/**
	 * Outputs extra css for blocks.
	 */
	public function global_inline_css() {
		$print_global_styles = apply_filters( 'ibtana_blocks_print_global_styles', true );
		if ( ! $print_global_styles ) {
			return;
		}
		$global_styles = json_decode( get_option( 'ibtana_blocks_global' ) );
		if ( $global_styles && is_object( $global_styles ) && isset( $global_styles->typography ) && is_object( $global_styles->typography ) ) {
			$css = '';
			foreach ( $global_styles->typography as $fontkey => $fontarray ) {
				if ( is_array( $fontarray ) ) {
					$font = $fontarray[0];
				}
				if ( isset( $font ) && is_object( $font ) && isset( $font->enable ) && true === $font->enable && isset( $font->loadGoogle ) && true === $font->loadGoogle && isset( $font->family ) && ! empty( $font->family ) ) {
					// Check if the font has been added yet.
					if ( isset( $font->family ) && ! array_key_exists( $font->family, self::$gfonts ) ) {
						$add_font = array(
							'fontfamily'   => $font->family,
							'fontvariants' => ( isset( $font->variant ) && ! empty( $font->variant ) ? array( $font->variant ) : array() ),
							'fontsubsets'  => ( isset( $font->subset ) && ! empty( $font->subset ) ? array( $font->subset ) : array() ),
						);
						self::$gfonts[ $font->family ] = $add_font;
					} else {
						if ( ! in_array( $font->variant, self::$gfonts[ $font->family ]['fontvariants'], true ) ) {
							array_push( self::$gfonts[ $font->family ]['fontvariants'], $font->variant );
						}
						if ( ! in_array( $font->subset, self::$gfonts[ $font->family ]['fontsubsets'], true ) ) {
							array_push( self::$gfonts[ $font->family ]['fontsubsets'], $font->subset );
						}
					}
				}
				if ( isset( $font ) && is_object( $font ) && isset( $font->enable ) && true === $font->enable ) {
					$add_css = false;
					$new_css = '';
					if ( 'p' === $fontkey ) {
						$new_css .= 'body {';
					} else {
						$new_css .= $fontkey .' {';
					}
					if ( isset( $font->color ) && ! empty( $font->color ) ) {
						$add_css = true;
						$new_css .= 'color:' . $font->color . ';';
					}
					if ( isset( $font->size ) && is_array( $font->size ) && is_numeric( $font->size[0] ) ) {
						$add_css = true;
						$new_css .= 'font-size:' . $font->size[0] . ( ! isset( $font->sizeType ) ? 'px' : $font->sizeType ) . ';';
					}
					if ( isset( $font->lineHeight ) && is_array( $font->lineHeight ) && is_numeric( $font->lineHeight[0] ) ) {
						$add_css = true;
						$new_css .= 'line-height:' . $font->lineHeight[0] . ( ! isset( $font->lineType ) ? 'px' : $font->lineType ) . ';';
					}
					if ( isset( $font->letterSpacing ) && is_numeric( $font->letterSpacing ) ) {
						$add_css = true;
						$new_css .= 'letter-spacing:' . $font->letterSpacing . 'px;';
					}
					if ( isset( $font->textTransform ) && ! empty( $font->textTransform ) ) {
						$add_css = true;
						$new_css .= 'text-transform:' . $font->textTransform . ';';
					}
					if ( isset( $font->family ) && ! empty( $font->family ) ) {
						$add_css = true;
						$new_css .= 'font-family:' . $font->family. ';';
					}
					if ( isset( $font->style ) && ! empty( $font->style ) ) {
						$add_css = true;
						$new_css .= 'font-style:' . $font->style . ';';
					}
					if ( isset( $font->weight ) && ! empty( $font->weight ) ) {
						$add_css = true;
						$new_css .= 'font-weight:' . $font->weight . ';';
					}
					if ( 'p' === $fontkey ) {
						$new_css .= '}';
						$new_css .= 'p {';
					}
					if ( isset( $font->padding ) && is_array( $font->padding ) && is_numeric( $font->padding[0] ) ) {
						$add_css = true;
						$new_css .= 'padding-top:' . $font->padding[0] . ( ! isset( $font->paddingType ) ? 'px' : $font->paddingType ) . ';';
					}
					if ( isset( $font->padding ) && is_array( $font->padding ) && is_numeric( $font->padding[1] ) ) {
						$add_css = true;
						$new_css .= 'padding-right:' . $font->padding[1] . ( ! isset( $font->paddingType ) ? 'px' : $font->paddingType ) . ';';
					}
					if ( isset( $font->padding ) && is_array( $font->padding ) && is_numeric( $font->padding[2] ) ) {
						$add_css = true;
						$new_css .= 'padding-bottom:' . $font->padding[2] . ( ! isset( $font->paddingType ) ? 'px' : $font->paddingType ) . ';';
					}
					if ( isset( $font->padding ) && is_array( $font->padding ) && is_numeric( $font->padding[3] ) ) {
						$add_css = true;
						$new_css .= 'padding-left:' . $font->padding[3] . ( ! isset( $font->paddingType ) ? 'px' : $font->paddingType ) . ';';
					}
					if ( isset( $font->margin ) && is_array( $font->margin ) && is_numeric( $font->margin[0] ) ) {
						$add_css = true;
						$new_css .= 'margin-top:' . $font->margin[0] . ( ! isset( $font->marginType ) ? 'px' : $font->marginType ) . ';';
					}
					if ( isset( $font->margin ) && is_array( $font->margin ) && is_numeric( $font->margin[1] ) ) {
						$add_css = true;
						$new_css .= 'margin-right:' . $font->margin[1] . ( ! isset( $font->marginType ) ? 'px' : $font->marginType ) . ';';
					}
					if ( isset( $font->margin ) && is_array( $font->margin ) && is_numeric( $font->margin[2] ) ) {
						$add_css = true;
						$new_css .= 'margin-bottom:' . $font->margin[2] . ( ! isset( $font->marginType ) ? 'px' : $font->marginType ) . ';';
					}
					if ( isset( $font->margin ) && is_array( $font->margin ) && is_numeric( $font->margin[3] ) ) {
						$add_css = true;
						$new_css .= 'margin-left:' . $font->margin[3] . ( ! isset( $font->marginType ) ? 'px' : $font->marginType ) . ';';
					}
					$new_css .= '}';
					if ( ( isset( $font->size ) && is_array( $font->size ) && is_numeric( $font->size[1] ) ) || ( isset( $font->lineHeight ) && is_array( $font->lineHeight ) && is_numeric( $font->lineHeight[1] ) ) ) {
						$add_css = true;
						$new_css .= '@media (min-width: 767px) and (max-width: 1024px) {';
						if ( 'p' === $fontkey ) {
							$new_css .= 'body {';
						} else {
							$new_css .= $fontkey .' {';
						}
						if ( isset( $font->size ) && is_array( $font->size ) && is_numeric( $font->size[1] ) ) {
							$new_css .= 'font-size:' . $font->size[1] . ( isset( $font->sizeType ) && ! empty( $font->sizeType ) ? $font->sizeType : 'px' ) . ';';
						}
						if ( isset( $font->lineHeight ) && is_array( $font->lineHeight ) && is_numeric( $font->lineHeight[1] ) ) {
							$new_css .= 'line-height:' . $font->lineHeight[1] . ( isset( $font->lineType ) && ! empty( $font->lineType ) ? $font->lineType : 'px' ) . ';';
						}
						$new_css .= '}';
						$new_css .= '}';
					}
					if ( ( isset( $font->size ) && is_array( $font->size ) && is_numeric( $font->size[2] ) ) || ( isset( $font->lineHeight ) && is_array( $font->lineHeight ) && is_numeric( $font->lineHeight[2] ) ) ) {
						$add_css = true;
						$new_css .= '@media (max-width: 767px) {';
						if ( 'p' === $fontkey ) {
							$new_css .= 'body {';
						} else {
							$new_css .= $fontkey .' {';
						}
						if ( isset( $font->size ) && is_array( $font->size ) && is_numeric( $font->size[2] ) ) {
							$new_css .= 'font-size:' . $font->size[2] . ( isset( $font->sizeType ) && ! empty( $font->sizeType ) ? $font->sizeType : 'px' ) . ';';
						}
						if ( isset( $font->lineHeight ) && is_array( $font->lineHeight ) && is_numeric( $font->lineHeight[2] ) ) {
							$new_css .= 'line-height:' . $font->lineHeight[2] . ( isset( $font->lineType ) && ! empty( $font->lineType ) ? $font->lineType : 'px' ) . ';';
						}
						$new_css .= '}';
						$new_css .= '}';
					}
					if ( $add_css ) {
						$css .= $new_css;
					}
				}
			}
			if ( ! empty( $css ) ) {
				$this->render_inline_css( $css, 'ibtana-blocks-global-styles' );
			}
		}
	}
	/**
	 * Outputs extra css for blocks.
	 */
	public function frontend_inline_css() {
		if ( function_exists( 'has_blocks' ) && has_blocks( get_the_ID() ) ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$this->frontend_build_css( $post );
		}
	}
	/**
	 * Outputs extra css for blocks.
	 *
	 * @param $post_object object of WP_Post.
	 */
	public function frontend_build_css( $post_object ) {
		if ( ! is_object( $post_object ) ) {
			return;
		}
		if ( ! method_exists( $post_object, 'post_content' ) ) {
			$blocks = $this->ibtana_parse_blocks( $post_object->post_content );
			// print_r( $blocks );
			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}
			foreach ( $blocks as $indexkey => $block ) {
				if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {

					if ( 'ive/testimonials' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_testimonials_css_head( $blockattr );
							$this->blocks_testimonials_scripts_gfonts( $blockattr );
						}
					}


					if ( 'core/block' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['ref'] ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
									$reuse_data_block = $this->ibtana_parse_blocks( $reusable_block->post_content );
									$this->blocks_cycle_through( $reuse_data_block );
								}
							}
						}
					}
					if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
						$this->blocks_cycle_through( $block['innerBlocks'] );
					}
				}
			}
		}
	}
	/**
	 * Builds css for inner blocks
	 *
	 * @param array $inner_blocks array of inner blocks.
	 */
	public function blocks_cycle_through( $inner_blocks ) {
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {


				if ( 'ive/testimonials' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						$this->render_testimonials_css_head( $blockattr );
						$this->blocks_testimonials_scripts_gfonts( $blockattr );
					}
				}

				if ( 'core/block' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['ref'] ) ) {
							$reusable_block = get_post( $blockattr['ref'] );
							if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
								$reuse_data_block = $this->ibtana_parse_blocks( $reusable_block->post_content );
								$this->blocks_cycle_through( $reuse_data_block );
							}
						}
					}
				}
				if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$this->blocks_cycle_through( $inner_block['innerBlocks'] );
				}
			}
		}
	}
	/**
	 * Grabs the scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_tableofcontents_scripts_check( $attr ) {
		$toc = Ibtana_Blocks_Table_Of_Contents::get_instance();
		$toc->enqueue_script( 'ibtana-blocks-table-of-contents' );
		$toc->enqueue_style( 'ibtana-blocks-table-of-contents' );
		if ( isset( $attr['uniqueID'] ) ) {
			$unique_id = $attr['uniqueID'];
			$style_id = 'ive-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $toc->output_css( $attr, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
		if ( isset( $attr['labelFont'] ) && is_array( $attr['labelFont'] ) && isset( $attr['labelFont'][0] ) && is_array( $attr['labelFont'][0] ) && isset( $attr['labelFont'][0]['google'] ) && $attr['labelFont'][0]['google'] && ( ! isset( $attr['labelFont'][0]['loadGoogle'] ) || true === $attr['labelFont'][0]['loadGoogle'] ) && isset( $attr['labelFont'][0]['family'] ) ) {
			$label_font = $attr['labelFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $label_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily'   => $label_font['family'],
					'fontvariants' => ( isset( $label_font['variant'] ) && ! empty( $label_font['variant'] ) ? array( $label_font['variant'] ) : array() ),
					'fontsubsets'  => ( isset( $label_font['subset'] ) && ! empty( $label_font['subset'] ) ? array( $label_font['subset'] ) : array() ),
				);
				self::$gfonts[ $label_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $label_font['variant'], self::$gfonts[ $label_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $label_font['family'] ]['fontvariants'], $label_font['variant'] );
				}
				if ( ! in_array( $label_font['subset'], self::$gfonts[ $label_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $label_font['family'] ]['fontsubsets'], $label_font['subset'] );
				}
			}
		}
		if ( isset( $attr['submitFont'] ) && is_array( $attr['submitFont'] ) && isset( $attr['submitFont'][0] ) && is_array( $attr['submitFont'][0] ) && isset( $attr['submitFont'][0]['google'] ) && $attr['submitFont'][0]['google'] && ( ! isset( $attr['submitFont'][0]['loadGoogle'] ) || true === $attr['submitFont'][0]['loadGoogle'] ) && isset( $attr['submitFont'][0]['family'] ) ) {
			$submit_font = $attr['submitFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $submit_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $submit_font['family'],
					'fontvariants' => ( isset( $submit_font['variant'] ) && ! empty( $submit_font['variant'] ) ? array( $submit_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $submit_font['subset'] ) && ! empty( $submit_font['subset'] ) ? array( $submit_font['subset'] ) : array() ),
				);
				self::$gfonts[ $submit_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $submit_font['variant'], self::$gfonts[ $submit_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $submit_font['family'] ]['fontvariants'], $submit_font['variant'] );
				}
				if ( ! in_array( $submit_font['subset'], self::$gfonts[ $submit_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $submit_font['family'] ]['fontsubsets'], $submit_font['subset'] );
				}
			}
		}
	}
	/**
	 * Grabs the scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_form_scripts_check( $attr ) {
		$this->enqueue_script( 'ibtana-blocks-form' );
		if ( isset( $attr['recaptcha'] ) && $attr['recaptcha'] ) {
			if ( isset( $attr['recaptchaVersion'] ) && 'v2' === $attr['recaptchaVersion'] ) {
				$this->enqueue_script( 'ibtana-blocks-google-recaptcha-v2' );
			} else {
				$this->enqueue_script( 'ibtana-blocks-google-recaptcha-v3' );
			}
		}
		if ( isset( $attr['labelFont'] ) && is_array( $attr['labelFont'] ) && isset( $attr['labelFont'][0] ) && is_array( $attr['labelFont'][0] ) && isset( $attr['labelFont'][0]['google'] ) && $attr['labelFont'][0]['google'] && ( ! isset( $attr['labelFont'][0]['loadGoogle'] ) || true === $attr['labelFont'][0]['loadGoogle'] ) && isset( $attr['labelFont'][0]['family'] ) ) {
			$label_font = $attr['labelFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $label_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily'   => $label_font['family'],
					'fontvariants' => ( isset( $label_font['variant'] ) && ! empty( $label_font['variant'] ) ? array( $label_font['variant'] ) : array() ),
					'fontsubsets'  => ( isset( $label_font['subset'] ) && ! empty( $label_font['subset'] ) ? array( $label_font['subset'] ) : array() ),
				);
				self::$gfonts[ $label_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $label_font['variant'], self::$gfonts[ $label_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $label_font['family'] ]['fontvariants'], $label_font['variant'] );
				}
				if ( ! in_array( $label_font['subset'], self::$gfonts[ $label_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $label_font['family'] ]['fontsubsets'], $label_font['subset'] );
				}
			}
		}
		if ( isset( $attr['submitFont'] ) && is_array( $attr['submitFont'] ) && isset( $attr['submitFont'][0] ) && is_array( $attr['submitFont'][0] ) && isset( $attr['submitFont'][0]['google'] ) && $attr['submitFont'][0]['google'] && ( ! isset( $attr['submitFont'][0]['loadGoogle'] ) || true === $attr['submitFont'][0]['loadGoogle'] ) && isset( $attr['submitFont'][0]['family'] ) ) {
			$submit_font = $attr['submitFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $submit_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $submit_font['family'],
					'fontvariants' => ( isset( $submit_font['variant'] ) && ! empty( $submit_font['variant'] ) ? array( $submit_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $submit_font['subset'] ) && ! empty( $submit_font['subset'] ) ? array( $submit_font['subset'] ) : array() ),
				);
				self::$gfonts[ $submit_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $submit_font['variant'], self::$gfonts[ $submit_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $submit_font['family'] ]['fontvariants'], $submit_font['variant'] );
				}
				if ( ! in_array( $submit_font['subset'], self::$gfonts[ $submit_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $submit_font['family'] ]['fontsubsets'], $submit_font['subset'] );
				}
			}
		}
		if ( isset( $attr['messageFont'] ) && is_array( $attr['messageFont'] ) && isset( $attr['messageFont'][0] ) && is_array( $attr['messageFont'][0] ) && isset( $attr['messageFont'][0]['google'] ) && $attr['messageFont'][0]['google'] && ( ! isset( $attr['messageFont'][0]['loadGoogle'] ) || true === $attr['messageFont'][0]['loadGoogle'] ) && isset( $attr['messageFont'][0]['family'] ) ) {
			$message_font = $attr['messageFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $message_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $message_font['family'],
					'fontvariants' => ( isset( $message_font['variant'] ) && ! empty( $message_font['variant'] ) ? array( $message_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $message_font['subset'] ) && ! empty( $message_font['subset'] ) ? array( $message_font['subset'] ) : array() ),
				);
				self::$gfonts[ $message_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $message_font['variant'], self::$gfonts[ $message_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $message_font['family'] ]['fontvariants'], $message_font['variant'] );
				}
				if ( ! in_array( $message_font['subset'], self::$gfonts[ $message_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $message_font['family'] ]['fontsubsets'], $message_font['subset'] );
				}
			}
		}
	}


	/**
	 * Grabs the Google Fonts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_testimonials_scripts_gfonts( $attr ) {
		if ( isset( $attr['layout'] ) && 'carousel' === $attr['layout'] ) {
			$this->enqueue_style( 'ibtana-blocks-pro-slick' );
			$this->enqueue_script( 'ibtana-blocks-slick-init' );
		}
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) && isset( $attr['titleFont'][0]['google'] ) && $attr['titleFont'][0]['google'] && ( ! isset( $attr['titleFont'][0]['loadGoogle'] ) || true === $attr['titleFont'][0]['loadGoogle'] ) && isset( $attr['titleFont'][0]['family'] ) ) {
			$title_font = $attr['titleFont'][0];
			// Check if the font has been added yet
			if ( ! array_key_exists( $title_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $title_font['family'],
					'fontvariants' => ( isset( $title_font['variant'] ) && ! empty( $title_font['variant'] ) ? array( $title_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $title_font['subset'] ) && !empty( $title_font['subset'] ) ? array( $title_font['subset'] ) : array() ),
				);
				self::$gfonts[ $title_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $title_font['variant'], self::$gfonts[ $title_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $title_font['family'] ]['fontvariants'], $title_font['variant'] );
				}
				if ( ! in_array( $title_font['subset'], self::$gfonts[ $title_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $title_font['family'] ]['fontsubsets'], $title_font['subset'] );
				}
			}
		}
		if ( isset( $attr['contentFont'] ) && is_array( $attr['contentFont'] ) && isset( $attr['contentFont'][0] ) && is_array( $attr['contentFont'][0] ) && isset( $attr['contentFont'][0]['google'] ) && $attr['contentFont'][0]['google'] && ( ! isset( $attr['contentFont'][0]['loadGoogle'] ) || true === $attr['contentFont'][0]['loadGoogle'] ) && isset( $attr['contentFont'][0]['family'] ) ) {
			$content_font = $attr['contentFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $content_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $content_font['family'],
					'fontvariants' => ( isset( $content_font['variant'] ) && ! empty( $content_font['variant'] ) ? array( $content_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $content_font['subset'] ) && ! empty( $content_font['subset'] ) ? array( $content_font['subset'] ) : array() ),
				);
				self::$gfonts[ $content_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $content_font['variant'], self::$gfonts[ $content_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $content_font['family'] ]['fontvariants'], $content_font['variant'] );
				}
				if ( ! in_array( $content_font['subset'], self::$gfonts[ $content_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $content_font['family'] ]['fontsubsets'], $content_font['subset'] );
				}
			}
		}
		if ( isset( $attr['nameFont'] ) && is_array( $attr['nameFont'] ) && isset( $attr['nameFont'][0] ) && is_array( $attr['nameFont'][0] ) && isset( $attr['nameFont'][0]['google'] ) && $attr['nameFont'][0]['google'] && ( ! isset( $attr['nameFont'][0]['loadGoogle'] ) || true === $attr['nameFont'][0]['loadGoogle'] ) && isset( $attr['nameFont'][0]['family'] ) ) {
			$name_font = $attr['nameFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $name_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $name_font['family'],
					'fontvariants' => ( isset( $name_font['variant'] ) && ! empty( $name_font['variant'] ) ? array( $name_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $name_font['subset'] ) && ! empty( $name_font['subset'] ) ? array( $name_font['subset'] ) : array() ),
				);
				self::$gfonts[ $name_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $name_font['variant'], self::$gfonts[ $name_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $name_font['family'] ]['fontvariants'], $name_font['variant'] );
				}
				if ( ! in_array( $name_font['subset'], self::$gfonts[ $name_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $name_font['family'] ]['fontsubsets'], $name_font['subset'] );
				}
			}
		}
		if ( isset( $attr['occupationFont'] ) && is_array( $attr['occupationFont'] ) && isset( $attr['occupationFont'][0] ) && is_array( $attr['occupationFont'][0] ) && isset( $attr['occupationFont'][0]['google'] ) && $attr['occupationFont'][0]['google'] && ( ! isset( $attr['occupationFont'][0]['loadGoogle'] ) || true === $attr['occupationFont'][0]['loadGoogle'] ) && isset( $attr['occupationFont'][0]['family'] ) ) {
			$occupation_font = $attr['occupationFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $occupation_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $occupation_font['family'],
					'fontvariants' => ( isset( $occupation_font['variant'] ) && ! empty( $occupation_font['variant'] ) ? array( $occupation_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $occupation_font['subset'] ) && ! empty( $occupation_font['subset'] ) ? array( $occupation_font['subset'] ) : array() ),
				);
				self::$gfonts[ $occupation_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $occupation_font['variant'], self::$gfonts[ $occupation_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $occupation_font['family'] ]['fontvariants'], $occupation_font['variant'] );
				}
				if ( ! in_array( $occupation_font['subset'], self::$gfonts[ $occupation_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $occupation_font['family'] ]['fontsubsets'], $occupation_font['subset'] );
				}
			}
		}
	}
	/**
	 * Builds CSS for Testimonial block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_testimonials_array( $attr, $unique_id ) {
		$css = '';
		if (
			isset( $attr['layout'] ) && 'carousel' === $attr['layout'] && isset( $attr['columnGap'] ) && ! empty( $attr['columnGap'] )
		) {
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-blocks-carousel .ive-blocks-testimonial-carousel-item {';
				$css .= 'padding: 0 ' . ( $attr['columnGap'] / 2 ) . 'px;';
			$css .= '}';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-blocks-carousel .ive-blocks-carousel-init {';
				$css .= 'margin: 0 -' . ( $attr['columnGap'] / 2 ) . 'px;';
			$css .= '}';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-blocks-carousel .slick-prev {';
				$css .= 'left:' . ( $attr['columnGap'] / 2 ) . 'px;';
			$css .= '}';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-blocks-carousel .slick-next {';
				$css .= 'right:' . ( $attr['columnGap'] / 2 ) . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['style'] ) && ( 'bubble' === $attr['style'] || 'inlineimage' === $attr['style'] ) ) {
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-text-wrap:after {';
			if ( isset( $attr['containerBorderWidth'] ) && is_array( $attr['containerBorderWidth'] ) && ! empty( $attr['containerBorderWidth'][2] ) ) {
				$css .= 'margin-top: ' . $attr['containerBorderWidth'][2] . 'px;';
			}
			if ( isset( $attr['containerBorder'] ) && ! empty( $attr['containerBorder'] ) ) {
				$alpha = ( isset( $attr['containerBorderOpacity'] ) && is_numeric( $attr['containerBorderOpacity'] ) ? $attr['containerBorderOpacity'] : 1 );
				$css .= 'border-top-color: ' . $this->ibtana_color_output( $attr['containerBorder'], $alpha ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && is_array( $attr['titleFont'][0] ) ) {
			$title_font = $attr['titleFont'][0];

			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-title {';
			if ( isset( $title_font['color'] ) && ! empty( $title_font['color'] ) ) {
				$css .= 'color:' . $this->ibtana_color_output( $title_font['color'] ) . ';';
			}
			if ( isset( $title_font['size'] ) && is_array( $title_font['size'] ) && ! empty( $title_font['size'][0] ) ) {
				$css .= 'font-size:' . $title_font['size'][0] . ( ! isset( $title_font['sizeType'] ) ? 'px' : $title_font['sizeType'] ) . ';';
			}
			if ( isset( $title_font['lineHeight'] ) && is_array( $title_font['lineHeight'] ) && ! empty( $title_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $title_font['lineHeight'][0] . ( ! isset( $title_font['lineType'] ) ? 'px' : $title_font['lineType'] ) . ';';
			}
			if ( isset( $title_font['letterSpacing'] ) && ! empty( $title_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $title_font['letterSpacing'] . 'px;';
			}
			if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $title_font['textTransform'] . ';';
			}
			if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
				$css .= 'font-family:' . $title_font['family'] . ';';
			}
			if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
				$css .= 'font-style:' . $title_font['style'] . ';';
			}
			if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) && 'regular' !== $title_font['weight'] ) {
				$css .= 'font-weight:' . $title_font['weight'] . ';';
			}
			$css .= '}';
		}
		if (
			isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) &&
			is_array( $attr['titleFont'][0] ) &&
			(
				(
					isset( $attr['titleFont'][0]['size'] ) && is_array( $attr['titleFont'][0]['size'] ) &&
					isset( $attr['titleFont'][0]['size'][1] ) && ! empty( $attr['titleFont'][0]['size'][1] )
				) ||
				( isset( $attr['titleFont'][0]['lineHeight'] ) && is_array( $attr['titleFont'][0]['lineHeight'] ) && isset( $attr['titleFont'][0]['lineHeight'][1] ) && ! empty( $attr['titleFont'][0]['lineHeight'][1] ) )
			)
		) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-title {';
			if ( isset( $attr['titleFont'][0]['size'][1] ) && ! empty( $attr['titleFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['titleFont'][0]['size'][1] . ( ! isset( $attr['titleFont'][0]['sizeType'] ) ? 'px' : $attr['titleFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['titleFont'][0]['lineHeight'][1] ) && ! empty( $attr['titleFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['titleFont'][0]['lineHeight'][1] . ( ! isset( $attr['titleFont'][0]['lineType'] ) ? 'px' : $attr['titleFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}

		if (
			isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) &&
			(
				(
					isset( $attr['titleFont'][0]['size'] ) && is_array( $attr['titleFont'][0]['size'] ) &&
					isset( $attr['titleFont'][0]['size'][2] ) && ! empty( $attr['titleFont'][0]['size'][2] )
				) ||
				(
					isset( $attr['titleFont'][0]['lineHeight'] ) && is_array( $attr['titleFont'][0]['lineHeight'] ) &&
					isset( $attr['titleFont'][0]['lineHeight'][2] ) && ! empty( $attr['titleFont'][0]['lineHeight'][2] )
				)
			)
		) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-title {';
			if ( isset( $attr['titleFont'][0]['size'][2] ) && ! empty( $attr['titleFont'][0]['size'][2] ) ) {
				$css .= 'font-size:' . $attr['titleFont'][0]['size'][2] . ( ! isset( $attr['titleFont'][0]['sizeType'] ) ? 'px' : $attr['titleFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['titleFont'][0]['lineHeight'][2] ) && ! empty( $attr['titleFont'][0]['lineHeight'][2] ) ) {
				$css .= 'line-height:' . $attr['titleFont'][0]['lineHeight'][2] . ( ! isset( $attr['titleFont'][0]['lineType'] ) ? 'px' : $attr['titleFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['contentFont'] ) && is_array( $attr['contentFont'] ) && is_array( $attr['contentFont'][0] ) ) {
			$content_font = $attr['contentFont'][0];

			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-content {';
			if ( isset( $content_font['color'] ) && ! empty( $content_font['color'] ) ) {
				$css .= 'color:' . $this->ibtana_color_output( $content_font['color'] ) . ';';
			}
			if ( isset( $content_font['size'] ) && is_array( $content_font['size'] ) && ! empty( $content_font['size'][0] ) ) {
				$css .= 'font-size:' . $content_font['size'][0] . ( ! isset( $content_font['sizeType'] ) ? 'px' : $content_font['sizeType'] ) . ';';
			}
			if ( isset( $content_font['lineHeight'] ) && is_array( $content_font['lineHeight'] ) && ! empty( $content_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $content_font['lineHeight'][0] . ( ! isset( $content_font['lineType'] ) ? 'px' : $content_font['lineType'] ) . ';';
			}
			if ( isset( $content_font['letterSpacing'] ) && ! empty( $content_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $content_font['letterSpacing'] . 'px;';
			}
			if ( isset( $content_font['textTransform'] ) && ! empty( $content_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $content_font['textTransform'] . ';';
			}
			if ( isset( $content_font['family'] ) && ! empty( $content_font['family'] ) ) {
				$css .= 'font-family:' . $content_font['family'] . ';';
			}
			if ( isset( $content_font['style'] ) && ! empty( $content_font['style'] ) ) {
				$css .= 'font-style:' . $content_font['style'] . ';';
			}
			if ( isset( $content_font['weight'] ) && ! empty( $content_font['weight'] ) && 'regular' !== $content_font['weight'] ) {
				$css .= 'font-weight:' . $content_font['weight'] . ';';
			}
			$css .= '}';
		}

		if (
			isset( $attr['contentFont'] ) && is_array( $attr['contentFont'] ) && isset( $attr['contentFont'][0] ) &&
			is_array( $attr['contentFont'][0] ) &&
			(
				(
					isset( $attr['contentFont'][0]['size'] ) && is_array( $attr['contentFont'][0]['size'] ) &&
					isset( $attr['contentFont'][0]['size'][1] ) && ! empty( $attr['contentFont'][0]['size'][1] )
				) ||
				(
					isset( $attr['contentFont'][0]['lineHeight'] ) && is_array( $attr['contentFont'][0]['lineHeight'] ) &&
					isset( $attr['contentFont'][0]['lineHeight'][1] ) && ! empty( $attr['contentFont'][0]['lineHeight'][1] )
				)
			)
		) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-content {';
			if ( isset( $attr['contentFont'][0]['size'][1] ) && ! empty( $attr['contentFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['contentFont'][0]['size'][1] . ( ! isset( $attr['contentFont'][0]['sizeType'] ) ? 'px' : $attr['contentFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['contentFont'][0]['lineHeight'][1] ) && ! empty( $attr['contentFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['contentFont'][0]['lineHeight'][1] . ( ! isset( $attr['contentFont'][0]['lineType'] ) ? 'px' : $attr['contentFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}

		if (
			isset( $attr['contentFont'] ) && is_array( $attr['contentFont'] ) && isset( $attr['contentFont'][0] ) &&
			is_array( $attr['contentFont'][0] ) &&
			(
				(
					isset( $attr['contentFont'][0]['size'] ) && is_array( $attr['contentFont'][0]['size'] ) &&
					isset( $attr['contentFont'][0]['size'][2] ) && ! empty( $attr['contentFont'][0]['size'][2] )
				) ||
				(
					isset( $attr['contentFont'][0]['lineHeight'] ) && is_array( $attr['contentFont'][0]['lineHeight'] ) &&
					isset( $attr['contentFont'][0]['lineHeight'][2] ) && ! empty( $attr['contentFont'][0]['lineHeight'][2] )
				)
			)
		) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-content {';
			if ( isset( $attr['contentFont'][0]['size'][2] ) && ! empty( $attr['contentFont'][0]['size'][2] ) ) {
				$css .= 'font-size:' . $attr['contentFont'][0]['size'][2] . ( ! isset( $attr['contentFont'][0]['sizeType'] ) ? 'px' : $attr['contentFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['contentFont'][0]['lineHeight'][2] ) && ! empty( $attr['contentFont'][0]['lineHeight'][2] ) ) {
				$css .= 'line-height:' . $attr['contentFont'][0]['lineHeight'][2] . ( ! isset( $attr['contentFont'][0]['lineType'] ) ? 'px' : $attr['contentFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}

		if ( isset( $attr['nameFont'] ) && is_array( $attr['nameFont'] ) && is_array( $attr['nameFont'][0] ) ) {
			$name_font = $attr['nameFont'][0];

			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-name {';
			if ( isset( $name_font['color'] ) && ! empty( $name_font['color'] ) ) {
				$css .= 'color:' . $this->ibtana_color_output( $name_font['color'] ) . ';';
			}
			if ( isset( $name_font['size'] ) && is_array( $name_font['size'] ) && ! empty( $name_font['size'][0] ) ) {
				$css .= 'font-size:' . $name_font['size'][0] . ( ! isset( $name_font['sizeType'] ) ? 'px' : $name_font['sizeType'] ) . ';';
			}
			if ( isset( $name_font['lineHeight'] ) && is_array( $name_font['lineHeight'] ) && ! empty( $name_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $name_font['lineHeight'][0] . ( ! isset( $name_font['lineType'] ) ? 'px' : $name_font['lineType'] ) . ';';
			}
			if ( isset( $name_font['letterSpacing'] ) && ! empty( $name_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $name_font['letterSpacing'] . 'px;';
			}
			if ( isset( $name_font['textTransform'] ) && ! empty( $name_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $name_font['textTransform'] . ';';
			}
			if ( isset( $name_font['family'] ) && ! empty( $name_font['family'] ) ) {
				$css .= 'font-family:' . $name_font['family'] . ';';
			}
			if ( isset( $name_font['style'] ) && ! empty( $name_font['style'] ) ) {
				$css .= 'font-style:' . $name_font['style'] . ';';
			}
			if ( isset( $name_font['weight'] ) && ! empty( $name_font['weight'] ) && 'regular' !== $name_font['weight'] ) {
				$css .= 'font-weight:' . $name_font['weight'] . ';';
			}
			$css .= '}';
		}

		if (
			isset( $attr['nameFont'] ) && is_array( $attr['nameFont'] ) && isset( $attr['nameFont'][0] ) &&
			is_array( $attr['nameFont'][0] ) &&
			(
				(
					isset( $attr['nameFont'][0]['size'] ) && is_array( $attr['nameFont'][0]['size'] ) &&
					isset( $attr['nameFont'][0]['size'][1] ) && ! empty( $attr['nameFont'][0]['size'][1] )
				) ||
				(
					isset( $attr['nameFont'][0]['lineHeight'] ) && is_array( $attr['nameFont'][0]['lineHeight'] ) &&
					isset( $attr['nameFont'][0]['lineHeight'][1] ) && ! empty( $attr['nameFont'][0]['lineHeight'][1] )
				)
			)
		) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-name {';
			if ( isset( $attr['nameFont'][0]['size'][1] ) && ! empty( $attr['nameFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['nameFont'][0]['size'][1] . ( ! isset( $attr['nameFont'][0]['sizeType'] ) ? 'px' : $attr['nameFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['nameFont'][0]['lineHeight'][1] ) && ! empty( $attr['nameFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['nameFont'][0]['lineHeight'][1] . ( ! isset( $attr['nameFont'][0]['lineType'] ) ? 'px' : $attr['nameFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}

		if ( isset( $attr['nameFont'] ) && is_array( $attr['nameFont'] ) && isset( $attr['nameFont'][0] ) && is_array( $attr['nameFont'][0] ) && ( ( isset( $attr['nameFont'][0]['size'] ) && is_array( $attr['nameFont'][0]['size'] ) && isset( $attr['nameFont'][0]['size'][2] ) && ! empty( $attr['nameFont'][0]['size'][2] ) ) || ( isset( $attr['nameFont'][0]['lineHeight'] ) && is_array( $attr['nameFont'][0]['lineHeight'] ) && isset( $attr['nameFont'][0]['lineHeight'][2] ) && ! empty( $attr['nameFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-name {';
			if ( isset( $attr['nameFont'][0]['size'][2] ) && ! empty( $attr['nameFont'][0]['size'][2] ) ) {
				$css .= 'font-size:' . $attr['nameFont'][0]['size'][2] . ( ! isset( $attr['nameFont'][0]['sizeType'] ) ? 'px' : $attr['nameFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['nameFont'][0]['lineHeight'][2] ) && ! empty( $attr['nameFont'][0]['lineHeight'][2] ) ) {
				$css .= 'line-height:' . $attr['nameFont'][0]['lineHeight'][2] . ( ! isset( $attr['nameFont'][0]['lineType'] ) ? 'px' : $attr['nameFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['occupationFont'] ) && is_array( $attr['occupationFont'] ) && is_array( $attr['occupationFont'][0] ) ) {
			$occupation_font = $attr['occupationFont'][0];

			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-occupation {';
			if ( isset( $occupation_font['color'] ) && ! empty( $occupation_font['color'] ) ) {
				$css .= 'color:' . $this->ibtana_color_output( $occupation_font['color'] ) . ';';
			}
			if ( isset( $occupation_font['size'] ) && is_array( $occupation_font['size'] ) && ! empty( $occupation_font['size'][0] ) ) {
				$css .= 'font-size:' . $occupation_font['size'][0] . ( ! isset( $occupation_font['sizeType'] ) ? 'px' : $occupation_font['sizeType'] ) . ';';
			}
			if ( isset( $occupation_font['lineHeight'] ) && is_array( $occupation_font['lineHeight'] ) && ! empty( $occupation_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $occupation_font['lineHeight'][0] . ( ! isset( $occupation_font['lineType'] ) ? 'px' : $occupation_font['lineType'] ) . ';';
			}
			if ( isset( $occupation_font['letterSpacing'] ) && ! empty( $occupation_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $occupation_font['letterSpacing'] . 'px;';
			}
			if ( isset( $occupation_font['textTransform'] ) && ! empty( $occupation_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $occupation_font['textTransform'] . ';';
			}
			if ( isset( $occupation_font['family'] ) && ! empty( $occupation_font['family'] ) ) {
				$css .= 'font-family:' . $occupation_font['family'] . ';';
			}
			if ( isset( $occupation_font['style'] ) && ! empty( $occupation_font['style'] ) ) {
				$css .= 'font-style:' . $occupation_font['style'] . ';';
			}
			if ( isset( $occupation_font['weight'] ) && ! empty( $occupation_font['weight'] ) && 'regular' !== $occupation_font['weight'] ) {
				$css .= 'font-weight:' . $occupation_font['weight'] . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['occupationFont'] ) && is_array( $attr['occupationFont'] ) && isset( $attr['occupationFont'][0] ) && is_array( $attr['occupationFont'][0] ) && ( ( isset( $attr['occupationFont'][0]['size'] ) && is_array( $attr['occupationFont'][0]['size'] ) && isset( $attr['occupationFont'][0]['size'][1] ) && ! empty( $attr['occupationFont'][0]['size'][1] ) ) || ( isset( $attr['occupationFont'][0]['lineHeight'] ) && is_array( $attr['occupationFont'][0]['lineHeight'] ) && isset( $attr['occupationFont'][0]['lineHeight'][1] ) && ! empty( $attr['occupationFont'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-occupation {';
			if ( isset( $attr['occupationFont'][0]['size'][1] ) && ! empty( $attr['occupationFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['occupationFont'][0]['size'][1] . ( ! isset( $attr['occupationFont'][0]['sizeType'] ) ? 'px' : $attr['occupationFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['occupationFont'][0]['lineHeight'][1] ) && ! empty( $attr['occupationFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['occupationFont'][0]['lineHeight'][1] . ( ! isset( $attr['occupationFont'][0]['lineType'] ) ? 'px' : $attr['occupationFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}

		if ( isset( $attr['occupationFont'] ) && is_array( $attr['occupationFont'] ) && isset( $attr['occupationFont'][0] ) && is_array( $attr['occupationFont'][0] ) && ( ( isset( $attr['occupationFont'][0]['size'] ) && is_array( $attr['occupationFont'][0]['size'] ) && isset( $attr['occupationFont'][0]['size'][2] ) && ! empty( $attr['occupationFont'][0]['size'][2] ) ) || ( isset( $attr['occupationFont'][0]['lineHeight'] ) && is_array( $attr['occupationFont'][0]['lineHeight'] ) && isset( $attr['occupationFont'][0]['lineHeight'][2] ) && ! empty( $attr['occupationFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.ive-blocks-testimonials-wrap' . $unique_id . ' .ive-testimonial-occupation {';
			if ( isset( $attr['occupationFont'][0]['size'][2] ) && ! empty( $attr['occupationFont'][0]['size'][2] ) ) {
				$css .= 'font-size:' . $attr['occupationFont'][0]['size'][2] . ( ! isset( $attr['occupationFont'][0]['sizeType'] ) ? 'px' : $attr['occupationFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['occupationFont'][0]['lineHeight'][2] ) && ! empty( $attr['occupationFont'][0]['lineHeight'][2] ) ) {
				$css .= 'line-height:' . $attr['occupationFont'][0]['lineHeight'][2] . ( ! isset( $attr['occupationFont'][0]['lineType'] ) ? 'px' : $attr['occupationFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		return $css;
	}


	/**
	 * Adds var to color output if needed.
	 *
	 * @param string $color the output color.
	 */
	public function ibtana_color_output( $color, $opacity = null ) {
		if ( strpos( $color, 'palette' ) === 0 ) {
			$color = 'var(--global-' . $color . ')';
		} else if ( isset( $opacity ) && is_numeric( $opacity ) ) {
			$color = $this->hex2rgba( $color, $opacity );
		}
		return $color;
	}

}
Ibtana_Blocks_Frontend::get_instance();
