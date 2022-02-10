<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function link_library_ajax_image_generator ( $my_link_library_plugin_admin ) {

    check_ajax_referer( 'link_library_generate_image' );

    $generaloptions = get_option( 'LinkLibraryGeneral' );
    $generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
    extract( $generaloptions );

    $name = $_POST['name'];
    $url = $_POST['url'];
    $mode = $_POST['mode'];
    $cid = $_POST['cid'];
    $filepath = $_POST['filepath'];
    $filepathtype = $_POST['filepathtype'];
    $linkid = intval($_POST['linkid']);

    if ( in_array( $generaloptions['thumbnailgenerator'], array( 'robothumb', 'thumbshots', 'google', 'wordpressmshots' ) ) ) {
	    echo $my_link_library_plugin_admin->ll_get_link_image($url, $name, $mode, $linkid, $cid, $filepath, $filepathtype, $generaloptions['thumbnailsize'], $generaloptions['thumbnailgenerator'] );
    } elseif ( 'pagepeeker' == $generaloptions['thumbnailgenerator'] ) {
	    echo $my_link_library_plugin_admin->ll_get_link_image($url, $name, $mode, $linkid, $generaloptions['pagepeekerid'], $filepath, $filepathtype, $generaloptions['pagepeekersize'], $generaloptions['thumbnailgenerator'] );
    } elseif ( 'shrinktheweb' == $generaloptions['thumbnailgenerator'] ) {
	    echo $my_link_library_plugin_admin->ll_get_link_image($url, $name, $mode, $linkid, $generaloptions['shrinkthewebaccesskey'], $filepath, $filepathtype, $generaloptions['stwthumbnailsize'], $generaloptions['thumbnailgenerator'] );
    }

    exit;
}
