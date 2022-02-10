<?php

function link_library_generate_rss_preview( $my_link_library_plugin ) {
$linkid = intval( $_GET['linkid'] );
$itemcount = intval( $_GET['previewcount'] );

$link_rss = get_post_meta( $linkid, 'link_rss', true );
$link_url = get_post_meta( $linkid, 'link_url', true );
$link = get_post( $linkid );

$genoptions = get_option('LinkLibraryGeneral');

include_once(ABSPATH . WPINC . '/feed.php');

if ( !empty( $link_rss ) && !empty( $link ) ) {
	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed( $link_rss );
	if ( !is_wp_error( $rss ) ) { // Checks that the object is created correctly
		// Figure out how many total items there are, but limit it to 5.
		$maxitems = $rss->get_item_quantity( $itemcount );

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items( 0, $maxitems );

	}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ( empty( $_GET['feed'] ) ) ? 'RSS_PHP' : 'RSS_PHP: ' . $link->post_title; ?></title>

		<!-- META HTTP-EQUIV -->
		<meta http-equiv="content-type" content="text/html; charset=UTF-8; ?>" />
		<meta http-equiv="imagetoolbar" content="false" />

		<?php if ( isset( $genoptions['stylesheet'] ) && !empty( $genoptions['stylesheet'] ) ) { ?>
			<style id='LinkLibraryStyle' type='text/css'>
				<?php echo stripslashes( $genoptions['fullstylesheet'] ); ?>
			</style>
		<?php } ?>

	</head>

	<body>
	<div id="ll_rss_preview_results">
		<?php if ( $rss_items ) { ?>
			<?php foreach($rss_items as $item): ?>
				<div class="ll_rss_preview_title" style="padding:0 5px 5px;">
					<h1><a target="feedwindow" href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a><div class='ll_rss_preview_date'><?php echo $item->get_date('j F Y | g:i a'); ?></div></h1>
					<div class='ll_rss_preview_content'><?php echo $item->get_description(); ?></div>
				</div>
				<br />
			<?php
			endforeach;
			?>
			<br />
			<div>
				<a class="ll_rss_preview_button" target="feedwindow" href="<?php echo $link_rss; ?>"><span>More News from this Feed</span></a> <a class="ll_rss_preview_button" target="sitewindow" href="<?php echo $link_url; ?>"><span>See Full Web Site</span></a>
			</div>
			<br />
			<br />
		<?php } ?>
	</div>
	</body>
	</html>

	<?php
	exit;
	}
}
