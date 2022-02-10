<?php
/**
 * Wow Company Class
 *
 * @package     Wow_Plugin
 * @subpackage  Includes/Wow_Company
 * @author      Wow-Company <helper@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates the menu in admin panel general for all Wow plugin
 *
 * @property string text_domain - Text domain for translate
 *
 * @since 1.0
 */
final class Wow_Company {

	public function __construct() {

		$this->plugin['text'] = 'button_generator';

		// Functions for Class
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Register the plugin menu on sidebar menu in admin panel.
	 *
	 * @since 1.0
	 */
	public function add_menu() {
		$icon =
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNTEyLjAwMDAwMHB0IiBoZWlnaHQ9IjUxMi4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDUxMi4wMDAwMDAgNTEyLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgo8bWV0YWRhdGE+CkNyZWF0ZWQgYnkgcG90cmFjZSAxLjE1LCB3cml0dGVuIGJ5IFBldGVyIFNlbGluZ2VyIDIwMDEtMjAxNwo8L21ldGFkYXRhPgo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCw1MTIuMDAwMDAwKSBzY2FsZSgwLjEwMDAwMCwtMC4xMDAwMDApIgpmaWxsPSIjMDAwMDAwIiBzdHJva2U9Im5vbmUiPgo8cGF0aCBkPSJNMjQ4MyA1MTAwIGMtNzEgLTQzIC02OCAtMTggLTcxIC01ODUgbC00IC01MTAgLTc3NCAtNTUwIGMtNDI2IC0zMDIKLTc4OCAtNTYzIC04MDQgLTU4MCAtMTMwIC0xMzUgLTcxIC0zNjggMTA5IC00MzIgbDUxIC0xOCAwIC0xMjEyIDAgLTEyMTMgMjA1CjAgMjA1IDAgMCAxMzA5IDAgMTMwOSAzOCAyOSBjMjAgMTcgMjc5IDIwMSA1NzQgNDExIGw1MzcgMzgyIDU1NiAtMzkyIDU1NQotMzkzIDAgLTEzMjcgMCAtMTMyOCAyMDUgMCAyMDUgMCAwIDEyMTAgYzAgOTYwIDMgMTIxMSAxMyAxMjE0IDYgMiAzNSA5IDYyCjE1IDE4MiA0MiAyNjQgMjY2IDE1MiA0MTMgLTIyIDI5IC0yNjkgMjA5IC04MTkgNTk3IGwtNzg3IDU1NiAtMSA4OCAwIDg3IDQ4NQowIDQ4NSAwIC0xMzYgMjAxIC0xMzcgMjAxIDEzMiAxOTMgYzcyIDEwNyAxMzEgMTk2IDEzMSAxOTkgMCAzIC0yMTYgNiAtNDgwIDYKbC00ODAgMCAwIDIzIGMwIDg3IC0xMjcgMTQ2IC0yMDcgOTd6Ii8+CjxwYXRoIGQ9Ik0yMTM2IDI4MjggbC00MTggLTI3MSA1IC00NTYgYzQgLTQ3NSA2IC01MDIgNDkgLTU1NiA0OCAtNjEgMzAgLTYwCjc0NyAtNjMgNzIyIC0zIDc0MyAtMiA3OTcgNTIgNjEgNjEgNjQgODggNjMgNTg1IGwwIDQ0NiAtMzg2IDI1MCBjLTIxMiAxMzgKLTM5OCAyNTggLTQxMyAyNjcgbC0yNiAxOCAtNDE4IC0yNzJ6IG02MzIgLTM1MyBsMjAyIC0xMzAgMCAtMjI3IDAgLTIyOCAtNDE1CjAgLTQxNSAwIDAgMjI4IDAgMjI3IDIwMyAxMzIgYzExMSA3MyAyMDcgMTMyIDIxMyAxMzAgNiAtMSAxMDEgLTYwIDIxMiAtMTMyeiIvPgo8cGF0aCBkPSJNMjQ0NSAxMzQxIGMtMzA1IC00OSAtNTIxIC0yNTIgLTU3MCAtNTM1IC0xMSAtNjggLTE1IC0xNzEgLTE1IC00NDcKbDAgLTM1OSAyMTAgMCAyMTAgMCAwIDM2OCBjMCAzNjMgMCAzNjcgMjQgNDE3IDQxIDg5IDEyMiAxNDUgMjIzIDE1MyAxMTcgOQoyMTQgLTQxIDI2OCAtMTM5IGwzMCAtNTQgMyAtMzczIDMgLTM3MiAyMDQgMCAyMDUgMCAwIDM4OCBjMCA0MzQgLTIgNDQ5IC02OQo1ODcgLTg0IDE3MCAtMjQzIDI5NiAtNDQ2IDM1MSAtNjMgMTcgLTIxNSAyNSAtMjgwIDE1eiIvPgo8L2c+Cjwvc3ZnPgo=';
		add_menu_page( 'Wow Plugins', 'Wow Plugins', 'manage_options', 'wow-company', array(
			$this,
			'main_page',
		), $icon );
		add_submenu_page( 'wow-company', 'About Wow Plugins', 'All Plugins', 'manage_options', 'wow-company' );
	}

	/**
	 * Include the main file
	 */
	public function main_page() {
		require_once 'about/main.php';
		$url_style = plugin_dir_url( __FILE__ ) . 'about/wow-page.min.css';
		// include the main style
		wp_enqueue_style( 'wow-page', $url_style );
	}
}

$wow_plugin = new Wow_Company();
