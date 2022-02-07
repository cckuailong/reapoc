<?php

/**
 * Gets the name of the default template
 * @return string
 */
function dlm_get_default_download_template() {
	$default = get_option( 'dlm_default_template' );

	if ( $default == 'custom' ) {
		$default = get_option( 'dlm_custom_template' );
	}

	return $default;
}
