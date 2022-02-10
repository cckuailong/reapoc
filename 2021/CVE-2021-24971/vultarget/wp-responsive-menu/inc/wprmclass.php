<?php
class WP_Responsive_Menu {

	protected $options = '';

	public $translatables = array(
	  	'search_box_text',
	    'bar_title'
  	);

	/**
	* Bootstraps the class and hooks required actions & filters.
	*/
	public function __construct() {
		add_action( 'wp_enqueue_scripts',  array( $this, 'wprm_enque_scripts' ) );
		add_action( 'wp_footer', array( $this, 'wprmenu_menu' ) );
		
		//Load wp responsive menu settings
		$this->options = get_option( 'wprmenu_options' );

		add_action( 'plugins_loaded', array($this, 'wprmenu_register_strings'));

		add_action( 'wp_ajax_wpr_live_update', array($this, 'wpr_live_update'));

		add_action( 'wp_footer', array($this, 'wpr_custom_css') );

		add_action( 'wp_ajax_wprmenu_import_data', array($this, 'wprmenu_import_data') );

		add_action( 'wp_ajax_wpr_get_transient_from_data', array($this, 'wpr_get_transient_from_data') );
	}

	public function option( $option ){
		if( isset($_COOKIE['wprmenu_live_preview']) 
			&& $_COOKIE['wprmenu_live_preview'] == 'yes' ) {
			$check_transient = get_transient('wpr_live_settings');

			if( $check_transient ) {
				if( isset( $check_transient[$option] ) 
					&& $check_transient[$option] != '' ) {
						return $check_transient[$option];
				}
			}
		}
		else {
			if( isset( $this->options[$option] ) && $this->options[$option] != '' )
				return $this->options[$option];
				return '';
		}
	}

	public function wprmenu_register_strings() {
		if( is_admin() ) :
			if( function_exists('pll_register_string') ) :
				pll_register_string('search_box_text', $this->option('search_box_text'), 'WP Responsive Menu');
      	pll_register_string('bar_title', $this->option('bar_title'), 'WP Responsive Menu');
			endif;
		endif;
	}

	function hex2rgba($color, $opacity = false) {
		$default = 'rgb(0,0,0)';
 
		//Return default if no color provided
		if(empty($color))
    	return $default; 
 
		//Sanitize $color if "#" is provided 
    if ($color[0] == '#' ) {
    	$color = substr( $color, 1 );
    }
 
    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
    	$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
    	$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
    	return $default;
    }
 
