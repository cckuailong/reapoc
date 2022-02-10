<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-limit-review-length">
            <span class="title">How do I limit the submitted review length?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-limit-review-length" class="inside">
        <p>To limit the allowed length of submitted reviews, use the following filter hooks in your theme's <code>functions.php</code> file:</p>
        <pre><code class="language-php">/**
 * Set the "maxlength" HTML attribute to limit review length to 100 characters
 * Simply change the number to your desired length.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/forms/review-form', function ($config) {
    if (array_key_exists('content', $config)) {
        $config['content']['maxlength'] = 100;
    }
    return $config;
});

/**
 * Limit review length to 100 characters in the form validation
 * Simply change the number to your desired length.
 * Paste this in your active theme's functions.php file.
 * @param array $rules
 * @return array
 */
add_filter('site-reviews/validation/rules', function ($rules) {
    $rules['content'] = 'required|max:100';
    return $rules;
});</code></pre>
    </div>
</div>
