<?php

class Code_Snippets_Shortcode {

	function __construct() {
		add_shortcode( 'code_snippet', array( $this, 'render_shortcode' ) );
		add_action( 'the_posts', array( $this, 'enqueue_assets' ) );
	}

	function enqueue_assets( $posts ) {

		if ( empty( $posts ) || code_snippets_get_setting( 'general', 'disable_prism' ) ) {
			return $posts;
		}

		$found = false;

		foreach ( $posts as $post ) {

			if ( false !== stripos( $post->post_content, '[code_snippet' ) ) {
				$found = true;
				break;
			}
		}

		if ( ! $found ) {
			return $posts;
		}

		$plugin = code_snippets();

		wp_enqueue_style(
			'code-snippets-front-end',
			plugins_url( 'css/min/front-end.css', $plugin->file ),
			array(), $plugin->version
		);

		wp_enqueue_script(
			'code-snippets-front-end',
			plugins_url( 'js/min/front-end.js', $plugin->file ),
			array(), $plugin->version, true
		);

		return $posts;
	}

	function render_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'id'      => 0,
				'network' => false,
			),
			$atts, 'code_snippet'
		);

		$id = intval( $atts['id'] );
		if ( ! $id ) {
			return '';
		}

		$network = $atts['network'] ? true : false;
		$snippet = get_snippet( $id, $network );

		if ( ! trim( $snippet->code ) ) {
			return '';
		}

		return '<pre><code class="language-php">' . esc_html( $snippet->code ) . '</code></pre>';
	}
}

