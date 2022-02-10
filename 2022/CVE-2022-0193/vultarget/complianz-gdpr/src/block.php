<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * production: npn run build
 * dev: npm start
 * translation: wp i18n make-pot . config/languages/complianz.pot --exclude="pro/assets, core/assets"
 *
 * */

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'cmplz_editor_assets' );
function cmplz_editor_assets() { // phpcs:ignore
	// Scripts.
	wp_enqueue_script(
		'cmplz-block', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-api' ), // Dependencies, defined above.
        filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		true // Enqueue the script in the footer.
	);

    wp_localize_script(
        'cmplz-block',
        'complianz',
        array(
            'site_url' => get_rest_url(),
            //'query_preview' => plugins_url( 'img/wp-query-preview.jpg', __FILE__ ),
            'cmplz_preview' => cmplz_url.  'assets/images/gutenberg-preview.png',
        )
    );

    //https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
    //wp package install git@github.com:wp-cli/i18n-command.git
    //wp i18n make-pot . config/languages/complianz-json.pot --include="src"
    //wp i18n make-json . config/languages/complianz-json.pot config/languages/
    wp_set_script_translations( 'cmplz-block', 'complianz-gdpr' , cmplz_path . '/languages');

	// Styles.
	$load_css = cmplz_get_value('use_document_css');
	if ($load_css) {
		wp_enqueue_style(
			'cmplz-block', // Handle.
			cmplz_url . "assets/css/document.min.css",
			array( 'wp-edit-blocks' ), cmplz_version
		);
	}
}


/**
 * Handles the front end rendering of the complianz block
 *
 * @param $attributes
 * @param $content
 * @return string
 */
function cmplz_render_document_block($attributes, $content)
{
    $html = '';
    if (isset($attributes['selectedDocument'])) {
        if (isset($attributes['documentSyncStatus']) && $attributes['documentSyncStatus']==='unlink' && isset($attributes['customDocument'])){
            $html = $attributes['customDocument'];
        } else {
        	$type = $attributes['selectedDocument'];
	        $region = cmplz_get_region_from_legacy_type($type);
	        if ($region){
	        	$type = str_replace('-'.$region, '', $type);
	        }
            $html = COMPLIANZ::$document->get_document_html($type, $region);
        }
    }

    return $html;
}

register_block_type('complianz/document', array(
    'render_callback' => 'cmplz_render_document_block',
));

