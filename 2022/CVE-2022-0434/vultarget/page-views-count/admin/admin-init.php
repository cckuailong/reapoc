<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
A3rev Plugin Admin Init

TABLE OF CONTENTS

- var admin_menu
- init()
- set_default_settings()
- get_all_settings()
- add_admin_menu()
- register_admin_menu()
- add_admin_pages()
- admin_settings_tab()

-----------------------------------------------------------------------------------*/

class Admin_Init extends Admin_UI
{

	/**
	 * @var array
	 */
	public $admin_menu = array();

	/*-----------------------------------------------------------------------------------*/
	/* init() */
	/* Admin Init */
	/*-----------------------------------------------------------------------------------*/
	public function init() {

		add_action( 'plugins_loaded', array( $this, 'add_admin_menu' ), 7 );

		// Filter to add admin pages for Amin UI process
		add_filter( $this->plugin_name . '_admin_pages', array( $this, 'add_admin_pages' ) );

		$menu_hook = 'admin_menu';
		add_action( $menu_hook, array( $this, 'register_admin_menu' ) );

		add_action( 'plugins_loaded', array( $this, 'get_all_settings' ), 8 );
	}

	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings() */
	/* Called when plugin just installed */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {

		do_action( $this->plugin_name . '_set_default_settings' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* get_all_settings() */
	/* Get all settings when plugin is loaded */
	/*-----------------------------------------------------------------------------------*/
	public function get_all_settings() {

		do_action( $this->plugin_name . '_get_all_settings' );
	}

	/**
	 * add_admin_menu()
	 * Add the Admin menu
	 * =============================================
	 * check add_menu_page() function at http://codex.wordpress.org/Function_Reference/add_menu_page to know the parameters need to parse for type is menu
	 * check add_submenu_page() function at http://codex.wordpress.org/Function_Reference/add_submenu_page to know the parameters need to parse for type is submenu
	 * array (
	 *	array (
	 *		'type'			=> 'menu' 					: (required) add it as main menu on left sidebar of Dashboard
	 *		'page_title'	=> 'Quotes & Orders Mode' 	: (required) The text to be displayed in the title tags of the page when the menu is selected
	 *		'menu_title'	=> 'Quotes & Orders Mode' 	: (required) The text to be displayed in the title tags of the page when the menu is selected
	 *		'capability'	=> 'manage_options'			: (required) The capability required for this menu to be displayed to the user.
	 *														View instruction at http://codex.wordpress.org/Roles_and_Capabilities
	 *		'menu_slug'		=> 'quotes-orders-mode'		: (required) The slug name to refer to this menu by.
	 *		'function'		=> 'my_function_name'		: (required) The function that displays the page content for the menu page.
	 *		'icon_url'		=> 'http://my_icon.png'		: (optional) The url to the icon to be used for this menu.
	 *		'position'		=> 50						: (optional) The position in the menu order this menu should appear.
	 *														By default, if this parameter is omitted, the menu will appear at the bottom of the menu structure.
	 *		'admin_url'		=> 'admin.php'				: (required) The admin url : admin.php , options-general.php
	 * 		'callback_function'=> 'my_callback_function': (optional) The callback function is called when this page does not have tab
	 *		'script_function' => 'my_script_function'	: (optional) The script function that only include for this page.
	 *		'view_doc'		=> ''						: (optional) Support html. The text show on the heading
	 * 	),
	 *	array (
	 *		'type'			=> 'submenu' 				: (required) add it as sub menu on left sidebar of Dashboard
	 *		'parent_slug'	=> 'quotes-orders-mode'		: (required) The slug name for the parent menu.
	 *		'page_title'	=> 'Quotes & Orders Mode' 	: (required) The text to be displayed in the title tags of the page when the menu is selected
	 *		'menu_title'	=> 'Quotes & Orders Mode' 	: (required) The text to be displayed in the title tags of the page when the menu is selected
	 *		'capability'	=> 'manage_options'			: (required) The capability required for this menu to be displayed to the user.
	 *														View instruction at http://codex.wordpress.org/Roles_and_Capabilities
	 *		'menu_slug'		=> 'quotes-orders-mode'		: (required) The slug name to refer to this menu by.
	 *		'function'		=> 'my_function_name'		: (required) The function that displays the page content for the menu page.
	 *		'admin_url'		=> 'admin.php'				: (required) The admin url : admin.php , options-general.php
	 * 		'callback_function'=> 'my_callback_function': (optional) The callback function is called when this page does not have tab
	 *		'script_function' => 'my_script_function'	: (optional) The script function that only include for this page.
	 *		'view_doc'		=> ''						: (optional) Support html. The text show on the heading
	 * 	)
	 * )
	 *
	 */
	public function add_admin_menu() {

		$this->admin_menu = apply_filters( $this->plugin_name . '_add_admin_menu', $this->admin_menu );
	}

	/*-----------------------------------------------------------------------------------*/
	/* register_admin_menu() */
	/* Setup the Admin menu in WordPress
	/*-----------------------------------------------------------------------------------*/
	public function register_admin_menu() {

		if ( is_array( $this->admin_menu ) && count( $this->admin_menu ) > 0 ) {
			foreach ( $this->admin_menu as $menu_item ) {
				if ( ! isset( $menu_item['type'] ) || trim( $menu_item['type'] ) == '' ) continue;

				switch( $menu_item['type'] ) {

					case 'menu':

						$menu_page = add_menu_page( esc_html( $menu_item['page_title'] ), $menu_item['menu_title'], $menu_item['capability'], $menu_item['menu_slug'] , $menu_item['function'], $menu_item['icon_url'], $menu_item['position'] );

						if ( isset( $menu_item['script_function'] ) && trim( $menu_item['script_function'] ) != ''  )
							add_action( "admin_print_scripts-" . $menu_page, $menu_item['script_function'] );

					break;

					case 'submenu':

						$submenu_page = add_submenu_page( $menu_item['parent_slug'] , esc_html( $menu_item['page_title'] ), $menu_item['menu_title'], $menu_item['capability'], $menu_item['menu_slug'] , $menu_item['function'] );

						if ( isset( $menu_item['script_function'] ) && trim( $menu_item['script_function'] ) != ''  )
							add_action( "admin_print_scripts-" . $submenu_page, $menu_item['script_function'] );

					break;
				}
			}

		}

	}

	/*-----------------------------------------------------------------------------------*/
	/* add_admin_pages() */
	/* Get list page for Admin UI can include scripts and styles at header and footer
	/*-----------------------------------------------------------------------------------*/
	public function add_admin_pages( $admin_pages ) {

		if ( ! is_array( $admin_pages ) ) $admin_pages = array();

		if ( is_array( $this->admin_menu ) && count( $this->admin_menu ) > 0 ) {
			foreach ( $this->admin_menu as $menu_item ) {
				if ( ! isset( $menu_item['type'] ) || trim( $menu_item['type'] ) == '' ) continue;

				switch( $menu_item['type'] ) {

					case 'menu':
					case 'submenu':
						if ( ! in_array( $menu_item['menu_slug'], $admin_pages ) )
							$admin_pages[] = $menu_item['menu_slug'];
					break;
				}
			}

		}

		return $admin_pages;
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page Layout
	/*-----------------------------------------------------------------------------------*/
	public function admin_settings_page( $page_data = array() ) {
		global $current_tab;

		if ( ! is_array( $page_data ) || count( $page_data ) < 1 ) return;

		$current_page = $page_data['menu_slug'];

	    do_action( $this->plugin_name . '_page_start' );
		do_action( $this->plugin_name . '-' . $current_page . '_page_start' );

	    ?>
        <div class="wrap">
            <div class="icon32 icon32-a3rev-ui-settings icon32-a3rev<?php echo esc_attr( $current_page ); ?>" id="icon32-a3rev<?php echo esc_attr( $current_page ); ?>"><br /></div>
            <?php
				$tabs = apply_filters( $this->plugin_name . '-' . $current_page . '_settings_tabs_array', array() );

			if ( ! is_array( $tabs ) || count( $tabs ) < 1 ) {
			?>
			<h1>
			<?php
					if ( $page_data !== false) {
						echo esc_html( $page_data['page_title'] );
						if ( isset( $page_data['view_doc'] ) ) echo $page_data['view_doc'];
					}
			?>
			</h1>
            <div style="width:100%; float:left;">
            <?php if ( isset( $page_data['callback_function'] ) && ! empty( $page_data['callback_function'] ) ) call_user_func( $page_data['callback_function'] ); ?>
            </div>
            <?php
				} else {
			?>
            <h2 class="nav-tab-wrapper">
			<?php
					// Get current tab
					$current_tab 		= ( empty( $_GET['tab'] ) ) ? '' : sanitize_text_field( urldecode( $_GET['tab'] ) );

					$activated_first_tab = false;
					$tab_data = false;
					foreach ( $tabs as $tab ) {
						echo '<a href="' . add_query_arg( array( 'page' => $current_page, 'tab' => $tab['name'] ), admin_url( $page_data['admin_url'] ) ) . '" class="nav-tab ';
						if ( $current_tab == '' && $activated_first_tab === false ) {
							echo 'nav-tab-active';
							$activated_first_tab = true;
							$current_tab = $tab['name'];
							$tab_data = $tab;
						} elseif ( $current_tab == $tab['name'] ) {
							echo 'nav-tab-active';
							$tab_data = $tab;
						}
						echo ' ' . esc_attr( sanitize_title( $tab['name'] ) );
						echo '">' . esc_html( $tab['label'] ) . '</a>';
					}

					do_action( $this->plugin_name . '-' . $current_page . '_settings_tabs' );
			?>
            </h2>
            <div style="width:100%; float:left;">
            <?php
					if ( $tab_data !== false && isset ( $tab_data['callback_function'] ) && !empty( $tab_data['callback_function'] ) ) {
						call_user_func( $tab_data['callback_function'] );
					} else {
						do_action( $this->plugin_name . '-' . $current_page . '_settings_tabs_' . $current_tab );
					}
			?>
			</div>
			<?php
				}
            ?>
            <div style="clear:both; margin-bottom:20px;"></div>
        </div>
		<?php

		do_action( $this->plugin_name . '_page_end' );
		do_action( $this->plugin_name . '-' . $current_page . '_page_end' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_tab() */
	/* Show Settings Tab Layout
	/*-----------------------------------------------------------------------------------*/
	public function admin_settings_tab( $current_page = '', $tab_data = array()  ) {
		global $current_subtab;

		if ( ! is_array( $tab_data ) || count( $tab_data ) < 1 ) return;

		$current_tab = $tab_data['name'];

	    do_action( $this->plugin_name . '-' . $current_page . '_tab_start' );
		do_action( $this->plugin_name . '-' . $current_tab . '_tab_start' );

		$subtabs = apply_filters( $this->plugin_name . '-' . $current_tab . '_settings_subtabs_array', array() );

		if ( is_array( $subtabs ) && count( $subtabs ) > 0 ) {

		?>
        <div class="a3_subsubsub_section">
        	<ul class="subsubsub">
        <?php
			// Get current subtab
			$current_subtab 	= ( empty( $_REQUEST['subtab'] ) ) ? '' : sanitize_text_field( urldecode( str_replace('#', '', $_REQUEST['subtab'] ) ) );
			$separate_text = '';
			$activated_first_subtab = false;
			foreach ( $subtabs as $subtab ) {
				echo '<li>' . $separate_text . '<a href="#' . trim( esc_attr( $subtab['name'] ) ) . '" class="';
				if ( $current_subtab == '' && $activated_first_subtab === false ) {
					echo 'current';
					$activated_first_subtab = true;
					$current_subtab = $subtab['name'];
				} elseif ( $current_subtab == $subtab['name'] ) {
					echo 'current';
				}
				echo '">' . esc_html( $subtab['label'] ) . '</a></li>' . "\n";

				$separate_text = ' | ';
			}

			do_action( $this->plugin_name . '-' . $current_tab . '_settings_subtabs' );
		?>
            </ul>
            <br class="clear">
        <?php
			foreach ( $subtabs as $subtab ) {
		?>
        	<div class="section" id="<?php echo trim( esc_attr( $subtab['name'] ) ); ?>">
            <?php if ( isset( $subtab['callback_function'] ) && !empty( $subtab['callback_function'] ) ) call_user_func( $subtab['callback_function'] ); ?>
            </div>
        <?php
			}

			do_action( $this->plugin_name . '-' . $current_tab . '_settings_subtabs_content' );
		?>

		</div>
		<?php
		}

		do_action( $this->plugin_name . '-' . $current_page . '_tab_end' );
		do_action( $this->plugin_name . '-' . $current_tab . '_tab_end' );
	}
}

}
