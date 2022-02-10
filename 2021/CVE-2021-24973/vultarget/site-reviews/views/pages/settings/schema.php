<?php defined('ABSPATH') || die; ?>

<h2 class="title"><?= _x('JSON-LD Schema Settings', 'admin-text', 'site-reviews'); ?></h2>

<p>
    <?= _x('The (JSON-LD) schema is disabled by default. To enable it, use the schema option in your blocks or shortcodes.', 'admin-text', 'site-reviews'); ?>
    <?= sprintf(_x('The schema is used to display rich review snippets in Google\'s search results. If the schema has been enabled, you can use the %s tool to test your pages for valid schema.', 'admin-text', 'site-reviews'),
        sprintf('<a href="https://search.google.com/test/rich-results" target="_blank">%s</a>', _x('Google Rich Results', 'admin-text', 'site-reviews'))
    );
?></p>
<p><?php
    printf(_x('In some cases it may be useful to link the Site Reviews schema with other schema on your page, this is done by adding the %s unique identifier property to each schema that you wish to link. If you are using Woocommerce and have set the Schema Type to "Product", Site Reviews will automatically do this for you. In all other cases, either use the <code>schema_identifier</code> Custom Field name in the Custom Fields metabox, or use the hook provided on the Help page.', 'admin-text', 'site-reviews'),
        '<code><a href="https://rich-snippets.io/how-to-build-complex-structured-data/#b-reference-by-id" target="_blank">@id</a></code>'
    );
?></p>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
