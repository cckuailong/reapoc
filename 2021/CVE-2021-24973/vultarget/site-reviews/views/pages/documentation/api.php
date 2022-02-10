<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'api/authentication.php',
    trailingslashit(__DIR__).'api/categories.php',
    trailingslashit(__DIR__).'api/reviews.php',
    trailingslashit(__DIR__).'api/summary.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}

