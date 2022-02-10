<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

defined( 'ABSPATH' ) || exit;

function wpr_optionsframework_option_name() {
	$wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );
	$wpr_optionsframework_settings['id'] = 'wprmenu_options';
	update_option( 'wpr_optionsframework', $wpr_optionsframework_settings );
}

//Google fonts
function wpr_google_fonts() {
  $fonts = array(
    "Arial, Helvetica, sans-serif", "'Arial Black', Gadget, sans-serif", "'Bookman Old Style', serif", "'Comic Sans MS', cursive", "'Comic Sans MS', cursive", "Courier, monospace", "Garamond, serif", "Georgia, serif", "Impact, Charcoal, sans-serif", "Impact, Charcoal, sans-serif", "'Lucida Console', Monaco, monospace", "'Lucida Console', Monaco, monospace", "'Lucida Sans Unicode', 'Lucida Grande', sans-serif", "'MS Sans Serif', Geneva, sans-serif", "'MS Serif', 'New York', sans-serif" , "'Palatino Linotype', 'Book Antiqua', Palatino, serif", "Tahoma,Geneva, sans-serif", "'Times New Roman', Times,serif", "'Trebuchet MS', Helvetica, sans-serif", "Verdana, Geneva, sans-serif");

  $google_fonts = array();

  foreach ( $fonts as $font ) {
    $google_fonts[$font] = $font;
  }
  return $google_fonts;
}


add_filter( 'wpr_optionsframework_menu', 'wpr_add_responsive_menu' );

function wpr_add_responsive_menu( $menu ) {
	$menu['page_title']  = 'WP Responsive Menu';
	$menu['menu_title']  = 'WPR Menu';
	$menu['mode']		     = 'menu';
	$menu['menu_slug']   = 'wp-responsive-menu';
	$menu['position']    = '200';
	return $menu;
}

//get all the pages from site
function menu_pages() {
	$pages = get_pages();
	
	$opt_pages = array();

	if ( is_array( $pages ) && !empty( $pages ) ) {
		foreach ( $pages as $page ) {
			$opt_pages[$page->ID] = $page->post_title;
		}
	}
	return $opt_pages;
}


function wpr_google_web_fonts() {
	$file_path = plugin_dir_path( __FILE__ ) . 'google-web-fonts.php' ;
	if ( file_exists( $file_path ) ) {
		require_once dirname( __FILE__ ) . '/google-web-fonts.php';
		$fonts = get_custom_fonts();
		$fonts = json_decode( $fonts );
		return $fonts;
	}
}

add_action( 'admin_notices', 'wpr_menu_admin_notice__error' );

function wpr_menu_admin_notice__error() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-responsive-menu' ) {
		$menus = wpr_get_menus();

		if( empty( $menus ) ) {
			$class = 'notice notice-error';
			$menu_link = admin_url( 'nav-menus.php?action=edit&menu=0');
			$message = __( 'It looks like you haven\'t created any menu yet. Please create menu from <a href="'.$menu_link.'"> here </a> to use it as your responsive menu.', 'wprmenu' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
		}
	}
}

function wpr_get_menus() {
	$menu = array();
	$menus = get_terms( 'nav_menu',array( 'hide_empty'=>false ) );

	if ( is_array( $menus ) && !empty( $menus ) ) {
		foreach( $menus as $m ) {
			$menu[$m->term_id] = $m->name;
		}
	}
	return $menu;
}

$options = get_option( 'wprmenu_options' );

