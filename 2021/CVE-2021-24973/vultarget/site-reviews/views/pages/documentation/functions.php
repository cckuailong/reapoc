<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'functions/readme.php',
    trailingslashit(__DIR__).'functions/glsr_create_review.php',
    trailingslashit(__DIR__).'functions/glsr_debug.php',
    trailingslashit(__DIR__).'functions/glsr_star_rating.php',
    trailingslashit(__DIR__).'functions/glsr_get_option.php',
    trailingslashit(__DIR__).'functions/glsr_get_review.php',
    trailingslashit(__DIR__).'functions/glsr_get_options.php',
    trailingslashit(__DIR__).'functions/glsr_get_reviews.php',
    trailingslashit(__DIR__).'functions/glsr_get_ratings.php',
    trailingslashit(__DIR__).'functions/glsr_trace.php',
    trailingslashit(__DIR__).'functions/glsr_log.php',
    trailingslashit(__DIR__).'functions/glsr_update_review.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}
