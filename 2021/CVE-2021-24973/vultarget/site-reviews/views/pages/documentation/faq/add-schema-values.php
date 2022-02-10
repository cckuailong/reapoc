<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-add-schema-values">
            <span class="title">How do I add additional values to the schema?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-add-schema-values" class="inside">
        <p>To add additional values to the generated schema, use the <code><a data-expand="#hooks-filter-schema" href="<?= glsr_admin_url('documentation', 'hooks'); ?>">site-reviews/schema/&lt;schema_type&gt;</a></code> hook in your theme's functions.php file.</p>
        <p>Make sure to use Google's <a href="https://search.google.com/test/rich-results">Rich Results</a> tool to test the schema after any custom modifications have been made.</p>
        <pre><code class="language-php">/**
 * Modifies the schema created by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param array $schema
 * @return array
 */
add_filter('site-reviews/schema/LocalBusiness', function ($schema) {
    $schema['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => '123 Main St',
        'addressLocality' => 'City',
        'addressRegion' => 'State',
        'postalCode' => 'Zip',
    ];
    return $schema;
});</code></pre>
    </div>
</div>
