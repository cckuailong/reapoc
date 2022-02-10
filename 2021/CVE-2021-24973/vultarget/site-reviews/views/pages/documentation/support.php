<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'support/basic-troubleshooting.php',
    trailingslashit(__DIR__).'support/common-problems-and-solutions.php',
    trailingslashit(__DIR__).'support/compatibility-issues.php',
    trailingslashit(__DIR__).'support/contact-support.php',
    trailingslashit(__DIR__).'support/upgrade-guide.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}
