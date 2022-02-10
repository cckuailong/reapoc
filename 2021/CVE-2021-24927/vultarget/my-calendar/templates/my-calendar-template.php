<?php
/**
 * My Calendar embed template.
 *
 * @category Templates
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title><?php the_title(); ?></title>
<?php wp_head(); ?>
</head>
<body>
<?php
	the_content();
	wp_footer();
?>
</body>
</html>
