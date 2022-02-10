<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-polyfill">
            <span class="title">Disable the polyfill.io script</span>
            <span class="badge code">site-reviews/assets/polyfill</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-polyfill" class="inside">
        <p>Use this hook if you want to disable the polyfill.io script from loading on your website.</p>
        <p><span class="required">Important:</span> The polyfill.io script provides support for Internet Explorer versions 10-11. If you disable it, Site Reviews will no longer work in those browsers.</p>
        <pre><code class="language-php">/**
 * Disables the polyfill.io script in Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/assets/polyfill', '__return_false');</code></pre>
    </div>
</div>
