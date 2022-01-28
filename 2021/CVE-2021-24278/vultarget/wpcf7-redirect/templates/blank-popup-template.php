<?php
/**
 * Popup template
 */

defined( 'ABSPATH' ) || exit;

$file_name = str_replace( 'php', 'css', basename( $template ) );
if ( file_exists( WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_PATH . $file_name ) ) {
	$css_file_url = WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_URL . $file_name;
} elseif ( file_exists( get_stylesheet_directory() . $file_name ) ) {
	$css_file_url = get_stylesheet_directory_uri() . $file_name;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link id="popup-template-css" rel="stylesheet" href="<?php echo $css_file_url; ?>">
	<title></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'popup-template' ); ?>>

	<div class="modal fade is-open" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
		<?php echo $template_html; ?>
	</div>

	<?php wp_footer(); ?>
</body>
</html>

