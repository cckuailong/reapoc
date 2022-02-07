<?php
/**
 * Pagination - Show numbered pagination.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( $pages <= 1 ) {
	return;
}
?>
<nav class="download-monitor-pagination">
	<?php
	echo paginate_links( apply_filters( 'download_monitor_pagination_args', array(
		'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
		'format'    => '',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $pages,
		'prev_text' => '&larr;',
		'next_text' => '&rarr;',
		'type'      => 'list',
		'end_size'  => 3,
		'mid_size'  => 3
	) ) );
	?>
</nav>