function wpr_optionsframework_options() {

	$options = array();

  $options[] = array( 'name' => __( 'General', 'wprmenu' ),
    'helper'  => __( 'Setup Features', 'wprmenu' ),
  	'type' => 'heading' );  
		
	$options[] = array( 'name' => __( 'Enable Responsive Menu', 'wprmenu' ),
		'desc' 	=> __( 'Turn on if you want to enable WP Responsive Menu functionality on your site.', 'wprmenu' ),
		'id' 		=> 'enabled',
		'std' 	=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Live Preview', 'wprmenu' ),
		'desc' 	=> __( 'Enable this option to see a live preview of menu directly on your dashboard.', 'wprmenu' ),
		'id' 		=> 'wpr_live_preview',
		'std' 	=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Search Input Within Menu', 'wprmenu' ),
		'desc' 	=> __( 'Enables the display of search input box inside the menu.', 'wprmenu' ),
		'id' 		=> 'search_box_menu_block',
		'std' 	=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Enable Widget Menu', 'wprmenu' ),
		'desc' 	=> __( 'This will enable widget menu', 'wprmenu' ),
		'id' 		=> 'wpr_enable_widget',
		'std' 	=> '0',
		'class' => 'pro-feature',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Search On Menu Bar', 'wprmenu' ),
		'desc' 	=> __( 'Enables the display of search option on menu bar.', 'wprmenu' ),
		'id' 		=> 'search_box_menubar',
		'std' 	=> '0',
		'class'	=> 'pro-feature',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Enable RTL Mode', 'wprmenu' ),
		'desc' 	=> __( 'If your site uses RTL styles then enable it.', 'wprmenu' ),
		'id' 		=> 'rtlview',
		'class'	=> 'pro-feature',
		'std' 	=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Keep Submenus Open', 'wprmenu' ),
		'desc' 	=> __( 'This option allows you to keep the submenus opened by default.', 'wprmenu' ),
		'id' 		=> 'submenu_opened',
		'class' => 'pro-feature',
		'std'		=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Enable Full Width Menu Container', 'wprmenu' ),
		'desc' 	=> __( 'This option makes the menu container in full width.', 'wprmenu' ),
		'id' 		=> 'fullwidth_menu_container',
		'class' => 'pro-feature',
		'std'		=> '0',
		'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Select Menu', 'wprmenu' ),
		'desc' 	=> __( 'Select the menu you want to display for mobile devices.', 'wprmenu' ),
		'id' 		=> 'menu',
		'std' 	=> '',
		'class' => 'mini',
		'options' => wpr_get_menus(),
		'type' 	=> 'radio' );

	$options[] = array( 'name' => __( 'Elements To Hide On Mobile', 'wprmenu' ),
		'desc' 	=> __( 'Enter the css class/ids for different elements you want to hide on mobile separated by a comma( , ). Example: .nav,#main-menu', 'wprmenu' ),
		'id' 		=> 'hide',
		'std' 	=> '',
		'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Search Box Placeholder', 'wprmenu' ),
		'desc' => __( 'Enter the text that would be displayed on the search box placeholder.', 'wprmenu' ),
		'id' 		=> 'search_box_text',
		'std' 	=> 'Search...',
		'type' 	=> 'text' );
	
	$options[] = array( 'name' => __( 'Menu Title Text', 'wprmenu' ),
		'id' 		=> 'bar_title',
		'std' 	=> 'MENU',
		'class' => 'mini',
		'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Menu Bar Logo', 'wprmenu' ),
	'id' 			=> 'bar_logo',
	'std' 		=> '',
	'type' 		=> 'upload' );

	$options[] = array( 'name' => __( 'Menu Logo Position', 'wprmenu' ),
	'desc' 		=> __( 'Position of logo on menu bar', 'wprmenu' ),
	'id' 			=> 'bar_logo_pos',
	'std' 		=> 'left',
	'class' 	=> 'pro-feature',
	'options' => array( 'left' => 'Left','center' => 'Center' ),
	'type' 		=> 'radio' );

	$options[] = array('name' => __( 'Logo link / Title link', 'wprmenu' ),
		'desc' 	=> __( 'Enter custom link you would like to open when clicking on the logo or title. If no link has been entered your site link would be use by default', 'wprmenu' ),
		'id' 		=> 'logo_link',
		'std' 	=> site_url(),
		'type' 	=> 'text');

	$options[] = array( 'name' => __( 'Swipe', 'wprmenu' ),
		'desc' 	=> __( 'Enables swipe gesture to open/close menus, Only applicable for left/right menu.', 'wprmenu' ),
		'id'      => 'swipe',
    'std'     => 'yes',
    'type'    => 'checkbox' );

	$options[] = array( 'name' => __( 'Zoom On Mobile Devices', 'wprmenu' ),
		'desc' 	=> __( 'Enable/Disable zoom on mobile devices', 'wprmenu' ),
		'id'      => 'zooming',
    'std'     => '0',
    'type'    => 'checkbox' );

	$options[] = array('name' => __( 'Open Submenu On Parent Click' , 'wprmenu' ),
		'desc' 	=> __( 'Enable this option if you would like to open submenu when clicking on the parent menu.', 'wprmenu' ),
		'id' 		=> 'parent_click',
		'std' 	=> '0',
		'type' 	=> 'checkbox');

	//Show the options if WooCommerce is installed and activated
	if( WPR_Menu_Loader::check_woocommerce_installed() ) :

		$options[] = array( 'name' => __( 'WooCommerce integration', 'wprmenu' ),
		  'desc' 		=> __( 'This option integrates WooCommerce option', 'wprmenu' ),
			'id' 			=> 'woocommerce_integration',
			'std' 		=> '0',
			'class' 	=> 'pro-feature',
			'type' 		=> 'checkbox' );

		$options[] = array( 'name' => __( 'Search Woocommerce products', 'wprmenu' ),
			'desc' 			=> __( 'This will only search products from your woocommerce store', 'wprmenu' ),
			'id' 				=> 'woocommerce_product_search',
			'std' 			=> 'no',
			'class' 		=> 'pro-feature',
			'type' 			=> 'checkbox' );

	endif;

	$options[] = array('name' => __( 'Hide Menu On Pages', 'wprmenu' ),
		'id' 					=> 'hide_menu_pages',
		'options' 		=> menu_pages(),
		'desc' 				=> __( 'Select the pages where you want to hide the responsive menu', 'wprmenu' ),
		'class' 			=> 'pro-feature',
		'type' 				=> 'hidemenupages');

	$options[] = array( 'name' => __( 'Create External File For CSS', 'wprmenu' ),
			'desc' 			=> __( 'This will create an external css file for the responsive menu', 'wprmenu' ),
			'id' 				=> 'wpr_enable_external_css',
			'std' 			=> '0',
			'class' 		=> 'pro-feature',
			'type' 			=> 'checkbox' );

	$options[] = array( 'name' => __( 'Minify External CSS', 'wprmenu' ),
			'desc' 			=> __( 'This will minify external responsive menu css file', 'wprmenu' ),
			'id' 				=> 'wpr_enable_minify',
			'std' 			=> '0',
			'class' 		=> 'pro-feature',
			'type' 			=> 'checkbox' );

	$options[] = array( 'name' => __( 'Custom CSS', 'wprmenu' ),
			'desc' 			=> __( 'Put your custom css here', 'wprmenu' ),
			'id' 				=> 'wpr_custom_css',
			'type' 			=> 'code' );

	$options[] = array( 'name' => __( 'Content Before Menu Element', 'wprmenu' ),
		'desc' 				=> __( 'Set custom content before menu elements.', 'wprmenu' ),
		'id' 					=> 'content_before_menu_element',
		'std' 				=> '',
		'type' 				=> 'editor' );

	$options[] = array( 'name' => __( 'Content After Menu Element', 'wprmenu' ),
		'desc' 				=> __( 'Set custom content after menu elements.', 'wprmenu' ),
		'id' 					=> 'content_after_menu_element',
		'std' 				=> '',
		'type' 				=> 'editor' );

		
	$options[] = array( 'name' => __( 'Appearance', 'wprmenu' ),
    'helper'  => __( 'Customize Looks', 'wprmenu' ),
		'type' => 'heading' );

	$options[] = array( 'name' => __( 'Header Menu Height', 'wprmenu' ),
		'id' 				=> 'header_menu_height',
		'desc' 			=> __( 'This will be the height for the menu bar', 'wprmenu' ),
		'class'			=> 'mini',
		'std' 			=> '42',
		'type' 			=> 'text' );

	$options[] = array( 'name' => __( 'Menu Style', 'wprmenu' ),
		'desc' 			=> __( 'Menu style with bar will show menu with bar and Icon style will show only menu icon', 'wprmenu' ),
		'id' 				=> 'menu_type',
		'std' 			=> 'default',
		'options' 	=> array( 'default' => 'Default','custom' => 'Custom' ),
		'type' 			=> 'radio' );

	$options[] = array( 'name' => __( 'Menu Icon Position From Top', 'wprmenu' ),
		'desc' 			=> __( 'Enter the menu icon position from top in px( Eg. 10px ).', 'wprmenu' ),
		'id' 				=> 'custom_menu_top',
		'class' 		=> 'mini',
		'std' 			=> '0',
		'type' 			=> 'text' );

	$options[] = array( 'name' => __( 'Menu Icon Direction', 'wprmenu' ),
		'id' 				=> 'menu_symbol_pos',
		'std' 			=> 'left',
		'class' 		=> 'mini',
		'options' 	=> array( 'left' => 'Left','right' => 'Right' ),
		'type' 			=> 'radio' );

	$options[] = array( 'name' => __( 'Menu Icon Horizontal Distance', 'wprmenu' ),
    'desc'    => __( 'Enter the menu icon distance from left/right based on direction chosen in px(Eg. 10px).', 'wprmenu' ),
    'id'      => 'custom_menu_left',
    'class'   => 'mini',
    'suffix'  => 'px',
    'std'     => '0',
    'type'    => 'text' );

	$options[] = array( 'name' => __( 'Menu Icon Background Color', 'wprmenu' ),
		'desc' 			=> __( 'Select custom menu icon background color.', 'wprmenu' ),
		'id' 				=> 'custom_menu_bg_color',
		'std' 			=> '#CCCCCC',
		'type' 			=> 'color' );
	
	$options[] = array( 'name' => __( 'Menu Icon Animation', 'wprmenu' ),
	'desc' 				=> __( 'Select the animation for menu icon', 'wprmenu' ),
	'id' 					=> 'menu_icon_animation',
	'options' 		=> array( 'hamburger--3dx' => '3DX','hamburger--3dx-r' => '3DX Reverse', 'hamburger--3dy' => '3DY', 'hamburger--3dy-r' => '3DY Reverse', 'hamburger--3dxy' => '3DXY', 'hamburger--3dxy-r' => '3DXY Reverse', 'hamburger--boring' => 'Boring', 'hamburger--collapse' => 'Collapse', 'hamburger--collapse-r' => 'Collapse Reverse', 'hamburger--elastic' => 'Elastic', 'hamburger--elastic-r' => 'Elastic Reverse', 'hamburger--minus' => 'Minus', 'hamburger--slider' => 'Slider', 'hamburger--slider-r' => 'Slider Reverse', 'hamburger--spring' => 'Spring', 'hamburger--spring-r' => 'Spring Reverse', 'hamburger--stand' => 'Stand', 'hamburger--stand-r' => 'Stand Reverse', 'hamburger--spin' => 'Spin', 'hamburger--spin-r' => 'Spin Reverse', 'hamburger--squeeze' => 'Squeeze', 'hamburger--vortex' => 'Vortex', 'hamburger--vortex-r' => 'Vortex Reverse' ),
	'std' => 'hamburger--slider',
	'type' => 'select' );


	$options[] = array( 'name' => __( 'Menu Slide Style', 'wprmenu' ),
	'id' 			=> 'slide_type',
	'std' 		=> 'bodyslide',
	'class' 	=> 'mini',
	'options' => array( 'normalslide' => 'Slide menu', 'bodyslide' => 'Push menu' ),
	'type' 		=> 'radio' );

	$options[] = array( 'name' => __( 'Menu Open Direction', 'wprmenu' ),
	'desc' 		=> __( 'Select the direction from where menu will open.', 'wprmenu' ),
	'id' 			=> 'position',
	'std' 		=> 'left',
	'class' 	=> 'mini',
	'options' => array( 'left' => 'Left','right' => 'Right', 'top' => 'Top', 'bottom' => 'Bottom' ),
	'type' 		=> 'radio' );

	$options[] = array( 'name' => __( 'Display Menu From Width', 'wprmenu' ),
	'desc' 		=> __( 'Enter the width (Eg. 768) below which the responsive menu will be visible on screen. Enter 6000 if you want to display menu on desktop/laptops.', 'wprmenu' ),
	'id' 			=> 'from_width',
	'std' 		=> '768',
	'class' 	=> 'mini',
  'suffix'  => 'px',
	'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Menu Container Width(%)', 'wprmenu' ),
	'id' 			=> 'how_wide',
	'std' 		=> '80',
	'class' 	=> 'mini',
	'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Menu Container Max Width', 'wprmenu' ),
	'desc' 		=> __( 'Enter menu container max width(px).', 'wprmenu' ),
	'id' 			=> 'menu_max_width',
	'std' 		=> '400',
  'suffix'  => 'px',
	'class' 	=> 'mini',
	'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Menu Title Font Size', 'wprmenu' ),
		'id' 		=> 'menu_title_size',
		'std' 	=> '20',
		'class' => 'mini',
    'suffix'  => 'px',
		'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Menu Title Font Weight', 'wprmenu' ),
		'id' 			=> 'menu_title_weight',
		'std' 		=> 'normal',
		'options' => array('100' => '100', '200' => '200', '300' =>'300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900', 'bold' => 'Bold', 'bolder' => 'Bolder', 'lighter' => 'Lighter' ,'normal' => 'Normal'),
		'type' 		=> 'select' );

	$options[] = array( 'name' => __( 'Menu Item Font Size', 'wprmenu' ),
		'desc' 	=> __( 'Enter the font size for main menu items.', 'wprmenu' ),
		'id' 		=> 'menu_font_size',
		'std' 	=> '15',
    'suffix'  => 'px',
		'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Menu Item Font Weight', 'wprmenu' ),
		'desc' 	=> __( 'Enter the font weight for main menu elements', 'wprmenu' ),
		'id' 		=> 'menu_font_weight',
		'std' 	=> 'normal',
		'options' => array('100' => '100', '200' => '200', '300' =>'300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900', 'bold' => 'Bold', 'bolder' => 'Bolder', 'lighter' => 'Lighter' ,'normal' => 'Normal'),
		'type' 	=> 'select' );

	$options[] = array( 'name' => __( 'Menu Item Text Style', 'wprmenu' ),
		'id' 		=> 'menu_font_text_type',
		'std' 	=> 'uppercase',
		'options' => array( 'none' => 'None', 'capitalize' => 'Capitalize', 'uppercase' =>'Uppercase', 'lowercase' => 'Lowercase' ),
		'type' 	=> 'select' );

	$options[] = array( 'name' => __( 'Submenu Alignment', 'wprmenu'),
		'desc' 	=> __( 'Select the text alignment of submenu items.', 'wprmenu' ),
		'id' 			=> 'submenu_alignment',
		'std' 		=> 'left',
		'class' 	=> 'mini pro-feature',
		'options' => array( 'left' => 'Left','right' => 'Right', 'center' => 'Center' ),
		'type' 		=> 'radio');
		
	$options[] = array( 'name' => __( 'Submenu Font Size', 'wprmenu' ),
		'desc' 	=> __( 'Enter the font size for submenu items.', 'wprmenu' ),
		'id' 		=> 'sub_menu_font_size',
		'std' 	=> '15',
    'suffix'  => 'px',
		'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Submenu Font Weight', 'wprmenu' ),
		'desc' => __( 'Enter the font weight for sub menu elements', 'wprmenu' ),
		'id' 	=> 'sub_menu_font_weight',
		'std' => 'normal',
		'options' => array( '100' => '100', '200' => '200', '300' =>'300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900', 'bold' => 'Bold', 'bolder' => 'Bolder', 'lighter' => 'Lighter' ,'normal' => 'Normal' ),
		'type' => 'select' );

	$options[] = array( 'name' => __( 'Submenu Text Style', 'wprmenu' ),
		'id' 		=> 'sub_menu_font_text_type',
		'std' 	=> 'uppercase',
		'options' => array( 'none' => 'None', 'capitalize' => 'Capitalize', 'uppercase' =>'Uppercase', 'lowercase' => 'Lowercase'),
		'type'  => 'select' );


	$options[] = array( 'name' => __( 'Cart Quantity Text Size', 'wprmenu' ),
	'id' 		=> 'cart_contents_bubble_text_size',
	'std' 	=> '12',
	'class' => 'pro-feature',
	'type' 	=> 'text' );

	$options[] = array( 'name' => __( 'Borders For Menu Items', 'wprmenu' ),
	'desc' 	=> __( 'Enable to show border for main menu items', 'wprmenu' ),
	'id' 		=> 'menu_border_bottom_show',
	'std' 	=> 'yes',
	'type' 	=> 'checkbox' );

	$options[] = array( 'name' => __( 'Menu border top opacity', 'wprmenu' ),
	'id' 		=> 'menu_border_top_opacity',
	'std' 	=> '0.05',
	'type' 	=> 'text');

	$options[] = array('name' => __( 'Menu border bottom opacity', 'wprmenu' ),
	'id' 		=> 'menu_border_bottom_opacity',
	'std' 	=> '0.05',
	'type' 	=> 'text');

	$options[] = array( 'name' => __( 'Menu container background image', 'wprmenu' ),
	'id' 		=> 'menu_bg',
	'std' 	=> '',
	'type' 	=> 'upload' );

	$options[] = array( 'name' => __( 'Menu container background size', 'wprmenu' ),
	'id' 			=> 'menu_bg_size',
	'std' 		=> 'cover',
	'options' => array( 'contain' => 'Contain','cover' => 'Cover','100%' => '100%' ),
	'type' 		=> 'radio' );

	$options[] = array( 'name' => __( 'Menu container background repeat', 'wprmenu' ),
	'id' 		=> 'menu_bg_rep',
	'std' 	=> 'repeat',
	'options' => array( 'repeat' => 'Repeat','no-repeat' => 'No repeat' ),
		'type' => 'radio' );

	$options[] = array( 'name' => __( 'Menu Bar Background image', 'wprmenu' ),
	'id' 		=> 'menu_bar_bg',
	'std' 	=> '',
	'type' 	=> 'upload' );

	$options[] = array( 'name' => __( 'Menu Bar Background Size', 'wprmenu' ),
	'id' 			=> 'menu_bar_bg_size',
	'std' 		=> 'cover',
	'options' => array( 'contain' => 'Contain', 'cover' => 'Cover', '100%' => '100%' ),
	'type' => 'radio' );

	$options[] = array( 'name' => __( 'Menu Bar Background Repeat', 'wprmenu' ),
	'id' 			=> 'menu_bar_bg_rep',
	'std' 		=> 'repeat',
	'options' => array( 'repeat' => 'Repeat','no-repeat' => 'No repeat' ),
	'type' 		=> 'radio' );

	$options[] = array( 'name' => __( 'Enable Menu Background Overlay', 'wprmenu' ),
		'desc' 		=> __( 'Turn on if you want to enable Menu Background Overlay.', 'wprmenu' ),
		'id' 			=> 'enable_overlay',
		'std' 		=> '0',
		'type' 		=> 'checkbox' );

	$options[] = array( 'name' => __( 'Menu Background Overlay Opacity', 'wprmenu' ),
		'desc' 		=> __( 'Set menu background overlay opacity.', 'wprmenu' ),
		'id' 			=> 'menu_background_overlay_opacity',
		'std' 		=> '0.83',
		'type' 		=> 'text' );

  $options[] = array( 'name' => __( 'Hide Menu Bar On Scroll', 'wprmenu' ),
    'desc'    => __( 'Turn on if you want to hide menu bar on scroll.', 'wprmenu' ),
    'id'      => 'hide_menubar_on_scroll',
    'std'     => '0',
    'class'   => 'pro-feature',
    'type'    => 'checkbox' );

	$options[] = array( 'name' => __( 'Menu elements position', 'wprmenu' ),
	'desc' 		=> __( 'Drag and drop to reorder the menu elements.', 'wprmenu' ),
	'id' 			=> 'order_menu_items',
	'type' 		=> 'menusort' );

	$options[] = array( 'name' => __( 'Widget Menu', 'wprmenu' ),
    'helper'  => __( 'Custom Widget Menu', 'wprmenu' ),
    'type'    => 'heading' );

	$options[] = array('name' => __( 'Widget Menu Icon', 'wprmenu' ),
		'id' 			=> 'widget_menu_icon',
		'std' 		=> 'wpr-icon-menu',
		'desc' 		=> __( 'Set widget menu icon', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
		'type' 		=> 'icon');

	$options[] = array('name' => __( 'Widget Menu Close Icon', 'wprmenu' ),
		'id' 			=> 'widget_menu_close_icon',
		'std' 		=> 'wpr-icon-cancel2',
		'desc' 		=> __( 'Set widget menu close icon', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
		'type' 		=> 'icon');

	$options[] = array( 'name' => __( 'Wiget Menu Icon Size', 'wprmenu' ),
		'desc' 		=> __( 'Set widget menu font size', 'wprmenu' ),
		'id' 			=> 'widget_menu_font_size',
		'desc' 		=> __( 'Set widget menu icon size', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
		'std' 		=> '28',
		'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Wiget Menu Top Position', 'wprmenu' ),
		'desc' 		=> __( 'Set widget menu position from top', 'wprmenu' ),
		'id' 			=> 'widget_menu_top_position',
		'class' 	=> 'mini pro-feature',
		'std' 		=> '0',
		'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Widget Menu Icon Color', 'wprmenu' ),
		'id'			=> 'widget_menu_icon_color',
		'std'			=> '#FFFFFF',
		'class' 	=> 'mini pro-feature',
		'desc' 		=> __( 'Set widget menu icon color', 'wprmenu' ),
		'type'		=> 'color' );

	$options[] = array( 'name' => __( 'Widget Menu Close Icon Color', 'wprmenu' ),
		'desc' 			=> __( 'Select the direction from where widget menu will open.', 'wprmenu' ),
		'id'			=> 'widget_menu_icon_active_color',
		'class' 	=> 'mini pro-feature',
		'std'			=> '#FFFFFF',
		'desc' 		=> __( 'Set widget menu close icon color', 'wprmenu' ),
		'type'		=> 'color' );

	$options[] = array( 'name' => __( 'Widget Menu Elements Background Color', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
		'id'			=> 'widget_menu_bg_color',
		'desc' 		=> __( 'Set widget menu elements background color', 'wprmenu' ),
		'std'			=> '#c82d2d',
		'type'		=> 'color' );

	$options[] = array( 'name' => __( 'Widget Menu Elements Color', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
		'desc' 		=> __( 'Set widget menu elements font color', 'wprmenu' ),
		'id'			=> 'widget_menu_text_color',
		'std'			=> '#FFFFFF',
		'type'		=> 'color' );

	$options[] = array( 'name' => __( 'Widget Menu Open Direction', 'wprmenu' ),
		'class' 	=> 'mini pro-feature',
	'desc' 			=> __( 'Select the direction from where widget menu will open.', 'wprmenu' ),
	'id' 				=> 'widget_menu_open_direction',
	'std' 			=> 'left',
	'options' 	=> array( 'left' => 'Left','right' => 'Right', 'top' => 'Top', 'bottom' => 'Bottom' ),
	'type' 			=> 'radio' );

	$options[] = array( 'name' => __( 'Color', 'wprmenu' ),
    'helper'  => __( 'Color Settings', 'wprmenu' ),
    'type'    => 'heading' );

	$options[] = array( 'name' => __( 'Menu Bar Background', 'wprmenu' ),
	'id' 		=> 'bar_bgd',
	'std' 	=> '#C92C2C',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Menu Bar Text Color', 'wprmenu' ),
	'id' 		=> 'bar_color',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );
	
	$options[] = array( 'name' => __( 'Menu Container background', 'wprmenu' ),
	'id' 		=> 'menu_bgd',
	'std' 	=> '#c82d2d',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Menu Item Text', 'wprmenu' ),
	'id' 		=> 'menu_color',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );
	
	$options[] = array( 'name' => __( 'Menu Item Text Hover', 'wprmenu' ),
	'id' 		=> 'menu_color_hover',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );	

	$options[] = array( 'name' => __( 'Menu Item Hover Background', 'wprmenu' ),
	'id' 		=> 'menu_textovrbgd',
	'std' 	=> '#d53f3f',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Active Menu Item', 'wprmenu' ),
	'id' 		=> 'active_menu_color',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Active Menu Item Background', 'wprmenu' ),
	'id' 		=> 'active_menu_bg_color',
	'std' 	=> '#d53f3f',
	'type' 	=> 'color' );
	
	$options[] = array( 'name' => __( 'Menu Icon Color', 'wprmenu' ),
	'id' 		=> 'menu_icon_color',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Menu Icon Hover/Focus', 'wprmenu' ),
	'id' 		=> 'menu_icon_hover_color',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );
	
	$options[] = array('name' => __( 'Menu Border Top', 'wprmenu' ),
	'id' 		=> 'menu_border_top',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color');
	
	$options[] = array('name' => __( 'Menu Border Bottom', 'wprmenu' ),
	'id' 		=> 'menu_border_bottom',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color');

	$options[] = array( 'name' => __( 'Social Icon', 'wprmenu' ),
	'id' 		=> 'social_icon_color',
	'std' 	=> '#FFFFFF',
	'class' => 'pro-feature',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Social Icon Hover', 'wprmenu' ),
	'id' 		=> 'social_icon_hover_color',
	'class' => 'pro-feature',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Search icon', 'wprmenu' ),
	'id' 		=> 'search_icon_color',
	'class' => 'pro-feature',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );

	$options[] = array( 'name' => __( 'Search Icon Hover', 'wprmenu' ),
	'id' 		=> 'search_icon_hover_color',
	'class' => 'pro-feature',
	'std' 	=> '#FFFFFF',
	'type' 	=> 'color' );

	//Show options if woocommerce is installed and activated
	if( WPR_Menu_Loader::check_woocommerce_installed() ) :

		$options[] = array( 'name' => __( 'Cart Icon', 'wprmenu' ),
		'id' 		=> 'cart_icon_color',
		'class' => 'pro-feature',
		'std' 	=> '#FFFFFF',
		'type' 	=> 'color' );

		$options[] = array( 'name' => __( 'Cart Icon Hover', 'wprmenu' ),
		'id' 		=> 'cart_icon_active_color',
		'class' => 'pro-feature',
		'std' 	=> '#FFFFFF',
		'type' 	=> 'color' );

		$options[] = array( 'name' => __( 'Cart Quantity Text', 'wprmenu' ),
		'id' 		=> 'cart_contents_bubble_text_color',
		'std' 	=> '#FFFFFF',
		'class' => 'pro-feature',
		'type' 	=> 'color' );

		$options[] = array( 'name' => __( 'Cart Quantity Background', 'wprmenu' ),
		'id' 		=> 'cart_contents_bubble_color',
		'std' 	=> '#d53f3f',
		'class' => 'pro-feature',
		'type' 	=> 'color' );

		$options[] = array( 'name' => __( 'Menu Background Overlay Color', 'wprmenu' ),
		'id'			=> 'menu_bg_overlay_color',
		'std'			=> '#000000',
		'type'		=> 'color' );

	endif;

	$options[] = array( 'name' => __( 'Fonts', 'wprmenu' ),
    'helper'  => __( 'Setup Fonts', 'wprmenu' ),
    'type'    => 'heading', 
  );

	$options[] = array( 'name' => __( 'Google Font Type', 'wprmenu' ),
	'desc' 		=> __( 'Select Font Type', 'wprmenu' ),
	'class' 	=> 'wpr_font_type pro-feature',
	'id' 			=> 'google_font_type',
	'std' 		=> '',
	'options' => array( 'standard' => 'Standard', 'web_fonts' => 'Web Fonts' ),
	'type' 		=> 'select' );	

	$options[] = array( 'name' => __( 'Font Family', 'wprmenu' ),
	'class' 	=> 'wpr_font_family pro-feature',
	'id' 			=> 'google_font_family',
	'std' 		=> '',
	'options' => wpr_google_fonts(),
	'type' 		=> 'select' );

	$options[] = array( 'name' => __( 'Web Font Family', 'wprmenu' ),
	'class' 	=> 'wpr_web_font_family pro-feature',
	'id' 			=> 'google_web_font_family',
	'std' 		=> '',
	'options' => wpr_google_web_fonts(),
	'type' 		=> 'select' );


	$options[] = array( 'name' => __( 'Icons', 'wprmenu' ),
    'helper'  => __( 'Icons Settings', 'wprmenu' ),
    'type'    => 'heading' );

	$options[] = array( 'name' => __( 'Menu Icon Type', 'wprmenu' ),
		'id' 			=> 'menu_icon_type',
		'std' 		=> 'default',
		'class' 	=> 'pro-feature',
		'options' => array( 'default' => 'Default','custom' => 'Custom' ),
		'type' 		=> 'radio' );

	$options[] = array( 'name' => __( 'Menu Icon Font Size', 'wprmenu' ),
		'id' 			=> 'custom_menu_font_size',
		'class'		=> 'mini pro-feature',
		'std' 		=> '40',
    'suffix'  => 'px',
		'type' 		=> 'text' );

	$options[] = array( 'name' => __( 'Menu Top Position', 'wprmenu' ),
		'desc' 		=> __( 'Menu icon position from top', 'wprmenu' ),
		'id' 			=> 'custom_menu_icon_top',
		'class'		=> 'mini pro-feature',
		'std' 		=> '-7',
		'type' 		=> 'text' );

	$options[] = array('name' => __( 'Menu Icon', 'wprmenu' ),
		'id' 			=> 'menu_icon',
		'std' 		=> 'wpr-icon-menu',
		'class' 	=> 'mini pro-feature',
		'type' 		=> 'icon');

	$options[] = array('name' => __( 'Menu Active Icon', 'wprmenu' ),
		'id' 			=> 'menu_close_icon',
		'std' 		=> 'wpr-icon-cancel2',
		'class' 	=> 'mini pro-feature',
		'type' 		=> 'icon');

	$options[] = array('name' => __( 'Submenu Open Icon', 'wprmenu' ),
		'desc' 		=> __( 'This icon will appear for the menu items having submenu. Which will be used to expand the submenu.', 'wprmenu' ),
		'id' 			=> 'submenu_open_icon',
		'std' 		=> 'wpr-icon-plus',
		'class' 	=> 'mini pro-feature',
		'type' 		=> 'icon');

	$options[] = array('name' => __( 'Submenu Close Icon', 'wprmenu' ),
		'desc' 		=> __( 'This icon will appear for closing an expanded submenu.', 'wprmenu' ),
		'id' 			=> 'submenu_close_icon',
		'class' 	=> 'mini pro-feature',
		'std' 		=> 'wpr-icon-minus',
		'type' 		=> 'icon');
	
	$options[] = array('name' => __( 'Search Icon', 'wprmenu' ),
		'id' 			=> 'search_icon',
		'class' 	=> 'mini pro-feature',
		'std' 		=> 'wpr-icon-search',
		'type' 		=> 'icon');

	//Show option if woocommerce is installed and activated
	if( WPR_Menu_Loader::check_woocommerce_installed() ) :

		$options[] = array('name' => __( 'Cart icon', 'wprmenu' ),
			'desc' 		=> __( 'Select Cart icon', 'wprmenu' ),
			'id' 			=> 'cart-icon',
			'class' 	=> 'mini pro-feature',
			'std' 		=> 'wpr-icon-cart',
			'type' 		=> 'icon');

	endif;

	$options[] = array( 'name' => __( 'Social', 'wprmenu' ),
    'helper'  => __( 'Social Features', 'wprmenu' ),
    'type'    => 'heading' );

	$options[] = array( 'name' => __( 'Social Icon Font Size', 'wprmenu' ),
		'id' 		=> 'social_icon_font_size',
		'class'	=> 'mini pro-feature',
		'std' 	=> '16',
		'type' 	=> 'text' );
	
	$options[] = array('name' => __( 'Add Your Social Links', 'wprmenu' ),
		'id' 		=> 'social',
		'class' => 'pro-feature',
		'std' 	=> '',
		'type' 	=> 'social');
  
    return $options;
}