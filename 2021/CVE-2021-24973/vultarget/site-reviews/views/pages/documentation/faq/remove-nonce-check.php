<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-remove-nonce-check">
            <span class="title">How do I remove the WordPress Nonce check for logged in users?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-remove-nonce-check" class="inside">
        <p>A <a href="https://www.bynicolas.com/code/wordpress-nonce/" target="_blank">WordPress Nonce</a> is a special token that expires after 12 hours. Nonces prevent malicious form submissions and is an additional security measure used in WordPress forms for logged in users.</p>
        <p>Nonces can be problematic when your pages are cached, and for this reason it's commonly suggested to not cache pages for logged in users. However, if caching is required on your site for logged in users, then this snippet will remove the Nonce check when a user submits a review.</p>
        <pre><code class="language-php">/**
 * This removes the nonce check for logged-in users when submitting a review.
 * @see http://developer.wordpress.org/plugins/security/nonces/
 */
add_filter('site-reviews/router/unguarded-actions', function ($actions) {
    $actions[] = 'submit-review';
    return $actions;
});</code></pre>
    </div>
</div>
