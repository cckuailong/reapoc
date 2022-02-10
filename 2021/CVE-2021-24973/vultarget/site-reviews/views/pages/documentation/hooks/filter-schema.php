<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-schema">
            <span class="title">Modify the schema</span>
            <span class="badge code">site-reviews/schema/&lt;schema_type&gt;</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-schema" class="inside">
        <p>Use this hook if you would like to modify the properties of the schema type.</p>
        <p>This hook is specific to the schema type. For example, to modify the schema properties for the LocalBusiness schema type you would use the <code>site-reviews/schema/LocalBusiness</code> hook, but to modify the schema properties for the Product schema type you would use the <code>site-reviews/schema/Product</code> hook.</p>
        <p>See the <code><a data-expand="#faq-add-schema-values" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a></code> for a detailed example of how to use this hook.</p>
        <p>Make sure to use Google's <a href="https://search.google.com/test/rich-results">Rich Results</a> tool to test the schema after any custom modifications have been made.</p>
        <pre><code class="language-php">/**
 * Modifies the properties of the schema created by Site Reviews.
 * Change "LocalBusiness" to the schema type you wish to change (i.e. Product)
 * Paste this in your active theme's functions.php file.
 * @param array $schema
 * @return array
 */
add_filter('site-reviews/schema/LocalBusiness', function ($schema) {
    // modify the $schema array here.
    return $schema;
});</code></pre>
    </div>
</div>
