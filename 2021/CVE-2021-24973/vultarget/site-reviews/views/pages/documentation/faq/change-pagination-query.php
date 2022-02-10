<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-pagination-query">
            <span class="title">How do I change the pagination query string?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-pagination-query" class="inside">
        <p>The pagination query string can be seen in the address bar of the browser when you go to the next or previous page of reviews (i.e. <code>https://website.com/reviews/?reviews-page=2</code>).</p>
        <pre><code class="language-php">/**
 * Modifies the pagination query string used by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @return string
 */
add_filter('site-reviews/const/PAGED_QUERY_VAR', function () {
    // change this to your preferred query string
    return 'reviews-page';
});</code></pre>
    </div>
</div>
