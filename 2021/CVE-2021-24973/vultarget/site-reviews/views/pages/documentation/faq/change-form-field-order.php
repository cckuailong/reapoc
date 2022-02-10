<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-form-field-order">
            <span class="title">How do I change the order of the review form fields?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-form-field-order" class="inside">
        <p>To customise the order of the fields in the review form, use the <code><a data-expand="#hooks-filter-form-field-order" href="<?= glsr_admin_url('documentation', 'hooks'); ?>">site-reviews/review-form/order</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
        <pre><code class="language-php">/**
 * Customises the order of the fields used in the Site Reviews review form.
 * Paste this in your active theme's functions.php file.
 * @param array $order
 * @return array
 */
add_filter('site-reviews/review-form/order', function ($order) {
    // The $order array contains the field keys returned below.
    // Simply change the order of the field keys to the desired field order.
    return [
        'rating',
        'title',
        'content',
        'name',
        'email',
        'terms',
    ];
});</code></pre>
        <p>If you have used the example above and the review-form fields are not working correctly, check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> for errors.</p>
    </div>
</div>
