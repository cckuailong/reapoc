// Load Jquery Date Picker in WP-Statistics Admin
wps_js.date_picker();

// Run Meta Box [Overview Or Dashboard]
if (wps_js.global.page.file === "index.php" || wps_js.is_active('overview_page') || wps_js.global.page.file === "post-new.php" || (wps_js.global.page.file === "post.php" && wps_js.isset(wps_js.global, 'page', 'ID'))) {
    wps_js.run_meta_boxes();
}