    //Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
    	if(abs($opacity) > 1)
    		$opacity = 1.0;
    	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
    	$output = 'rgb('.implode(",",$rgb).')';
    }

    //Return rgb(a) color string
    return $output;
	}

	/**
	*
	* Added inline style to the responsive menu
	*
	*/
	public function inlineCss() {
		$inlinecss = '';
		if( $this->option('enabled') ) :
			$from_width = $this->option('from_width') != '' ? $this->option('from_width') : '768';
			$inlinecss .= '@media only screen and ( max-width: '.$from_width.'px ) {';
			$how_wide = $this->option('how_wide') != '' ? $this->option('how_wide') : '40';
			$menu_max_width = $this->option('menu_max_width');

			$border_top_color = $this->hex2rgba($this->option("menu_border_top"), $this->option("menu_border_top_opacity"));

			$border_bottom_color = $this->hex2rgba($this->option("menu_border_bottom"), $this->option("menu_border_bottom_opacity"));
			$menu_title_font = $this->option('menu_title_size') == '' ? '20' : $this->option('menu_title_size');

			$inlinecss .= 'html body div.wprm-wrapper {
				overflow: scroll;
			}';

			//manu background image
			if( $this->option('menu_bg') != '' ) :
				$inlinecss .= '#mg-wprm-wrap {
					background-image: url( '.$this->option("menu_bg").');
					background-size: '.$this->option("menu_bg_size").';
					background-repeat: '.$this->option("menu_bg_rep").';
			}';
			endif;

			if( $this->option('enable_overlay') == '1' ) :
				$overlay_bg_color = $this->hex2rgba($this->option("menu_bg_overlay_color"), $this->option("menu_background_overlay_opacity"));
				$inlinecss .= 'html body div.wprm-overlay{ background: '.$overlay_bg_color .' }';
			endif;

			if( $this->option('menu_border_bottom_show') == 'yes' ):
				$inlinecss .= '
				#mg-wprm-wrap ul li {
					border-top: solid 1px '.$border_top_color.';
					border-bottom: solid 1px '.$border_bottom_color.';
				}
				';
			endif;

			if( $this->option('menu_bar_bg') != '' ) :
				$inlinecss .= '
					#wprmenu_bar {
					background-image: url( '.$this->option("menu_bar_bg").' );
					background-size: '.$this->option("menu_bar_bg_size").' ;
					background-repeat: '.$this->option("menu_bar_bg_rep").';
				}
				';
			endif;
			if( $menu_title_font > 26 )
				$inlinecss .= '#wprmenu_bar .menu_title a{ top: 0; }';
			$inlinecss .= '
				#wprmenu_bar {
					background-color: '.$this->option("bar_bgd").';
				}
			
				html body div#mg-wprm-wrap .wpr_submit .icon.icon-search {
					color: '.$this->option("search_icon_color").';
				}
				#wprmenu_bar .menu_title, #wprmenu_bar .wprmenu_icon_menu {
					color: '.$this->option("bar_color").';
				}
				#wprmenu_bar .menu_title {
					font-size: '.$menu_title_font.'px;
					font-weight: '.$this->option('menu_title_weight').';
				}
				#mg-wprm-wrap li.menu-item a {
					font-size: '.$this->option('menu_font_size').'px;
					text-transform: '.$this->option('menu_font_text_type').';
					font-weight: '.$this->option('menu_font_weight').';
				}
				#mg-wprm-wrap li.menu-item-has-children ul.sub-menu a {
					font-size: '.$this->option('sub_menu_font_size').'px;
					text-transform: '.$this->option('sub_menu_font_text_type').';
					font-weight: '.$this->option('sub_menu_font_weight').';
				}
				#mg-wprm-wrap li.current-menu-item > a {
					background: '.$this->option('active_menu_bg_color').';
				}
				#mg-wprm-wrap li.current-menu-item > a,
				#mg-wprm-wrap li.current-menu-item span.wprmenu_icon{
					color: '.$this->option('active_menu_color').' !important;
				}
				#mg-wprm-wrap {
					background-color: '.$this->option("menu_bgd").';
				}
				.cbp-spmenu-push-toright {
					left: '.$how_wide.'% ;
				}
				.cbp-spmenu-push-toright .mm-slideout {
					left:'.$how_wide.'% ;
				}
				.cbp-spmenu-push-toleft {
					left: -'.$how_wide.'% ;
				}
				#mg-wprm-wrap.cbp-spmenu-right,
				#mg-wprm-wrap.cbp-spmenu-left,
				#mg-wprm-wrap.cbp-spmenu-right.custom,
				#mg-wprm-wrap.cbp-spmenu-left.custom,
				.cbp-spmenu-vertical {
					width: '.$how_wide.'%;
					max-width: '.$menu_max_width.'px;
				}
				#mg-wprm-wrap ul#wprmenu_menu_ul li.menu-item a,
				div#mg-wprm-wrap ul li span.wprmenu_icon {
					color: '.$this->option("menu_color").' !important;
				}
				#mg-wprm-wrap ul#wprmenu_menu_ul li.menu-item a:hover {
					background: '.$this->option("menu_textovrbgd").';
					color: '.$this->option("menu_color_hover").' !important;
				}
				div#mg-wprm-wrap ul>li:hover>span.wprmenu_icon {
					color: '.$this->option("menu_color_hover").' !important;
				}
				.wprmenu_bar .hamburger-inner, .wprmenu_bar .hamburger-inner::before, .wprmenu_bar .hamburger-inner::after {
					background: '.$this->option("menu_icon_color").';
				}
				.wprmenu_bar .hamburger:hover .hamburger-inner, .wprmenu_bar .hamburger:hover .hamburger-inner::before,
			 .wprmenu_bar .hamburger:hover .hamburger-inner::after {
				background: '.$this->option("menu_icon_hover_color").';
				}
			';

			if( $this->option('menu_symbol_pos') == 'left' ) :
				$inlinecss .= 'div.wprmenu_bar div.hamburger{padding-right: 6px !important;}';
			endif;
			
			if( $this->option("menu_border_bottom_show") == 'no' ):
				$inlinecss .= '
				#wprmenu_menu, #wprmenu_menu ul, #wprmenu_menu li, .wprmenu_no_border_bottom {
					border-bottom:none;
				}
				#wprmenu_menu.wprmenu_levels ul li ul {
					border-top:none;
				}
			';
			endif;

			$inlinecss .= '
				#wprmenu_menu.left {
					width:'.$how_wide.'%;
					left: -'.$how_wide.'%;
					right: auto;
				}
				#wprmenu_menu.right {
					width:'.$how_wide.'%;
					right: -'.$how_wide.'%;
					left: auto;
				}
			';

			if( $this->option("menu_symbol_pos") == 'right' ) :
				$inlinecss .= '
					.wprmenu_bar .hamburger {
						float: '.$this->option("menu_symbol_pos").';
					}
					.wprmenu_bar #custom_menu_icon.hamburger {
						top: '.$this->option("custom_menu_top").'px;
						right: '.$this->option("custom_menu_left").'px;
						float: right;
						background-color: '.$this->option("custom_menu_bg_color").';
					}
				';
			endif;

			if( $this->option('menu_icon_type') == 'default' ) :
				$menu_padding = $this->option("header_menu_height");
				$menu_padding = intval($menu_padding);

				if( $menu_padding > 50 ) {
					$menu_padding = $menu_padding - 27;
					$menu_padding = $menu_padding / 2;
					$top_position = $menu_padding + 30;

					$inlinecss .= 'html body div#wprmenu_bar {
						padding-top: '.$menu_padding.'px;
						padding-bottom: '.$menu_padding.'px;
					}';

					if( $this->option('menu_type') == 'default' ) {
						$inlinecss .= '.wprmenu_bar div.wpr_search form {
							top: '.$top_position.'px;
						}';
					}
				}
				
				$inlinecss .= 'html body div#wprmenu_bar {
					height : '.$this->option("header_menu_height").'px;
				}';
			endif;

			if( $this->option('menu_type') == 'default'  ) :
				$inlinecss .= '#mg-wprm-wrap.cbp-spmenu-left, #mg-wprm-wrap.cbp-spmenu-right, #mg-widgetmenu-wrap.cbp-spmenu-widget-left, #mg-widgetmenu-wrap.cbp-spmenu-widget-right {
					top: '.$this->option("header_menu_height").'px !important;
				}';
			endif;

			if( $this->option("menu_symbol_pos") == 'left' ) :
				$inlinecss .= '
					.wprmenu_bar .hamburger {
						float: '.$this->option("menu_symbol_pos").';
					}
					.wprmenu_bar #custom_menu_icon.hamburger {
						top: '.$this->option("custom_menu_top").'px;
						left: '.$this->option("custom_menu_left").'px;
						float: left !important;
						background-color: '.$this->option("custom_menu_bg_color").';
					}
				';
			endif;
			if( $this->option('hide') != '' ):
				$inlinecss .= $this->option('hide').'{ display: none !important; }';
			endif;
			$inlinecss .= '.custMenu #custom_menu_icon {
				display: block;
			}';
			if( $this->option("menu_type") != 'custom' ) : 
				$inlinecss .= 'html { padding-top: 42px !important; }';
			endif;
			$inlinecss .= '#wprmenu_bar,#mg-wprm-wrap { display: block; }
			div#wpadminbar { position: fixed; }';

			$inlinecss .=	'}';
		endif;
		return $inlinecss;

	}

	/**
	*
	* Add necessary js and css for our wp responsive menu
	*
	* @since 1.0.2
	* @param blank
	* @return array
	*/	
	public function wprm_enque_scripts() {
		//hamburger menu icon style
		wp_enqueue_style( 'hamburger.css' , plugins_url().'/wp-responsive-menu/css/wpr-hamburger.css', array(), '1.0' );
		//menu css
		wp_enqueue_style( 'wprmenu.css' , plugins_url().'/wp-responsive-menu/css/wprmenu.css', array(), '1.0' );

		//menu css
		wp_enqueue_style( 'wpr_icons', plugins_url().'/wp-responsive-menu/inc/icons/style.css', array(),  '1.0' );

		//inline css
    wp_add_inline_style( 'wprmenu.css', $this->inlineCss() );
		
		//mordenizer js
		wp_enqueue_script( 'modernizr', plugins_url(). '/wp-responsive-menu/js/modernizr.custom.js', array( 'jquery' ), '1.0' );

		//touchswipe js
		wp_enqueue_script( 'touchSwipe', plugins_url(). '/wp-responsive-menu/js/jquery.touchSwipe.min.js', array( 'jquery' ), '1.0' );

		//wprmenu js
		wp_enqueue_script('wprmenu.js', plugins_url( '/wp-responsive-menu/js/wprmenu.js'), array( 'jquery', 'touchSwipe' ), '1.0' );
		
		$wpr_options = array(
		 		'zooming' 				=> $this->option('zooming'),
		 		'from_width' 			=> $this->option('from_width'),
		 		'push_width' 			=> $this->option('menu_max_width'),
		 		'menu_width' 			=> $this->option('how_wide'),
		 		'parent_click' 		=> $this->option('parent_click'),
		 		'swipe' 					=> $this->option('swipe'),
		 		'enable_overlay' 	=> $this->option('enable_overlay'),
		 	);
		//Localize necessary variables
		wp_localize_script( 'wprmenu.js', 'wprmenu', $wpr_options );
	}

	/**
	*
	* WordPress deafult search form
	*
	* @since 3.0.4
	* @param blank
	* @return html
	*/
	public function wpr_search_form() {
		$search_placeholder = $this->option('search_box_text');
		$search_placeholder = function_exists('pll__') ? pll__($search_placeholder) : $search_placeholder;
		$unique_id = esc_attr( uniqid( 'search-form-' ) );
		return '<form role="search" method="get" class="wpr-search-form" action="' . site_url() . '"><label for="'.$unique_id.'"></label><input type="search" class="wpr-search-field" placeholder="' . $search_placeholder . '" value="" name="s" title="Search for:"><button type="submit" class="wpr_submit"><i class="wpr-icon-search"></i></button></form>';
	}

	/**
	*
	* Outputs Responsive Menu Html
	*
	* @since 1.0
	* @param empty
	* @return html
	*/	
	public function wprmenu_menu() {
		if( $this->option('enabled') ) :
			$openDirection = $this->option('position');

			$menu_title = $this->option('bar_title');
			$menu_title = function_exists('pll__') ? pll__($menu_title) : $menu_title;

			$menu_icon_animation = $this->option('menu_icon_animation') != '' ? $this->option('menu_icon_animation') : 'hamburger--slider'; 
			?>

			<div class="wprm-wrapper">
			<?php
			if( $this->option('enable_overlay') == '1' ) : ?>
				<div class="wprm-overlay"></div>
			<?php endif; ?>
			
			<?php
			if( $this->option('menu_type') == 'custom' ): ?>
				<div class="wprmenu_bar custMenu <?php echo $this->option('slide_type'); echo ' '.$this->option('position'); ?>">
					<div id="custom_menu_icon" class="hamburger <?php echo $menu_icon_animation; ?>">
  					<span class="hamburger-box">
    					<span class="hamburger-inner"></span>
  					</span>
					</div>
				</div>
			<?php else: ?>
				<div id="wprmenu_bar" class="wprmenu_bar <?php echo $this->option('slide_type'); echo ' '.$this->option('position'); ?>">

					<div class="hamburger <?php echo $menu_icon_animation; ?>">
  						<span class="hamburger-box">
    						<span class="hamburger-inner"></span>
  						</span>
					</div>
					<div class="menu_title">
						<?php 
							if( $this->option('bar_logo') == '' && $this->option('logo_link') !== '' ) : ?>
								<a href="<?php echo $this->option('logo_link'); ?>"><?php echo $menu_title; ?></a>
							<?php else: ?>
								<?php echo $menu_title; ?>
							<?php endif; ?>
						<?php 
						$logo_link = $this->option('logo_link') != '' ? $this->option('logo_link') : get_site_url();
						if( $this->option('bar_logo') != '' ) :
							echo '<a href="'.$logo_link.'"><img class="bar_logo" alt="logo" src="'.$this->option('bar_logo').'"/></a>';
						endif; 
					?>
					</div>
				</div>
			<?php endif; ?>

			<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-<?php echo $openDirection; ?> <?php echo $this->option('menu_type'); ?> " id="mg-wprm-wrap">
				<?php if( $this->option('menu_type') == 'custom' ): ?>
					<div class="menu_title">
						<?php echo $menu_title; ?>
						<?php if( $this->option('bar_logo') ) echo '<img class="bar_logo" alt="logo" src="'.$this->option('bar_logo').'"/>' ?>
					</div>
				<?php endif; ?>

				<?php
				/**
				*
				* After Menu Header Hook
				*
				* @since 3.1
				*/
				do_action('wpr_after_menu_bar'); 
				?>

				<ul id="wprmenu_menu_ul">
					

					<?php
					/* Content Before Menu */
					if( $this->option('content_before_menu_element') !== '' ) {
						$content_before_menu_elements = preg_replace('/\\\\/', '', $this->option('content_before_menu_element'));

						echo '<li class="wprm_before_menu_content">'. $content_before_menu_elements . '</li>';
					}
					?>


					<?php
					$search_position = $this->option('order_menu_items') != '' ? $this->option('order_menu_items') : 'Menu,Search,Social';
					$search_position = explode(',', $search_position);
					foreach( $search_position as $key => $menu_element ) :
						if( $menu_element == 'Menu' ) : 
							$menus = get_terms( 'nav_menu',array( 'hide_empty'=>false ) );
							if( $menus ) : foreach( $menus as $m ) :
								if( $m->term_id == $this->option('menu') ) $menu = $m;
							endforeach; endif;
							
							if( is_object( $menu ) ) :
								wp_nav_menu( array( 'menu'=>$menu->name,'container'=>false,'items_wrap'=>'%3$s' ) );
							endif;
						endif;

						if( $menu_element == 'Search' ) :
							if( $this->option('search_box_menu_block') != '' && $this->option('search_box_menu_block') == 1  ) : 
						?>
							<li>
								<div class="wpr_search search_top">
									<?php echo $this->wpr_search_form(); ?>
								</div>
							</li>
						<?php
						endif;
					endif;
						?>
					<?php
					endforeach;
					 ?>

					<?php
					 /* After Menu Element */
					 if( $this->option('content_after_menu_element') !== '' ) {
						$content_after_menu_element = preg_replace('/\\\\/', '', $this->option('content_after_menu_element'));

						echo '<li class="wprm_after_menu_content">'. $content_after_menu_element . '</li>';
					}
					?>

					
				</ul>

				<?php
				/**
				*
				* After Menu Container Hook
				*
				* @since 3.1
				*/
				do_action('wpr_after_menu_container'); 
				?>
				
				</div>
			</div>
			<?php
		endif;
	}


	/**
	*
	* Show custom css from the plugin settings
	*
	* @since 3.1
	* @param empty
	* @return string
	*/
	public function wpr_custom_css() {
		$wpr_custom_css = $this->option('wpr_custom_css');

		if( !empty($wpr_custom_css) ) :
		?>
		<style type="text/css">
		<?php
			echo '/* WPR Custom CSS */' . "\n";
    	echo $wpr_custom_css . "\n";
    ?>
		</style>
		<?php
		endif;
	}

	/**
	*
	* Save settings into transient
	*
	* @since 3.1
	* @param empty
	* @return array
	*/
	public function wpr_live_update() {
		if( isset($_POST['wprmenu_options']) ) {
			set_transient('wpr_live_settings', $_POST['wprmenu_options'], 60 * 60 * 24);
		}
		wp_die();
	}

	/**
	*
	* Get demo settings from the file
	*
	* @since 3.1
	* @param empty
	* @return json object
	*/
	public function wprmenu_import_data() {
		
		$response = 'error';
		$menu = '';

		if( $this->option('menu') ) {
			$menu = $this->option('menu');
		}
		
		if( isset($_POST) ) {
			$settings_id = isset($_POST['settings_id']) ? $_POST['settings_id'] : '';
			$demo_type = isset($_POST['demo_type']) ? $_POST['demo_type'] : '';

			$demo_id = isset($_POST['demo_id']) ? $_POST['demo_id'] : '';

			if( $settings_id !== '' 
				&& $demo_type !== '' 
				&& $demo_id !== ''  ) {
				$site_name = MG_WPRM_DEMO_SITE_URL;
				$remoteLink = $site_name.'/wp-json/wprmenu-server/v2/type='.$demo_type.'/demo_name='.$demo_id.'/settings_id='.$settings_id;

				$content = wp_remote_get($remoteLink);

				if( is_array($content) 
					&& isset($content['response']) 
					&& $content['response']['code'] == 200  ) {
					
					$content = $content['body'];
					$items = json_decode($content, true);
					
					if( is_array($items) ) {
						$items['menu'] = $menu;
					}

					$content = maybe_serialize($items);

					if( $content ) {
						$response = 'success';

						global $wpdb;
				
						$wpdb->update(
							$wpdb->prefix.'options',
							array(
								'option_value' => $content,
							),
							array(
								'option_name' => 'wprmenu_options',
							)
						);
					}
					else {
						$response = 'error';
					}
				}
				else {
					$response = 'error';
				}
			}
			else {
				$response = 'error';
			}
		}
		else {
			$response = 'error';
		}
		echo json_encode( array('status' => $response) );		
		wp_die();
	}

	/**
	*
	* Get settings from transient and save into options api
	*
	* @since 3.1
	* @param empty
	* @return json object
	*/
	public function wpr_get_transient_from_data() {
		$response = 'error';
		$check_transient = get_transient('wpr_live_settings');
		
		if( $check_transient) {
			$content = maybe_serialize($check_transient);
			update_option('wprmenu_options', $check_transient);
			$response = 'success';
		}
		
		echo json_encode( array('status' => $response) );		
		wp_die();
	}
}