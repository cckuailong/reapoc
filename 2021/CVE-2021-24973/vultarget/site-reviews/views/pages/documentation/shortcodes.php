<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'shortcodes/site_reviews.php',
    trailingslashit(__DIR__).'shortcodes/site_reviews_summary.php',
    trailingslashit(__DIR__).'shortcodes/site_reviews_form.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}
