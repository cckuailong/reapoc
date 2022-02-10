<?php
/**
 * Shortcode Display Class
 *
 * Contains all the functions for the diplay purposes! (Generates CSS, CSS Classes, HTML Attributes...)
 *
 * @since 2.19
 */

namespace CustomFacebookFeed;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Autolink;
use CustomFacebookFeed\CFF_Parse;


if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class CFF_Shortcode_Display {

	/**
	 * Ajax  Loaded
	 *
	 * Check if is Ajax Loaded then put the script inside the content
	 *
	 * @since 2.19
	 */
	public function ajax_loaded(){
		$ajax_loaded_content = '';
		$ajax_theme = CFF_Utils::check_if_on( $this->atts['ajax'] );
	    if ($ajax_theme) {
	        $cff_min  = isset( $this->options[ 'cff_minify' ] ) ? '.min' : '';
	        $cff_link_hashtags = CFF_Utils::check_if_on( $this->atts['textlink'] ) ? 'false' : CFF_Utils::check_if_on( $this->atts['linkhashtags'] );
	        $ajax_loaded_content .= '<script type="text/javascript">var cfflinkhashtags = "' . $cff_link_hashtags . '";</script>';
	        $ajax_loaded_content .= '<script type="text/javascript" src="' . CFF_PLUGIN_URL . 'assets/js/cff-scripts'.$cff_min.'.js?ver='.CFFVER . '"></script>';
	    }

	    return $ajax_loaded_content;
	}


	/**
	 * Style Compiler.
	 *
	 * Returns an array containing all the styles for the Feed
	 *
	 * @since 2.19
	 * @return String
	 */
	public function style_compiler( $style_array ){
		$style = '';
		foreach ($style_array as $single_style) {
			if( !empty($single_style['value']) && $single_style['value'] != '#' && $single_style['value'] != 'inherit' && $single_style['value'] !== '0' ){
				$style .= 	$single_style['css_name'] . ':' .
							(isset($single_style['pref']) ? $single_style['pref'] : '') .
							$single_style['value'] .
							(isset($single_style['suff']) ? $single_style['suff'] : '') .
							';';
			}
		}
		$style = ( !empty($style) ) ? ' style="' . $style . '" ' : '';
		return $style;
	}

	/**
	 * CSS Class Compiler .
	 *
	 * Returns custom CSS classes for the CFF list container shortcode
	 *
	 * @since 2.19
	 * @return Array
	 */
	public function feed_style_class_compiler(){
		$result = [
			'cff_custom_class' => '',
			'cff_feed_styles' => '',
			'cff_feed_attributes' => ''
		];
		//Set to be 100% width on mobile?
	    $cff_feed_width_resp = CFF_Utils::check_if_on( $this->atts['widthresp'] );
	    $cff_feed_height = CFF_Utils::get_css_distance( $this->atts[ 'height' ] ) ;

		//Disable default CSS styles?
	     $cff_disable_styles =  CFF_Utils::get_css_distance( $this->atts[ 'disablestyles' ] ) ;

		$cff_class = $this->atts['class'];
	    //Masonry
	    $cff_cols = $this->atts['cols'];
		$colstablet = (int)$this->atts['colstablet'];
		$cff_cols_mobile = $this->atts['colsmobile'];
	    $cff_cols_js = $this->atts['colsjs'];

	    $masonry = ( intval($cff_cols) > 1 ) ? true :  false;
	    $js_only = isset( $cff_cols_js ) ? $cff_cols_js : false;
	    if( $js_only === 'false' ) $js_only = false;

	    if( $masonry || $masonry == 'true' ){
	    	$this->atts['headeroutside'] = true;
		}
	    $masonry_classes = '';
	    if( isset($masonry) ) {
	        if( $masonry === 'on' || $masonry === true || $masonry === 'true' ) {
	            $masonry_classes .= 'cff-masonry';
	            $masonry_classes .= ( $cff_cols != 3 ) 			? sprintf( ' masonry-%s-desktop', $cff_cols ) : '';
	            $masonry_classes .= ( $cff_cols_mobile == 2 ) 	? ' masonry-2-mobile' : '';
		        if( $cff_cols != 3 ) {
			        $masonry_classes .= sprintf( ' masonry-%s-desktop', $cff_cols );
		        }
		        if( $cff_cols_mobile > 1 ) {
			        $masonry_classes .= sprintf( ' masonry-%s-mobile', $cff_cols_mobile );
		        }
		        if( $colstablet > 1 ) {
			        $masonry_classes .= sprintf( ' masonry-%s-tablet', $colstablet );
		        }
	            $masonry_classes .= ( ! $js_only ) 				? ' cff-masonry-css' : ' cff-masonry-js';
	        }
	    }

		$mobile_cols_class = '';
		if (! empty( $this->atts['colsmobile'] ) && (int)$this->atts['colsmobile'] > 0) {
			$mobile_cols_class = ' cff-mob-cols-' . (int)$this->atts['colsmobile'];
		}

		$tablet_cols_class = '';
		if (! empty( $this->atts['colstablet'] )
		    && (int)$this->atts['colstablet'] > 0
		    && (int)$this->atts['colstablet'] !== 2) {
			$tablet_cols_class = ' cff-tab-cols-' . (int)$this->atts['colstablet'];
		}

	    //If there's a class then add it here
	    $css_classes_string = '';

	    if( !empty($cff_class) || !empty($cff_feed_height) || !$cff_disable_styles || $cff_feed_width_resp || !empty($masonry_classes) ){
	    	$css_classes_string .= ( !empty($cff_class) ) 			? $cff_class 				: '';
	    	$css_classes_string .= ( !empty($masonry_classes) ) 	? $masonry_classes 			: '';
	    	$css_classes_string .= ( !empty($cff_feed_height) ) 	? ' cff-fixed-height ' 		: '';
	    	$css_classes_string .= ( $cff_feed_width_resp ) 		? ' cff-width-resp ' 		: '';
	    	$css_classes_string .= ( !$cff_disable_styles ) 			? ' cff-default-styles ' 	: '';
	    }
		$css_classes_string .= $mobile_cols_class . $tablet_cols_class;
	    if ( ! empty( $this->atts['paletteclass'] ) ) {
		    $css_classes_string .= ' ' . $this->atts['paletteclass'];
	    }

	    $css_classes_string = ( !empty($css_classes_string) ) ? ' class="cff cff-list-container '.$css_classes_string.'" ' : 'class="cff cff-list-container"';

		$title_limit = !isset($title_limit) ? $this->atts['textlength'] : 9999;
	    $attributes_string = ' data-char="'.$title_limit.'" ';
	    $mobile_num = isset( $this->atts['nummobile'] ) && (int)$this->atts['nummobile'] !== (int)$this->atts['num'] ? (int)$this->atts['nummobile'] : false;

	    $attributes_string .= ( $mobile_num ) ? ' data-nummobile="' . $mobile_num . '" data-pag-num="' . (int)$this->atts['num'] . '" ' :  '';
	    if ( CFF_GDPR_Integrations::doing_gdpr( $this->atts ) ) {
			$attributes_string .= ' data-cff-flags="gdpr" ';
		}


		$result = [
			'cff_custom_class' => $css_classes_string,
			'cff_feed_styles' => $this->get_style_attribute( 'feed_global' ),
			'cff_feed_attributes' => $attributes_string
		];
		return $result;
	}


	/**
	 * Item HTML Attributes
	 *
	 * Returns the post item feed class atrribute
	 *
	 * @since 2.19
	 * @return Array
	 */
	public function get_item_attributes($cff_post_type, $cff_album, $cff_post_bg_color_check, $cff_post_style, $cff_box_shadow, $name, $cff_post_id){
		#extract($args);
		$item_class = 'cff-item ';
		if ($cff_post_type == 'link') 			$item_class .= 'cff-link-item ';
		else if ($cff_post_type == 'event') 	$item_class .= 'cff-timeline-event ';
		else if ($cff_post_type == 'photo') 	$item_class .= 'cff-photo-post ';
		else if ($cff_post_type == 'video') 	$item_class .= 'cff-video-post ';
		else if ($cff_post_type == 'swf') 		$item_class .= 'cff-swf-post ';
		else if ($cff_post_type == 'offer') 	$item_class .= 'cff-offer-post ';
		else 									$item_class .= 'cff-status-post ';

		$item_class .= $cff_album ? 'cff-album ' : '';
		$item_class .= ($cff_post_bg_color_check || $cff_post_style == "boxed")  ? 'cff-box ' : '';
		$item_class .= $cff_box_shadow ? 'cff-shadow ' : '';
		$item_class .= isset($name) ? 'author-'. CFF_Utils::cff_to_slug($name) : '';


		return [
			'class' => 'class="'. $item_class .'"',
			'id' 	=> 'id="cff_'. $cff_post_id .'"',
			'style' => $this->get_post_item_style()
		];
	}

	/**
	 * Item CLass Attributes
	 *
	 * Returns the post item feed style attribute
	 *
	 * @since 2.19
	 * @return String
	 */
	public function get_post_item_style(){
		$item_style = '';
		if( $this->atts['poststyle'] == 'regular' ){
			$item_style = ' style="border-bottom: '. CFF_Utils::return_value( $this->atts[ 'sepsize' ] , 0). 'px solid #'. str_replace('#', '', CFF_Utils::return_value( $this->atts[ 'sepcolor' ] , 'ddd')) . ';"';
		}else if( $this->atts['poststyle'] == 'boxed' ){
			$item_style_array  = [
				['css_name' => 'border-radius', 'value' => $this->atts['postcorners'] , 'suff' => 'px'],
				['css_name' => 'background-color', 'value' => str_replace('#', '', $this->atts['postbgcolor']), 'pref' => '#']
			];
			$item_style = $this->style_compiler( $item_style_array );
		}
		return $item_style;
	}

	/**
	 *
	 * Style Attribute
	 * Generates the Style attribute for the Feed Elements
	 *
	 * @since 2.19
	 * @return String
	 */
	public function get_style_attribute( $element ){
		$style_array = [];
		switch ($element) {
			case 'link_box':
				$style_array = [
					['css_name' => 'border', 'value' => str_replace('#', '', $this->atts['linkbordercolor']), 'pref' => ' 1px solid #'],
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->atts['linkbgcolor']), 'pref' => '#']
				];
			break;
			case 'body_description':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['descsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['descweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['desccolor']), 'pref' => '#']
				];
			break;
			case 'feed_global':
				$style_array = [
					['css_name' => 'width', 'value' => CFF_Utils::get_css_distance( $this->atts[ 'width' ] ) ],
				];
			break;
			case 'feed_wrapper_insider':
				$style_array = [
					['css_name' => 'padding', 'value' => CFF_Utils::get_css_distance( $this->atts[ 'padding' ] ) ],
					['css_name' => 'height', 'value' => CFF_Utils::get_css_distance( $this->atts[ 'height' ] ) ],
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->atts[ 'bgcolor' ] ), 'pref' => '#']
				];
			break;
			case 'header':
				$style_array = [
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->atts['headerbg']), 'pref' => '#'],
					['css_name' => 'padding', 'value' => CFF_Utils::get_css_distance( $this->atts['headerpadding'] ) ],
					['css_name' => 'font-size', 'value' => $this->atts['headertextsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['headertextweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['headertextcolor']), 'pref' => '#']
				];
			break;
			case 'header_visual':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['headertextcolor']), 'pref' => '#'],
					['css_name' => 'font-size', 'value' => $this->atts['headertextsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['headertextweight']]
				];
			break;

			case 'header_icon':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['headericoncolor']), 'pref' => '#'],
					['css_name' => 'font-size', 'value' => $this->atts['headericonsize'], 'suff' => 'px']
				];
			break;

            case 'header_bio':
	            $style_array = [
		            ['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['headerbiocolor']), 'pref' => '#'],
		            ['css_name' => 'font-size', 'value' => $this->atts['headerbiosize'], 'suff' => 'px']
	            ];
	            break;
			case 'author':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['authorsize'], 'suff' => 'px'],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['authorcolor']), 'pref' => '#']
				];
			break;
			case 'date':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['datesize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['dateweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['datecolor']), 'pref' => '#']
				];
			break;

			case 'post_link':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['linksize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['linkweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['linkcolor']), 'pref' => '#']
				];
			break;
			case 'event_title':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['eventtitlesize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['eventtitleweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['eventtitlecolor']), 'pref' => '#']
				];
            break;
			case 'post_text':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts['textsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->atts['textweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts['textcolor']), 'pref' => '#']
				];
			break;
			case 'shared_cap_link':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts[ 'linkurlsize' ], 'suff' => 'px'],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts[ 'linkurlcolor' ]), 'pref' => '#']
				];
			break;
			case 'shared_desclink':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->atts[ 'linkdescsize' ], 'suff' => 'px'],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->atts[ 'linkdesccolor' ]), 'pref' => '#']
				];
			break;

		}

		return $this->style_compiler( $style_array );
	}


	/**
	 *
	 * Style Attribute
	 * Generates the Style attribute for the Feed Elements
	 *
	 * @since 2.19
	 * @return String
	 */
	public function check_show_section( $section_name ){
		$is_shown = ( CFF_Utils::stripos($this->atts[ 'include' ], $section_name) !== false ) ? true : false;
		$is_shown = ( CFF_Utils::stripos($this->atts[ 'exclude' ], $section_name) !== false ) ? false : $is_shown;
		return $is_shown;
	}



	/**
	 *
	 * Get Author Template Data
	 * Get Authors the data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */

	static function get_author_name( $news ){
		return isset($news->from->name) ? str_replace('"', "", $news->from->name) : '';
	}

	static function get_author_link_atts( $news, $target, $cff_nofollow, $cff_author_styles ){
	 	return !isset($news->from->link) ? '' : ' href="https://facebook.com/' . $news->from->id . '" '.$target.$cff_nofollow.' '.$cff_author_styles;
	}

	static function get_author_link_el( $news ){
		return !isset($news->from->link) ? 'span' : 'a';
	}

	static function get_author_post_text_story( $post_text_story, $cff_author_name ){
		if( !empty($cff_author_name) ){
			$cff_author_name_pos = strpos($post_text_story, $cff_author_name);
			if ($cff_author_name_pos !== false) {
				$post_text_story = substr_replace($post_text_story, '', $cff_author_name_pos, strlen($cff_author_name));
			}
		}
		return $post_text_story;
	}

	static function get_author_pic_src_class( $news, $atts ){
		$cff_author_src = $cff_author_img_src = isset($news->from->picture->data->url) ? $news->from->picture->data->url : '';
		$img_class = '';
		if ( CFF_GDPR_Integrations::doing_gdpr( $atts ) ){
			$cff_author_img_src = CFF_PLUGIN_URL. '/assets/img/placeholder.png';
			$img_class = ' cff-no-consent';
		}
		return [
			'real_image' 	=> $cff_author_src,
			'image' 		=> $cff_author_img_src,
			'class' 		=> $img_class
		];
	}


	/**
	 *
	 * Get Date Data
	 * Get Date the data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_date( $options, $atts, $news ){
		$cff_date_before = isset($atts[ 'beforedate' ]) && CFF_Utils::check_if_on($atts['beforedateenabled']) ? $atts[ 'beforedate' ] : '';
		$cff_date_after = isset($atts[ 'afterdate' ]) && CFF_Utils::check_if_on($atts['afterdateenabled']) ? $atts[ 'afterdate' ] : '';
		//Timezone. The post date is adjusted by the timezone offset in the cff_getdate function.
		$cff_timezone = $atts['timezone'];

		//Posted ago strings
		$cff_date_translate_strings = array(
			'cff_translate_second' 		=> $atts['secondtext'],
			'cff_translate_seconds' 	=> $atts['secondstext'],
			'cff_translate_minute' 		=> $atts['minutetext'],
			'cff_translate_minutes' 	=> $atts['minutestext'],
			'cff_translate_hour' 		=> $atts['hourtext'],
			'cff_translate_hours' 		=> $atts['hourstext'],
			'cff_translate_day' 		=> $atts['daytext'],
			'cff_translate_days' 		=> $atts['daystext'],
			'cff_translate_week' 		=> $atts['weektext'],
			'cff_translate_weeks' 		=> $atts['weekstext'],
			'cff_translate_month' 		=> $atts['monthtext'],
			'cff_translate_months' 		=> $atts['monthstext'],
			'cff_translate_year' 		=> $atts['yeartext'],
			'cff_translate_years' 		=> $atts['yearstext'],
			'cff_translate_ago' 		=> $atts['agotext']
		);
		$cff_date_formatting 	= $atts[ 'dateformat' ];
		$cff_date_custom 		= $atts[ 'datecustom' ];

		$post_time = isset($news->created_time) ? $news->created_time : '';
		$post_time = isset($news->backdated_time) ? $news->backdated_time : $post_time; //If the post is backdated then use that as the date instead
		return $cff_date_before . ' ' .CFF_Utils::cff_getdate(strtotime($post_time), $cff_date_formatting, $cff_date_custom, $cff_date_translate_strings, $cff_timezone) . ' ' . $cff_date_after;
	}

	/**
	 *
	 * Get Media Link Data
	 * Get the Media link data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_media_link_text( $atts, $cff_post_type, $cff_album ){
		$cff_translate_photo_text = CFF_Utils::return_value($atts['phototext'], esc_html__('Photo', 'custom-facebook-feed'));
		$cff_translate_video_text = CFF_Utils::return_value($atts['videotext'], esc_html__('Video', 'custom-facebook-feed'));
		return ( $cff_post_type == 'photo' || $cff_album ) ? $cff_translate_photo_text : $cff_translate_video_text;
	}

	static function get_media_link_icon( $cff_post_type, $cff_album ){
		return ( $cff_post_type == 'photo' || $cff_album ) ? 'picture-o fa-image' : 'video-camera fa-video';
	}


	/**
	 *
	 * Get Post Link Data
	 * Get the Post link data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_post_link_social_links( $link, $cff_post_text_to_share ){
		return [
			'facebook' => [
				'icon' => 'facebook-square',
				'text' => esc_html__('Share on Facebook', 'custom-facebook-feed'),
				'share_link' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($link)
			],
			'twitter' => [
				'icon' => 'twitter',
				'text' => esc_html__('Share on Twitter', 'custom-facebook-feed'),
				'share_link' => 'https://twitter.com/intent/tweet?text=' . urlencode($link)
			],
			'linkedin' => [
				'icon' => 'linkedin',
				'text' => esc_html__('Share on Linked In', 'custom-facebook-feed'),
				'share_link' => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . urlencode($link) . '&amp;title=' . rawurlencode( strip_tags($cff_post_text_to_share) )
			],
			'email' => [
				'icon' => 'envelope',
				'text' => esc_html__('Share by Email', 'custom-facebook-feed'),
				'share_link' => 'mailto:?subject=Facebook&amp;body=' . urlencode($link) . '%20-%20' . rawurlencode( strip_tags($cff_post_text_to_share) )
			]
		];
	}

	static function get_post_link_text_to_share( $cff_post_text ){
		$cff_post_text_to_share = '';
		if( strpos($cff_post_text, '<span class="cff-expand">') !== false ){
			$cff_post_text_to_share = explode('<span class="cff-expand">', $cff_post_text);
			if( is_array($cff_post_text_to_share) ) $cff_post_text_to_share = $cff_post_text_to_share[0];
		}
		return $cff_post_text_to_share;
	}

	static function get_post_link_text_link( $atts, $cff_post_type ){
		$cff_facebook_link_text = $atts[ 'facebooklinktext' ];
		$link_text = ($cff_facebook_link_text != '' && !empty($cff_facebook_link_text))  ? $cff_facebook_link_text : esc_html__('View on Facebook', 'custom-facebook-feed');
		//If it's an offer post then change the text
		if ($cff_post_type == 'offer') $link_text = esc_html__('View Offer', 'custom-facebook-feed');
		return $link_text;
	}

	static function get_post_link_fb_share_text( $atts ){
		return ( $atts[ 'sharelinktext' ] ) ? $atts[ 'sharelinktext' ]  : esc_html__('Share', 'custom-facebook-feed');
	}


	/**
	 *
	 * Get Post Text Data
	 * Get the Post text data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_post_text_title_format( $atts ){
		return ( empty($atts[ 'textformat' ]) || $atts[ 'textformat' ] == 'p' ) ? 'div' : $atts[ 'textformat' ];
	}

	static function get_post_text_link( $cff_post_type, $this_class, $link, $PostID ){
		return ( $cff_post_type == 'link' || $cff_post_type == 'video' ) ? "https://www.facebook.com/" . $this_class->page_id . "/posts/" . $PostID[1] : $link;
	}

	static function get_post_text_contenttext( $post_text, $cff_linebreak_el, $cff_title_link ){
		//Replace line breaks in text (needed for IE8 and to prevent lost line breaks in HTML minification)
		$post_text = preg_replace("/\r\n|\r|\n/",$cff_linebreak_el, $post_text);
		//If the text is wrapped in a link then don't hyperlink any text within
		if( $cff_title_link ):
			//Remove links from text
			$result = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $post_text);
			return CFF_Utils::cff_wrap_span( $result ) . ' ';;
		else :
			return CFF_Autolink::cff_autolink( $post_text );
		endif;
	}

	static function get_post_text_call_to_actions( $atts, $news, $cff_title_styles, $cff_posttext_link_color, $cff_nofollow_referrer){
		//Add a call to action button if included
		if( isset($news->call_to_action->value->link) ){
			$cff_cta_link = $news->call_to_action->value->link;

			if( $cff_cta_link[0] == '/' ){
				$cff_cta_link = 'https://facebook.com' . $cff_cta_link;
			} else {
				//If it doesn't start with 'http' then add it otherwise the link doesn't work. Don't do this if it's a tel num.
				if (strpos($cff_cta_link, 'http') === false && strpos($cff_cta_link, 'tel:') === false) $cff_cta_link = 'http://' . $cff_cta_link;
			}

			$cff_button_type = $news->call_to_action->type;

			switch ($cff_button_type) {
				case 'SHOP_NOW':
					$cff_cta_button_text = CFF_Utils::return_value( $atts['shopnowtext'], 'Shop Now');
				break;
				case 'MESSAGE_PAGE':
					$cff_cta_button_text = CFF_Utils::return_value( $atts['messagepage'], 'Message Page');
				break;
				case 'LEARN_MORE':
					$cff_cta_button_text = CFF_Utils::return_value( $atts['learnmoretext'], 'Learn More');
				break;
				default:
				$cff_cta_button_text = ucwords(strtolower( str_replace('_',' ',$cff_button_type) ) );
			}

			$cff_app_link = isset($news->call_to_action->value->app_link) ? $news->call_to_action->value->app_link : '';

            // Set the message page cta to use the default messenger link as the API can sometimes send an invalid link
            if ( $cff_button_type == 'MESSAGE_PAGE' ) $cff_cta_link = 'https://m.me/' . $news->from->id;

			//Add the button to the post if the text isn't "NO_BUTTON"
			if( $cff_button_type != 'NO_BUTTON' ):
			?>
				<p class="cff-cta-link" <?php echo $cff_title_styles ?>><a href="<?php echo esc_url($cff_cta_link) ?>" target="_blank" data-app-link="<?php echo $cff_app_link ?>" style="color: #<?php echo $cff_posttext_link_color ?>;" <?php echo $cff_nofollow_referrer ?> ><?php echo $cff_cta_button_text ?></a></p>
			<?php
			endif;
		}
	}


	/**
	 *
	 * Get Shared Link Data
	 * Get the Shared Link data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_shared_link_caption( $news ){
		$cff_link_caption = '';
		if( !empty($news->link) ){
			$cff_link_caption = htmlentities($news->link, ENT_QUOTES, 'UTF-8');
			$cff_link_caption_parts = explode('/', $cff_link_caption);
			if( isset($cff_link_caption_parts[2]) ) $cff_link_caption = $cff_link_caption_parts[2];
		}
		return $cff_link_caption;
	}

	static function get_shared_link_title_format( $atts ){
		return ( empty( $atts[ 'linktitleformat' ] ) ) ? 'p' : $atts[ 'linktitleformat' ];
	}

	static function get_shared_link_title_styles( $atts ){
		return ( !empty($atts[ 'linktitlesize' ]) && $atts[ 'linktitlesize' ] != 'inherit' ) ? 'style="font-size:' . $atts[ 'linktitlesize' ] . 'px;"' : '';
	}


	static function get_shared_link_description_text( $body_limit, $description_text, $cff_title_link, $cff_posttext_link_color ){
		//Truncate desc
		if (!empty($body_limit)) {
			if (strlen($description_text) > $body_limit) $description_text = substr($description_text, 0, $body_limit) . '...';
		}
		if ($cff_title_link) {
		}else{
			$description_text = CFF_Autolink::cff_autolink( htmlspecialchars($description_text), $link_color = $cff_posttext_link_color );
		}
		return $description_text;
	}

	static function get_shared_link_description( $cff_title_link, $description_text ){
		$cff_link_description = '';
		if ($cff_title_link) {
			$cff_link_description =  CFF_Utils::cff_wrap_span( htmlspecialchars($description_text) );
		}else{
			$cff_link_description =  nl2br($description_text);
		}
		return $cff_link_description ;
	}



	/**
	 *
	 * Get Error Message Data
	 * Get the error message data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */

	static function get_error_check( $page_id, $user_id, $access_token ){
		$cff_ppca_check_error = false;
		if( ! get_user_meta($user_id, 'cff_ppca_check_notice_dismiss') ){
			$cff_posts_json_url = 'https://graph.facebook.com/v8.0/'.$page_id.'/posts?limit=1&access_token='.$access_token;
			$transient_name = 'cff_ppca_' . substr($page_id, 0, 5) . substr($page_id, strlen($page_id)-5, 5) . '_' . substr($access_token, 15, 10);
			$cff_cache_time = 1;
			$cache_seconds = YEAR_IN_SECONDS;
			$cff_ppca_check = CFF_Utils::cff_get_set_cache($cff_posts_json_url, $transient_name, $cff_cache_time, $cache_seconds, '', true, $access_token, $backup=false);
			$cff_ppca_check_json = json_decode($cff_ppca_check);

			if( isset( $cff_ppca_check_json->error ) && strpos($cff_ppca_check_json->error->message, 'Public Content Access') !== false ){
				$cff_ppca_check_error = true;
			}
		}
		return $cff_ppca_check_error;
	}

	static function get_error_message_cap( ){
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		return $cap;
	}

	static function get_error_check_ppca( $FBdata ){
		//Is it a PPCA error from the API?
		return ( isset($FBdata->error->message) && strpos($FBdata->error->message, 'Public Content Access') !== false ) ? true : false;
	}



	/**
	 *
	 * Get Likebox Data
	 * Get the likebox data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */

	static function get_likebox_height( $atts, $cff_like_box_small_header, $cff_like_box_faces ){
		$cff_likebox_height = $atts[ 'likeboxheight' ];
		$cff_likebox_height = preg_replace('/px$/', '', $cff_likebox_height);
		//Calculate the like box height
		$cff_likebox_height = 130;
		if( $cff_like_box_small_header == 'true' ) $cff_likebox_height = 70;
		if( $cff_like_box_faces == 'true' ) $cff_likebox_height = 214;
		if( $cff_like_box_small_header == 'true' && $cff_like_box_faces == 'true' ) $cff_likebox_height = 154;
		return $cff_likebox_height;
	}


	static function get_likebox_width( $atts ){
		$cff_likebox_custom_width =  isset( $atts[ 'likeboxcustomwidth' ] ) ? CFF_Utils::check_if_on($atts[ 'likeboxcustomwidth' ]) : false;
		$cff_likebox_width = $cff_likebox_custom_width &&  isset($atts[ 'likeboxwidth' ]) && !empty($atts[ 'likeboxwidth' ]) ? $atts[ 'likeboxwidth' ] : 300;
		return $cff_likebox_width;
	}

	static function get_likebox_classes( $atts ){
		return "cff-likebox" . ( $atts[ 'likeboxoutside' ] ? " cff-outside" : '' ) . ( $atts[ 'likeboxpos' ] == 'top' ? ' cff-top' : ' cff-bottom' );
	}

	static function get_likebox_tag( $atts ){
		return ( $atts[ 'likeboxpos' ] == 'top') ? 'section' : 'div';
	}


	/**
	 *
	 * Get Header Data
	 * Get the Header data for the templates
	 *
	 * @since 2.19
	 * -----------------------------------------
	 */
	static function get_header_txt_classes( $cff_header_outside ){
		return ($cff_header_outside) ? " cff-outside" : '';
	}


	static function get_header_parts( $atts ){
		if ( !empty( $atts['headerinc'] ) || !empty( $atts['headerexclude'] ) ) {
			if ( !empty( $atts['headerinc'] ) ) {
				$header_inc = explode( ',', str_replace( ' ', '', strtolower( $atts['headerinc'] ) ) );
				$cff_header_cover = in_array( 'cover', $header_inc, true );
				$cff_header_name = in_array( 'name', $header_inc, true );
				$cff_header_bio = in_array( 'about', $header_inc, true );
			} else {
				$header_exc = explode( ',', str_replace( ' ', '', strtolower( $atts['headerexclude'] ) ) );
				$cff_header_cover = ! in_array( 'cover', $header_exc, true );
				$cff_header_name = ! in_array( 'name', $header_exc, true );
				$cff_header_bio = ! in_array( 'about', $header_exc, true );
			}
		}else{
			$cff_header_cover = CFF_Utils::check_if_on( $atts['headercover'] );
			$cff_header_name = CFF_Utils::check_if_on( $atts['headername'] );
			$cff_header_bio = CFF_Utils::check_if_on( $atts['headerbio'] );
		}

		return [
			'cover' 		=> $cff_header_cover,
			'name' 			=> $cff_header_name,
			'bio'			=> $cff_header_bio
		];
	}

	static function get_header_height_style( $atts ){
		$cff_header_cover_height = ! empty( $atts['headercoverheight'] ) ? (int)$atts['headercoverheight'] : 300;
		$header_hero_style = $cff_header_cover_height !== 300 ? ' style="height: '.$cff_header_cover_height.'px";' : '';
		return $header_hero_style;
	}

	static function get_header_font_size( $atts ){
		return !empty($atts['headertextsize']) ? 'style="font-size:'. $atts['headertextsize'] .'px;"'  : '';
	}

	static function get_header_link( $header_data, $page_id ){
		$link = CFF_Parse::get_link( $header_data );
		if( $link == 'https://facebook.com' ) $link .= '/'.$page_id;
		return $link;
	}

	public static function avatar_src( $header_data, $atts ) {
		if ( CFF_GDPR_Integrations::doing_gdpr( $atts ) ) {
			return trailingslashit( CFF_PLUGIN_URL ) . 'assets/img/placeholder.png';
		}
		return CFF_Parse::get_avatar( $header_data );
	}

	public static function cover_image_src( $header_data, $atts ) {
		if ( CFF_GDPR_Integrations::doing_gdpr( $atts ) ) {
			return trailingslashit( CFF_PLUGIN_URL ) . 'assets/img/placeholder.png';
		}
		return CFF_Parse::get_cover_source( $header_data );
	}

	/*
	*
	* PRINT THE GDPR NTOCE FOR ADMINS IN THE FRON END
	*
	*/
	static function print_gdpr_notice($element_name, $custom_class = ''){
		if ( ! is_user_logged_in()  || ! current_user_can( 'edit_posts' )) {
			return;
		}
	?>
		<div class="cff-gdpr-notice <?php echo $custom_class; ?>">
			<i class="fa fa-lock" aria-hidden="true"></i>
			<?php echo esc_html__('This notice is visible to admins only.','custom-facebook-feed') ?><br/>
			<?php echo $element_name.' '.esc_html__('disabled due to GDPR setting.','custom-facebook-feed') ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cff-style&tab=misc')); ?>"><?php echo esc_html__('Click here','custom-facebook-feed') ?></a> <?php echo esc_html__('for more info.','custom-facebook-feed') ?>
		</div>
	<?php
	}

}