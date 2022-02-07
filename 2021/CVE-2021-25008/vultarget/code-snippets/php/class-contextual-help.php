<?php

/**
 * This file holds all of the content for the contextual help screens
 * @package Code_Snippets
 */
class Code_Snippets_Contextual_Help {

	/**
	 * @var WP_Screen
	 */
	public $screen;

	/**
	 * @var string
	 */
	public $screen_name;

	/**
	 * @param string $screen_name
	 */
	function __construct( $screen_name ) {
		$this->screen_name = $screen_name;
	}

	/**
	 * Load the contextual help
	 */
	public function load() {
		$this->screen = get_current_screen();

		if ( method_exists( $this, "load_{$this->screen_name}_help" ) ) {
			call_user_func( array( $this, "load_{$this->screen_name}_help" ) );
		}

		$this->load_help_sidebar();
	}

	/**
	 * Load the help sidebar
	 */
	private function load_help_sidebar() {

		$this->screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'code-snippets' ) . '</strong></p>' .
			'<p><a href="https://wordpress.org/plugins/code-snippets">' . __( 'About Plugin', 'code-snippets' ) . '</a></p>' .
			'<p><a href="https://wordpress.org/plugins/code-snippets/faq">' . __( 'FAQ', 'code-snippets' ) . '</a></p>' .
			'<p><a href="https://wordpress.org/support/plugin/code-snippets">' . __( 'Support Forum', 'code-snippets' ) . '</a></p>' .
			'<p><a href="https://sheabunge.com/plugins/code-snippets">' . __( 'Plugin Website', 'code-snippets' ) . '</a></p>'
		);
	}

	/**
	 * Reusable introduction text
	 * @return string
	 */
	private function get_intro_text() {
		return __( 'Snippets are similar to plugins - they both extend and expand the functionality of WordPress. Snippets are more light-weight, just a few lines of code, and do not put as much load on your server.', 'code-snippets' );
	}

	/**
	 * Register and handle the help tabs for the manage snippets admin page
	 */
	private function load_manage_help() {

		$this->screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview', 'code-snippets' ),
			'content' => '<p>' . $this->get_intro_text() .
			             __( ' Here you can manage your existing snippets and perform tasks on them such as activating, deactivating, deleting and exporting.', 'code-snippets' ) . '</p>',
		) );

		$this->screen->add_help_tab( array(
			'id'      => 'safe-mode',
			'title'   => __( 'Safe Mode', 'code-snippets' ),
			'content' =>
				'<p>' . __( 'Be sure to check your snippets for errors before you activate them, as a faulty snippet could bring your whole blog down. If your site starts doing strange things, deactivate all your snippets and activate them one at a time.', 'code-snippets' ) . '</p>' .
				'<p>' . __( "If something goes wrong with a snippet and you can't use WordPress, you can cause all snippets to stop executing by adding <code>define('CODE_SNIPPETS_SAFE_MODE', true);</code> to your <code>wp-config.php</code> file. After you have deactivated the offending snippet, you can turn off safe mode by removing this line or replacing <strong>true</strong> with <strong>false</strong>.", 'code-snippets' ) . '</p>',
		) );
	}

	/**
	 * Register and handle the help tabs for the single snippet admin page
	 */
	private function load_edit_help() {

		$this->screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview', 'code-snippets' ),
			'content' => '<p>' . $this->get_intro_text() . __( ' Here you can add a new snippet, or edit an existing one.', 'code-snippets' ) . '</p>',
		) );

		$snippet_host_links = array(
			__( 'WP Function Me', 'code-snippets' ) => __( 'https://www.wpfunction.me', 'code-snippets' ),
			__( 'CSS-Tricks', 'code-snippets' ) => __( 'https://css-tricks.com/snippets/wordpress/', 'code-snippets' ),
			__( 'WordPress Stack Exchange', 'code-snippets' ) => __( 'https://wordpress.stackexchange.com/', 'code-snippets' ),
			__( 'WP Beginner', 'code-snippets' ) => __( 'https://www.wpbeginner.com/category/wp-tutorials/', 'code-snippets' ),
			__( 'GenerateWP', 'code-snippets' ) => __( 'https://generatewp.com', 'code-snippets' ),
		);

		$snippet_host_list = '';
		foreach ( $snippet_host_links as $title => $link ) {
			$snippet_host_list .= sprintf( '<li><a href="%s">%s</a></li>', esc_url( $link ), esc_html( $title ) );
		}

		$this->screen->add_help_tab( array(
			'id'      => 'finding',
			'title'   => __( 'Finding Snippets', 'code-snippets' ),
			'content' =>
				'<p>' . __( 'Here are some links to websites which host a large number of snippets that you can add to your site:', 'code-snippets' ) .
				'<ul>' . $snippet_host_list . '</ul>' .
				__( 'More places to find snippets, as well as a selection of example snippets, can be found in the <a href="https://github.com/sheabunge/code-snippets/wiki/Finding-snippets">plugin documentation</a>.', 'code-snippets' ) . '</p>',
		) );

		$this->screen->add_help_tab( array(
			'id'      => 'adding',
			'title'   => __( 'Adding Snippets', 'code-snippets' ),
			'content' =>
				'<p>' . __( 'You need to fill out the name and code fields for your snippet to be added. While the description field will add more information about how your snippet works, what is does and where you found it, it is completely optional.', 'code-snippets' ) . '</p>' .
				'<p>' . __( 'Please be sure to check that your snippet is valid PHP code and will not produce errors before adding it through this page. While doing so will not become active straight away, it will help to minimise the chance of a faulty snippet becoming active on your site.', 'code-snippets' ) . '</p>',
		) );
	}

	/**
	 * Register and handle the help tabs for the import snippets admin page
	 */
	private function load_import_help() {
		$manage_url = code_snippets()->get_menu_url( 'manage' );

		$this->screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview', 'code-snippets' ),
			'content' => '<p>' . $this->get_intro_text() .
			             __( ' Here you can load snippets from a code snippets export file into the database alongside existing snippets.', 'code-snippets' ) . '</p>',
		) );

		$this->screen->add_help_tab( array(
			'id'      => 'import',
			'title'   => __( 'Importing', 'code-snippets' ),
			'content' =>
				'<p>' . __( 'You can load your snippets from a code snippets export file using this page.', 'code-snippets' ) .
				/* translators: %s: URL to Snippets admin menu */
				sprintf( __( 'Imported snippets will be added to the database along with your existing snippets. Regardless of whether the snippets were active on the previous site, imported snippets are always inactive until activated using the <a href="%s">Manage Snippets</a> page.', 'code-snippets' ), $manage_url ) . '</p>',
		) );

		$this->screen->add_help_tab( array(
			'id'      => 'export',
			'title'   => __( 'Exporting', 'code-snippets' ),
			/* translators: %s: URL to Manage Snippets admin menu */
			'content' => '<p>' . sprintf( __( 'You can save your snippets to a code snippets export file using the <a href="%s">Manage Snippets</a> page.', 'code-snippets' ), $manage_url ) . '</p>',
		) );
	}
}
