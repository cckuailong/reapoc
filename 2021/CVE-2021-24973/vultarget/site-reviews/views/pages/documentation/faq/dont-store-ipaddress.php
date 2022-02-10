<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-dont-store-ipaddress">
            <span class="title">How do I prevent the IP address from being saved with the review?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-dont-store-ipaddress" class="inside">
        <pre><code class="language-php">/**
 * Prevents the IP address from being saved with the review
 * Paste this code in your theme's functions.php file.
 * @param array $values
 * @return array
 */
add_filter('site-reviews/create/review-values', function ($values) {
    $values['ip_address'] = '';
    return $values;
});</code></pre>
    </div>
</div>
