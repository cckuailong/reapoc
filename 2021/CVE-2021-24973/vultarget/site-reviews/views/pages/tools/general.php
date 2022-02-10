<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'general/export-plugin-settings.php',
    trailingslashit(__DIR__).'general/import-plugin-settings.php',
    trailingslashit(__DIR__).'general/import-reviews.php',
    trailingslashit(__DIR__).'general/migrate-plugin.php',
    trailingslashit(__DIR__).'general/optimise-db-tables.php',
    trailingslashit(__DIR__).'general/repair-review-relations.php',
    trailingslashit(__DIR__).'general/reset-assigned-meta.php',
    trailingslashit(__DIR__).'general/reset-permissions.php',
    trailingslashit(__DIR__).'general/test-ip-detection.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('tools/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}
