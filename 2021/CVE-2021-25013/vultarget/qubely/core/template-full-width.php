<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Qubely Template Full Width
 *
 * @package qubely
 * @since v.1.1.0
 *
 */
get_header();
while ( have_posts() ) : the_post();
	the_content();
endwhile; // End of the loop.

get_footer();