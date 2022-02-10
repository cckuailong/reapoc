<?php defined('ABSPATH') || die;

$sections = [
    trailingslashit(__DIR__).'faq/add-schema-values.php',
    trailingslashit(__DIR__).'faq/add-review-pagination.php',
    trailingslashit(__DIR__).'faq/change-review-title-tag.php',
    trailingslashit(__DIR__).'faq/change-font.php',
    trailingslashit(__DIR__).'faq/change-response-name.php',
    trailingslashit(__DIR__).'faq/change-review-field-order.php',
    trailingslashit(__DIR__).'faq/change-summary-field-order.php',
    trailingslashit(__DIR__).'faq/change-form-field-order.php',
    trailingslashit(__DIR__).'faq/change-pagination-query.php',
    trailingslashit(__DIR__).'faq/translate-text.php',
    trailingslashit(__DIR__).'faq/create-review.php',
    trailingslashit(__DIR__).'faq/customise-stars.php',
    trailingslashit(__DIR__).'faq/hide-form-after-submission.php',
    trailingslashit(__DIR__).'faq/limit-review-length.php',
    trailingslashit(__DIR__).'faq/query-by-rank.php',
    trailingslashit(__DIR__).'faq/dont-store-ipaddress.php',
    trailingslashit(__DIR__).'faq/redirect-after-submission.php',
    trailingslashit(__DIR__).'faq/remove-name-dash.php',
    trailingslashit(__DIR__).'faq/remove-nonce-check.php',
    trailingslashit(__DIR__).'faq/replace-avatar-with-featured-image.php',
    trailingslashit(__DIR__).'faq/notify-author-when-responding.php',
    trailingslashit(__DIR__).'faq/plugin-templates.php',
    trailingslashit(__DIR__).'faq/ipaddress-incorrectly-detected.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}

